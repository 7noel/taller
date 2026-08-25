<?php

namespace App\Services;

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

        unset($data['initial_quantity'], $data['initial_warehouse_id']);

        $data['created_by'] = Auth::id();
        $data['updated_by'] = Auth::id();

        return DB::transaction(function () use ($data, $initialQuantity, $initialWarehouseId) {
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

            return $part;
        });
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