<?php

namespace App\Services;

use App\Models\CompanySetting;
use App\Models\ProviderSettlement;
use App\Models\ServiceVoucher;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use RuntimeException;

class ProviderSettlementService
{
    /**
     * Crea una liquidación de servicios tercerizados (LST01) a partir de los
     * vales completados del proveedor indicados en voucher_ids.
     */
    public function create(array $data): ProviderSettlement
    {
        return DB::transaction(function () use ($data) {
            $settings = CompanySetting::get();
            $data['igv_rate'] = (float) ($data['igv_rate'] ?? $settings?->igv_rate ?? 0.18);
            $data['detraction_rate'] = (float) ($data['detraction_rate'] ?? $settings?->detraccion_rate ?? 0.12);

            $establishmentId = (int) ($data['establishment_id'] ?? Auth::user()?->establishment_id);
            $numbers = app(DocumentSeriesService::class)->getNextNumber($establishmentId, 'LST', 'LST01');

            $data = array_merge($data, [
                'establishment_id' => $establishmentId,
                'document_series_id' => $numbers['series']->id,
                'document_type_code' => $numbers['document_type_code'] ?? 'LST',
                'document_serie' => $numbers['series']->prefix_serie,
                'document_number' => $numbers['number'],
                'document_sn' => $numbers['sn'],
                'status' => ProviderSettlement::STATUS_DRAFT,
                'created_by' => Auth::id(),
                'updated_by' => Auth::id(),
            ]);

            $settlement = ProviderSettlement::create($this->computeTotals($data));
            $settlement->recordStatusChange(ProviderSettlement::STATUS_DRAFT, null, 'Liquidación creada.');

            if (! empty($data['voucher_ids'])) {
                $this->syncVouchers($settlement, $data['voucher_ids']);
            }

            return $settlement->fresh(['provider', 'documentSeries.documentType']);
        });
    }

    /**
     * Actualiza datos de la liquidación (solo en borrador) y recalcula totales.
     */
    public function update(ProviderSettlement $settlement, array $data): ProviderSettlement
    {
        if ($settlement->status !== ProviderSettlement::STATUS_DRAFT) {
            throw new RuntimeException('Solo las liquidaciones en borrador pueden editarse.');
        }

        return DB::transaction(function () use ($settlement, $data) {
            $data['igv_rate'] = (float) ($data['igv_rate'] ?? $settlement->igv_rate);
            $data['detraction_rate'] = (float) ($data['detraction_rate'] ?? $settlement->detraction_rate);
            $data['updated_by'] = Auth::id();

            $settlement->update($this->computeTotals($data, $settlement));

            if (array_key_exists('voucher_ids', $data)) {
                $this->syncVouchers($settlement, $data['voucher_ids'] ?? []);
            }

            return $settlement->fresh(['provider', 'vouchers', 'documentSeries.documentType']);
        });
    }

    /**
     * Sincroniza los vales de la liquidación con diff/upsert (regla 06):
     * solo vales completados del mismo proveedor y aún no liquidados.
     */
    public function syncVouchers(ProviderSettlement $settlement, array $voucherIds): ProviderSettlement
    {
        return DB::transaction(function () use ($settlement, $voucherIds) {
            $validIds = ServiceVoucher::query()
                ->where('provider_id', $settlement->provider_id)
                ->where('status', ServiceVoucher::STATUS_COMPLETED)
                ->where(function ($q) use ($settlement) {
                    $q->whereNull('provider_settlement_id')
                        ->orWhere('provider_settlement_id', $settlement->id);
                })
                ->whereIn('id', $voucherIds)
                ->pluck('id');

            // Desvincula los que ya no vienen.
            ServiceVoucher::query()
                ->where('provider_settlement_id', $settlement->id)
                ->whereNotIn('id', $validIds)
                ->update(['provider_settlement_id' => null, 'updated_by' => Auth::id()]);

            // Vincula los seleccionados.
            ServiceVoucher::query()
                ->whereIn('id', $validIds)
                ->update(['provider_settlement_id' => $settlement->id, 'updated_by' => Auth::id()]);

            $this->recalculateTotals($settlement);

            return $settlement->fresh(['provider', 'vouchers', 'documentSeries.documentType']);
        });
    }

    /**
     * Quita un vale de la liquidación (solo en borrador).
     */
    public function detachVoucher(ProviderSettlement $settlement, ServiceVoucher $voucher): ProviderSettlement
    {
        if ($settlement->status !== ProviderSettlement::STATUS_DRAFT) {
            throw new RuntimeException('Solo se pueden quitar vales de una liquidación en borrador.');
        }

        return DB::transaction(function () use ($settlement, $voucher) {
            $voucher->update(['provider_settlement_id' => null, 'updated_by' => Auth::id()]);
            $this->recalculateTotals($settlement);

            return $settlement->fresh(['provider', 'vouchers', 'documentSeries.documentType']);
        });
    }

