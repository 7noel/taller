<?php

namespace App\Services;

use App\Models\InventoryGuide;
use App\Models\Part;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

/**
 * Emisión de guías de inventario con identidad documental:
 *  - U2 / NIA1 (ingreso), U3 / NSA1 (salida), U4 / NTA1 (transferencia).
 *  - El ajuste de stock reutiliza NIA1/NSA1 con motivo 28.
 */
class InventoryGuideService
{
    protected StockService $stockService;

    public function __construct(StockService $stockService)
    {
        $this->stockService = $stockService;
    }

    public function createInput(array $data): InventoryGuide
    {
        return $this->createGuide('U2', $data, 'entry');
    }

    public function createOutput(array $data): InventoryGuide
    {
        return $this->createGuide('U3', $data, 'exit');
    }

    public function createTransfer(array $data): InventoryGuide
    {
        return $this->createGuide('U4', $data, 'transfer');
    }

    /**
     * Ajuste de stock (motivo 28): si el total es positivo → NIA1, si es
     * negativo → NSA1. Cada línea decide entrada o salida según su signo.
     */
    public function createAdjustment(array $data): InventoryGuide
    {
        $data['movement_reason_code'] = '28';
        $total = array_sum(array_map(fn ($i) => (float) ($i['quantity'] ?? 0), $data['items'] ?? []));

        return $this->createGuide($total >= 0 ? 'U2' : 'U3', $data, 'adjustment');
    }

    protected function createGuide(string $typeCode, array $data, string $operation): InventoryGuide
    {
        $establishmentId = (int) ($data['establishment_id'] ?? Auth::user()?->establishment_id);
        $prefix = InventoryGuide::TYPE_CODES[$typeCode]['serie'];

        return DB::transaction(function () use ($typeCode, $data, $establishmentId, $prefix, $operation) {
            $numbers = app(DocumentSeriesService::class)->getNextNumber($establishmentId, $typeCode, $prefix);

            $guide = InventoryGuide::create([
                'establishment_id' => $establishmentId,
                'document_series_id' => $numbers['series']->id,
                'document_type_code' => $typeCode,
                'document_serie' => $numbers['series']->prefix_serie,
                'document_number' => $numbers['number'],
                'document_sn' => $numbers['sn'],
                'movement_reason_code' => $data['movement_reason_code'] ?? null,
                'origin_warehouse_id' => $data['origin_warehouse_id'] ?? null,
                'destination_warehouse_id' => $data['destination_warehouse_id'] ?? null,
                'provider_id' => $data['provider_id'] ?? null,
                'work_order_id' => $data['work_order_id'] ?? null,
                'purchase_order_id' => $data['purchase_order_id'] ?? null,
                'provider_invoice' => $data['provider_invoice'] ?? null,
                'provider_guide' => $data['provider_guide'] ?? null,
                'movement_date' => $data['movement_date'] ?? now()->toDateString(),
                'status' => InventoryGuide::STATUS_POSTED,
                'notes' => $data['notes'] ?? null,
                'created_by' => Auth::id(),
                'updated_by' => Auth::id(),
            ]);

            foreach ($data['items'] ?? [] as $item) {
                $partId = (int) ($item['part_id'] ?? 0);
                $quantity = (float) ($item['quantity'] ?? 0);

                if (! $partId || $quantity == 0) {
                    continue;
                }

                if ($operation === 'transfer') {
                    $this->stockService->transfer(
                        partId: $partId,
                        fromWarehouseId: (int) $data['origin_warehouse_id'],
                        toWarehouseId: (int) $data['destination_warehouse_id'],
                        quantity: abs($quantity),
                        movementReasonCode: $data['movement_reason_code'] ?? null,
                        inventoryGuideId: $guide->id,
                        workOrderId: $data['work_order_id'] ?? null,
                        notes: $data['notes'] ?? null,
                    );
                    continue;
                }

                $isEntry = $operation === 'entry' || ($operation === 'adjustment' && $quantity > 0);
                $part = Part::find($partId);
                $unitCost = (float) ($item['unit_cost'] ?? $part?->cost_price ?? 0);

                $this->stockService->registerMovement(
                    partId: $partId,
                    warehouseId: (int) ($isEntry
                        ? ($data['destination_warehouse_id'] ?? $data['warehouse_id'] ?? 0)
                        : ($data['origin_warehouse_id'] ?? $data['warehouse_id'] ?? 0)),
                    type: $isEntry ? 'entry' : 'exit',
                    quantity: abs($quantity),
                    unitCost: $unitCost,
                    currency: $data['currency'] ?? 'PEN',
                    exchangeRate: $data['exchange_rate'] ?? null,
                    documentType: 'inventory_guide',
                    documentId: $guide->id,
                    reference: $guide->document_sn,
                    movementReasonCode: $data['movement_reason_code'] ?? null,
                    inventoryGuideId: $guide->id,
                    purchaseOrderId: $data['purchase_order_id'] ?? null,
                    workOrderId: $data['work_order_id'] ?? null,
                    notes: $data['notes'] ?? null,
                );
            }

            return $guide->fresh([
                'movementReason', 'originWarehouse', 'destinationWarehouse',
                'provider', 'workOrder', 'purchaseOrder', 'movements.part', 'movements.warehouse',
            ]);
        });
    }
}
