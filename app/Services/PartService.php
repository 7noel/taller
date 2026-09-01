<?php

namespace App\Services;

use App\Models\Estimate;
use App\Models\EstimateItem;
use App\Models\Part;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class PartService
{
    protected StockService $stockService;

    public function __construct(StockService $stockService)
    {
        $this->stockService = $stockService;
    }

    public function create(array $data): Part
    {
        $initialQuantity = (float) ($data['initial_quantity'] ?? 0);
        $initialWarehouseId = $data['initial_warehouse_id'] ?? null;
        $estimateItemIds = array_values(array_filter((array) ($data['estimate_item_ids'] ?? [])));

        unset($data['initial_quantity'], $data['initial_warehouse_id'], $data['estimate_item_ids']);

        $data['created_by'] = Auth::id();
        $data['updated_by'] = Auth::id();

        return DB::transaction(function () use ($data, $initialQuantity, $initialWarehouseId, $estimateItemIds) {
            $part = Part::create($data);

            // Inventario inicial: genera una entrada al almacén indicado.
            if ($initialQuantity > 0 && $initialWarehouseId) {
                $this->stockService->registerMovement(
                    partId: $part->id,
                    warehouseId: (int) $initialWarehouseId,
                    type: 'entry',
                    quantity: $initialQuantity,
                    unitCost: (float) $data['cost_price'],
                    currency: $data['cost_currency'] ?? 'PEN',
                    exchangeRate: null,
                    documentType: null,
                    documentId: null,
                    reference: 'Inventario inicial',
                );
            }

            // Vincula líneas libres de presupuestos (repuestos) a este catálogo.
            if (! empty($estimateItemIds)) {
                $this->linkEstimateItems($part, $estimateItemIds);
            }

            return $part;
        });
    }

    /**
     * Asigna part_id a las líneas libres seleccionadas (solo tipo repuesto y
     * sin vínculo previo) dentro de la misma transacción de creación del Part.
     * El snapshot de la línea (descripción/precios) no se modifica.
     */
    protected function linkEstimateItems(Part $part, array $estimateItemIds): void
    {
        $items = EstimateItem::query()
            ->whereKey($estimateItemIds)
            ->where('item_type', 'part')
            ->whereNull('part_id')
            ->get();

        if ($items->isEmpty()) {
            return;
        }

        EstimateItem::whereKey($items->pluck('id'))->update(['part_id' => $part->id]);

        foreach ($items->pluck('estimate_id')->unique() as $estimateId) {
            $estimate = Estimate::find($estimateId);

            if (! $estimate) {
                continue;
            }

            activity()
                ->performedOn($estimate)
                ->causedBy(Auth::id())
                ->withProperties(['part_id' => $part->id, 'part_sku' => $part->sku])
                ->log("Repuesto \"{$part->name}\" (SKU {$part->sku}) vinculado a líneas del presupuesto.");
        }
    }

    public function update(Part $part, array $data): Part
    {
        unset($data['initial_quantity'], $data['initial_warehouse_id']);

        $data['updated_by'] = Auth::id();
        $part->update($data);

        return $part;
    }

    public function delete(Part $part): bool
    {
        return $part->delete();
    }
}