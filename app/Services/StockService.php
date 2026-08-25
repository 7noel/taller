<?php

namespace App\Services;

use App\Models\StockMovement;
use App\Models\WarehouseStock;
use Illuminate\Support\Facades\DB;
use InvalidArgumentException;

class StockService
{
    /**
     * Registra un movimiento de stock y actualiza el stock (Costo Promedio Ponderado).
     *
     * Los costos en soles (unit_cost_pen / total_cost_pen) se usan como base del
     * kardex valorizado. Si la moneda es USD, se convierte con exchange_rate.
     */
    public function registerMovement(
        int $partId,
        int $warehouseId,
        string $type,
        float $quantity,
        float $unitCost,
        string $currency = 'PEN',
        ?float $exchangeRate = null,
        ?string $documentType = null,
        ?int $documentId = null,
        ?string $reference = null,
    ): StockMovement {
        if ($quantity <= 0) {
            throw new InvalidArgumentException('La cantidad debe ser mayor a 0.');
        }

        // Conversión a soles
        $exchangeRate = $currency === 'USD' ? ($exchangeRate ?? 1) : 1;
        $unitCostPen = round($unitCost * $exchangeRate, 2);
        $totalCostPen = round($unitCostPen * $quantity, 2);

        return DB::transaction(function () use (
            $partId, $warehouseId, $type, $quantity, $unitCost,
            $currency, $exchangeRate, $unitCostPen, $totalCostPen,
            $documentType, $documentId, $reference
        ) {
            // Bloquear la fila de stock para evitar condiciones de carrera
            $stock = WarehouseStock::where('part_id', $partId)
                ->where('warehouse_id', $warehouseId)
                ->lockForUpdate()
                ->first();

            $currentQuantity = $stock?->quantity ?? 0;
            $currentAvg = $stock?->average_cost ?? 0;

            // Para salidas y ajustes negativos, el costo unitario en soles es el promedio vigente
            if ($type === 'exit' || ($type === 'adjustment' && $quantity < 0)) {
                if ($currentQuantity < $quantity) {
                    throw new InvalidArgumentException('Stock insuficiente para la salida.');
                }
                $unitCostPen = $currentAvg;
                $totalCostPen = round($unitCostPen * $quantity, 2);
                $unitCost = $currency === 'USD' ? round($unitCostPen / $exchangeRate, 4) : $unitCostPen;
            }

            if ($type === 'entry') {
                // CPP: nuevo promedio ponderado
                $newQuantity = $currentQuantity + $quantity;
                $newAvg = $newQuantity > 0
                    ? round((($currentQuantity * $currentAvg) + ($quantity * $unitCostPen)) / $newQuantity, 2)
                    : 0;
            } elseif ($type === 'exit') {
                $newQuantity = $currentQuantity - $quantity;
                $newAvg = $currentAvg;
            } else {
                // adjustment: positivo = entra, negativo = sale
                $newQuantity = $currentQuantity + $quantity;
                if ($quantity > 0) {
                    $newAvg = $newQuantity > 0
                        ? round((($currentQuantity * $currentAvg) + ($quantity * $unitCostPen)) / $newQuantity, 2)
                        : 0;
                } else {
                    $newAvg = $currentAvg;
                }
            }

            $stock = WarehouseStock::updateOrCreate(
                ['part_id' => $partId, 'warehouse_id' => $warehouseId],
                ['quantity' => max($newQuantity, 0), 'average_cost' => $newAvg],
            );

            return StockMovement::create([
                'part_id' => $partId,
                'warehouse_id' => $warehouseId,
                'type' => $type,
                'quantity' => $quantity,
                'currency' => $currency,
                'exchange_rate' => $currency === 'USD' ? $exchangeRate : null,
                'unit_cost' => $unitCost,
                'total_cost' => round($unitCost * $quantity, 2),
                'unit_cost_pen' => $unitCostPen,
                'total_cost_pen' => $totalCostPen,
                'document_type' => $documentType,
                'document_id' => $documentId,
                'reference' => $reference,
                'created_by' => auth()->id(),
            ]);
        });
    }

