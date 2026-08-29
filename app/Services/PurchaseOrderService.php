<?php

namespace App\Services;

use App\Models\CompanySetting;
use App\Models\PurchaseOrder;
use App\Models\PurchaseOrderItem;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use RuntimeException;

class PurchaseOrderService
{
    protected StockService $stockService;
    protected InventoryGuideService $guideService;

    public function __construct(StockService $stockService, InventoryGuideService $guideService)
    {
        $this->stockService = $stockService;
        $this->guideService = $guideService;
    }

    public function create(array $data): PurchaseOrder
    {
        return DB::transaction(function () use ($data) {
            $establishmentId = (int) ($data['establishment_id'] ?? Auth::user()?->establishment_id);
            $numbers = app(DocumentSeriesService::class)->getNextNumber($establishmentId, 'OC', 'OC01');

            $data = array_merge($data, [
                'establishment_id' => $establishmentId,
                'document_series_id' => $numbers['series']->id,
                'document_type_code' => 'OC',
                'document_serie' => $numbers['series']->prefix_serie,
                'document_number' => $numbers['number'],
                'document_sn' => $numbers['sn'],
                'status' => PurchaseOrder::STATUS_DRAFT,
                'created_by' => Auth::id(),
                'updated_by' => Auth::id(),
            ]);

            $po = PurchaseOrder::create(array_merge($data, $this->computeTotals($data)));
            $this->syncItems($po, $data['items'] ?? []);

            return $po->fresh(['provider', 'warehouse', 'items.part', 'documentSeries']);
        });
    }

    public function update(PurchaseOrder $po, array $data): PurchaseOrder
    {
        if ($po->status === PurchaseOrder::STATUS_RECEIVED) {
            throw new RuntimeException('No se puede editar una orden de compra ya recibida.');
        }

        DB::transaction(function () use ($po, $data) {
            $data['updated_by'] = Auth::id();
            $po->update(array_merge($data, $this->computeTotals($data)));
            $this->syncItems($po, $data['items'] ?? []);
        });

        return $po->fresh(['provider', 'warehouse', 'items.part', 'documentSeries']);
    }

    /**
     * Recepción: emite la NIA1 (motivo 02 Compra nacional) y las entradas de
     * stock al almacén indicado. Guarda factura/guía del proveedor en la OC.
     */
    public function receive(PurchaseOrder $po, array $data): PurchaseOrder
    {
        if ($po->status === PurchaseOrder::STATUS_RECEIVED) {
            throw new RuntimeException('La orden de compra ya fue recibida.');
        }
        if ($po->status === PurchaseOrder::STATUS_CANCELLED) {
            throw new RuntimeException('No se puede recibir una orden de compra anulada.');
        }

        return DB::transaction(function () use ($po, $data) {
            $this->guideService->createInput([
                'establishment_id' => $po->establishment_id,
                'movement_reason_code' => '02',
                'destination_warehouse_id' => (int) ($data['warehouse_id'] ?? $po->warehouse_id),
                'provider_id' => $po->provider_id,
                'purchase_order_id' => $po->id,
                'provider_invoice' => $data['provider_invoice'] ?? null,
                'provider_guide' => $data['provider_guide'] ?? null,
                'movement_date' => $data['received_at'] ?? now()->toDateString(),
                'currency' => $po->currency,
                'exchange_rate' => $po->exchange_rate,
                'notes' => 'Recepción de '.($po->document_sn ?? 'OC'),
                'items' => $po->items->map(fn ($i) => [
                    'part_id' => $i->part_id,
                    'quantity' => $i->quantity,
                    'unit_cost' => $i->unit_cost,
                ])->all(),
            ]);

            $po->update([
                'status' => PurchaseOrder::STATUS_RECEIVED,
                'warehouse_id' => $data['warehouse_id'] ?? $po->warehouse_id,
                'provider_invoice' => $data['provider_invoice'] ?? null,
                'provider_guide' => $data['provider_guide'] ?? null,
                'received_at' => $data['received_at'] ?? now()->toDateString(),
                'updated_by' => Auth::id(),
            ]);

            return $po->fresh(['provider', 'warehouse', 'items.part', 'inventoryGuides', 'documentSeries']);
        });
    }

    public function cancel(PurchaseOrder $po): PurchaseOrder
    {
        if ($po->status === PurchaseOrder::STATUS_RECEIVED) {
            throw new RuntimeException('No se puede anular una orden de compra ya recibida.');
        }

        $po->update(['status' => PurchaseOrder::STATUS_CANCELLED, 'updated_by' => Auth::id()]);

        return $po->fresh();
    }

    public function delete(PurchaseOrder $po): bool
    {
        if ($po->status === PurchaseOrder::STATUS_RECEIVED) {
            throw new RuntimeException('No se puede eliminar una orden de compra recibida.');
        }

        return (bool) $po->delete();
    }

    protected function computeTotals(array $data): array
    {
        $subtotal = 0;
        foreach ($data['items'] ?? [] as $item) {
            $subtotal += round((float) ($item['quantity'] ?? 0) * (float) ($item['unit_cost'] ?? 0), 2);
        }

        $settings = CompanySetting::get();
        $iva = round($subtotal * (float) ($data['iva_rate'] ?? $settings?->igv_rate ?? 0.18), 2);

        return [
            'subtotal' => $subtotal,
            'iva' => $iva,
            'total' => round($subtotal + $iva, 2),
        ];
    }

    protected function syncItems(PurchaseOrder $po, array $items): void
    {
        $existing = $po->items()->get()->keyBy('id');

        foreach ($items as $item) {
            $partId = (int) ($item['part_id'] ?? 0);
            $quantity = (float) ($item['quantity'] ?? 0);

            if (! $partId || $quantity <= 0) {
                continue;
            }

            $payload = [
                'part_id' => $partId,
                'quantity' => $quantity,
                'unit_cost' => (float) ($item['unit_cost'] ?? 0),
                'total_cost' => round($quantity * (float) ($item['unit_cost'] ?? 0), 2),
                'uom' => $item['uom'] ?? null,
            ];

            $id = $item['id'] ?? null;
            if ($id && isset($existing[$id])) {
                $existing[$id]->update($payload);
                $existing->forget($id);
            } else {
                PurchaseOrderItem::create(array_merge(['purchase_order_id' => $po->id], $payload));
            }
        }

        foreach ($existing as $item) {
            $item->delete();
        }
    }
}
