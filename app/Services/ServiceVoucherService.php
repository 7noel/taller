<?php

namespace App\Services;

use App\Models\CompanySetting;
use App\Models\ServiceVoucher;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use RuntimeException;

class ServiceVoucherService
{
    /**
     * Emite un comprobante de servicio tercerizado (CST01).
     *
     * El usuario registra montos SIN IGV (agreed_amount / discount_applied);
     * el sistema calcula base, IGV, total con IGV, detracción y total a pagar.
     */
    public function create(array $data): ServiceVoucher
    {
        return DB::transaction(function () use ($data) {
            $settings = CompanySetting::get();
            $data['igv_rate'] = (float) ($data['igv_rate'] ?? $settings?->igv_rate ?? 0.18);
            $data['detraction_rate'] = (float) ($data['detraction_rate'] ?? $settings?->detraccion_rate ?? 0.12);

            $establishmentId = (int) ($data['establishment_id'] ?? Auth::user()?->establishment_id);
            $numbers = app(DocumentSeriesService::class)->getNextNumber($establishmentId, 'CST', 'CST01');

            $data = array_merge($data, [
                'establishment_id' => $establishmentId,
                'document_series_id' => $numbers['series']->id,
                'document_type_code' => $numbers['document_type_code'] ?? 'CST',
                'document_serie' => $numbers['series']->prefix_serie,
                'document_number' => $numbers['number'],
                'document_sn' => $numbers['sn'],
                'status' => ServiceVoucher::STATUS_PENDING,
                'created_by' => Auth::id(),
                'updated_by' => Auth::id(),
            ]);

            $voucher = ServiceVoucher::create($this->computeTotals($data));
            $voucher->recordStatusChange(ServiceVoucher::STATUS_PENDING, null, 'Comprobante de servicio tercerizado emitido.');

            return $voucher->fresh(['workOrder.vehicle', 'provider', 'settlement', 'documentSeries.documentType']);
        });
    }

    /**
     * Actualiza un comprobante (solo si no está liquidado) recalculando totales.
     */
    public function update(ServiceVoucher $voucher, array $data): ServiceVoucher
    {
        if ($voucher->status === ServiceVoucher::STATUS_LIQUIDATED) {
            throw new RuntimeException('No se puede editar un comprobante ya liquidado.');
        }

        return DB::transaction(function () use ($voucher, $data) {
            $data['igv_rate'] = (float) ($data['igv_rate'] ?? $voucher->igv_rate);
            $data['detraction_rate'] = (float) ($data['detraction_rate'] ?? $voucher->detraction_rate);
            $data['updated_by'] = Auth::id();

            $voucher->update($this->computeTotals($data));

            return $voucher->fresh(['workOrder.vehicle', 'provider', 'settlement', 'documentSeries.documentType']);
        });
    }

    /**
     * Marca el servicio como recibido conforme (pending → completed).
     */
    public function complete(ServiceVoucher $voucher): ServiceVoucher
    {
        if ($voucher->status !== ServiceVoucher::STATUS_PENDING) {
            throw new RuntimeException('Solo los comprobantes pendientes pueden marcarse como completados.');
        }

        DB::transaction(function () use ($voucher) {
            $voucher->update(['status' => ServiceVoucher::STATUS_COMPLETED, 'updated_by' => Auth::id()]);
            $voucher->recordStatusChange(ServiceVoucher::STATUS_COMPLETED, ServiceVoucher::STATUS_PENDING, 'Servicio recibido conforme.');
        });

        return $voucher->fresh();
    }

    /**
     * Elimina un comprobante (solo si no está liquidado).
     */
    public function delete(ServiceVoucher $voucher): void
    {
        if ($voucher->status === ServiceVoucher::STATUS_LIQUIDATED) {
            throw new RuntimeException('No se puede eliminar un comprobante liquidado.');
        }

        DB::transaction(function () use ($voucher) {
            $voucher->delete();
        });
    }

    /**
     * Cálculo de totales a partir de montos SIN IGV:
     *   base = agreed_amount − discount_applied
     *   igv  = base × igv_rate
     *   total_con_igv = base + igv
     *   detracción = total_con_igv × detraction_rate  (base = importe total con IGV)
     *   total_a_pagar = total_con_igv − detracción
     */
    protected function computeTotals(array $data): array
    {
        $agreed = (float) ($data['agreed_amount'] ?? 0);
        $discount = (float) ($data['discount_applied'] ?? 0);
        $base = round($agreed - $discount, 2);
        $igvRate = (float) ($data['igv_rate'] ?? 0.18);
        $detractionRate = (float) ($data['detraction_rate'] ?? 0.12);

        $igv = round($base * $igvRate, 2);
        $totalWithIgv = round($base + $igv, 2);
        $detraction = round($totalWithIgv * $detractionRate, 2);
        $totalPayable = round($totalWithIgv - $detraction, 2);

        return array_merge($data, [
            'base_amount' => $base,
            'igv_amount' => $igv,
            'total_with_igv' => $totalWithIgv,
            'detraction_amount' => $detraction,
            'total_payable' => $totalPayable,
        ]);
    }
}