    /**
     * Aprueba la liquidación (draft → approved).
     */
    public function approve(ProviderSettlement $settlement): ProviderSettlement
    {
        if ($settlement->status !== ProviderSettlement::STATUS_DRAFT) {
            throw new RuntimeException('Solo las liquidaciones en borrador pueden aprobarse.');
        }

        DB::transaction(function () use ($settlement) {
            $settlement->update([
                'status' => ProviderSettlement::STATUS_APPROVED,
                'approved_by' => Auth::id(),
                'approved_at' => now(),
                'updated_by' => Auth::id(),
            ]);
            $settlement->recordStatusChange(ProviderSettlement::STATUS_APPROVED, ProviderSettlement::STATUS_DRAFT, 'Liquidación aprobada.');
        });

        return $settlement->fresh(['provider', 'vouchers', 'documentSeries.documentType']);
    }

    /**
     * Paga la liquidación (approved → paid) y marca los vales como liquidados.
     */
    public function pay(ProviderSettlement $settlement): ProviderSettlement
    {
        if ($settlement->status !== ProviderSettlement::STATUS_APPROVED) {
            throw new RuntimeException('Solo las liquidaciones aprobadas pueden pagarse.');
        }

        DB::transaction(function () use ($settlement) {
            $settlement->update([
                'status' => ProviderSettlement::STATUS_PAID,
                'paid_by' => Auth::id(),
                'paid_at' => now(),
                'updated_by' => Auth::id(),
            ]);
            $settlement->recordStatusChange(ProviderSettlement::STATUS_PAID, ProviderSettlement::STATUS_APPROVED, 'Liquidación pagada.');

            ServiceVoucher::query()
                ->where('provider_settlement_id', $settlement->id)
                ->where('status', ServiceVoucher::STATUS_COMPLETED)
                ->get()
                ->each(function (ServiceVoucher $voucher) use ($settlement) {
                    $voucher->update(['status' => ServiceVoucher::STATUS_LIQUIDATED, 'updated_by' => Auth::id()]);
                    $voucher->recordStatusChange(
                        ServiceVoucher::STATUS_LIQUIDATED,
                        ServiceVoucher::STATUS_COMPLETED,
                        'Liquidado en ' . $settlement->document_sn
                    );
                });
        });

        return $settlement->fresh(['provider', 'vouchers', 'documentSeries.documentType']);
    }

    /**
     * Elimina la liquidación (solo en borrador) y desvincula sus vales.
     */
    public function delete(ProviderSettlement $settlement): void
    {
        if ($settlement->status !== ProviderSettlement::STATUS_DRAFT) {
            throw new RuntimeException('Solo se puede eliminar una liquidación en borrador.');
        }

        DB::transaction(function () use ($settlement) {
            ServiceVoucher::query()
                ->where('provider_settlement_id', $settlement->id)
                ->update(['provider_settlement_id' => null, 'updated_by' => Auth::id()]);

            $settlement->delete();
        });
    }

    /**
     * Recalcula los totales a partir de los vales ya vinculados.
     */
    protected function recalculateTotals(ProviderSettlement $settlement): void
    {
        $settlement->update($this->computeTotals([], $settlement));
    }

    /**
     * Cálculo de totales de la liquidación. Los vales guardan montos base SIN IGV;
     * sobre el total con IGV se calcula la detracción.
     */
    protected function computeTotals(array $data, ?ProviderSettlement $settlement = null): array
    {
        if ($settlement) {
            $subtotal = round((float) $settlement->vouchers()->sum('base_amount'), 2);
            $discount = (float) ($data['global_discount'] ?? $settlement->global_discount);
            $igvRate = (float) ($data['igv_rate'] ?? $settlement->igv_rate);
            $detractionRate = (float) ($data['detraction_rate'] ?? $settlement->detraction_rate);
        } else {
            $subtotal = round((float) ServiceVoucher::query()
                ->where('provider_id', $data['provider_id'])
                ->where('status', ServiceVoucher::STATUS_COMPLETED)
                ->whereNull('provider_settlement_id')
                ->whereIn('id', $data['voucher_ids'] ?? [])
                ->sum('base_amount'), 2);
            $discount = (float) ($data['global_discount'] ?? 0);
            $igvRate = (float) ($data['igv_rate'] ?? 0.18);
            $detractionRate = (float) ($data['detraction_rate'] ?? 0.12);
        }

        $base = round($subtotal - $discount, 2);
        $igv = round($base * $igvRate, 2);
        $totalWithIgv = round($base + $igv, 2);
        $detraction = round($totalWithIgv * $detractionRate, 2);
        $totalPayable = round($totalWithIgv - $detraction, 2);

        return array_merge($data, [
            'subtotal' => $subtotal,
            'base_amount' => $base,
            'igv_amount' => $igv,
            'total_with_igv' => $totalWithIgv,
            'detraction_amount' => $detraction,
            'total_payable' => $totalPayable,
        ]);
    }
}