    /**
     * Recalcula la cantidad y costo promedio de un stock sumando sus movimientos.
     */
    public function updateStock(int $partId, int $warehouseId): WarehouseStock
    {
        $movements = StockMovement::where('part_id', $partId)
            ->where('warehouse_id', $warehouseId)
            ->orderBy('created_at')
            ->orderBy('id')
            ->get();

        $quantity = 0.0;
        $avg = 0.0;

        foreach ($movements as $movement) {
            if ($movement->type === 'entry' || ($movement->type === 'adjustment' && $movement->quantity > 0)) {
                $newQuantity = $quantity + $movement->quantity;
                $avg = $newQuantity > 0
                    ? round((($quantity * $avg) + ($movement->quantity * $movement->unit_cost_pen)) / $newQuantity, 2)
                    : 0;
                $quantity = $newQuantity;
            } else {
                $quantity -= $movement->quantity; // exit y adjustment negativo
            }
        }

        return WarehouseStock::updateOrCreate(
            ['part_id' => $partId, 'warehouse_id' => $warehouseId],
            ['quantity' => max($quantity, 0), 'average_cost' => $avg],
        );
    }

    /**
     * Devuelve el kardex valorizado: saldo inicial, movimientos y saldo final.
     */
    public function getKardex(int $partId, int $warehouseId, ?string $fromDate = null, ?string $toDate = null): array
    {
        $base = StockMovement::where('part_id', $partId)
            ->where('warehouse_id', $warehouseId);

        // Saldo inicial: suma de movimientos anteriores a fromDate
        $openingQuery = (clone $base)->when($fromDate, fn ($q) => $q->where('created_at', '<', $fromDate . ' 00:00:00'));
        $openingMovements = $openingQuery->orderBy('created_at')->orderBy('id')->get();

        $quantity = 0.0;
        $avg = 0.0;
        foreach ($openingMovements as $movement) {
            if ($movement->type === 'entry' || ($movement->type === 'adjustment' && $movement->quantity > 0)) {
                $newQuantity = $quantity + $movement->quantity;
                $avg = $newQuantity > 0
                    ? round((($quantity * $avg) + ($movement->quantity * $movement->unit_cost_pen)) / $newQuantity, 2)
                    : 0;
                $quantity = $newQuantity;
            } else {
                $quantity -= $movement->quantity;
            }
        }

        $movements = (clone $base)
            ->when($fromDate, fn ($q) => $q->where('created_at', '>=', $fromDate . ' 00:00:00'))
            ->when($toDate, fn ($q) => $q->where('created_at', '<=', $toDate . ' 23:59:59'))
            ->orderBy('created_at')
            ->orderBy('id')
            ->get();

        $finalQuantity = $quantity;
        $finalAvg = $avg;
        foreach ($movements as $movement) {
            if ($movement->type === 'entry' || ($movement->type === 'adjustment' && $movement->quantity > 0)) {
                $newQuantity = $finalQuantity + $movement->quantity;
                $finalAvg = $newQuantity > 0
                    ? round((($finalQuantity * $finalAvg) + ($movement->quantity * $movement->unit_cost_pen)) / $newQuantity, 2)
                    : 0;
                $finalQuantity = $newQuantity;
            } else {
                $finalQuantity -= $movement->quantity;
            }
        }

        return [
            'opening' => [
                'quantity' => round($quantity, 2),
                'average_cost' => round($avg, 2),
                'total_value' => round($quantity * $avg, 2),
            ],
            'movements' => $movements,
            'closing' => [
                'quantity' => round(max($finalQuantity, 0), 2),
                'average_cost' => round($finalAvg, 2),
                'total_value' => round(max($finalQuantity, 0) * $finalAvg, 2),
            ],
        ];
    }
}