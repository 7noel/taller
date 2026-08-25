<?php

namespace Database\Seeders;

use App\Models\Part;
use App\Models\Warehouse;
use App\Services\StockService;
use Illuminate\Database\Seeder;

class StockSeeder extends Seeder
{
    public function run(): void
    {
        $warehouse = Warehouse::first();

        if (! $warehouse) {
            return;
        }

        $stockService = app(StockService::class);

        $initial = [
            ['sku' => 'FO-001', 'qty' => 20],
            ['sku' => 'AC-5W30', 'qty' => 40],
            ['sku' => 'PF-001', 'qty' => 12],
            ['sku' => 'BU-004', 'qty' => 30],
            ['sku' => 'FA-001', 'qty' => 15],
        ];

        foreach ($initial as $row) {
            $part = Part::where('sku', $row['sku'])->first();

            if (! $part) {
                continue;
            }

            $stockService->registerMovement(
                partId: $part->id,
                warehouseId: $warehouse->id,
                type: 'entry',
                quantity: $row['qty'],
                unitCost: (float) $part->cost_price,
                currency: $part->cost_currency ?? 'PEN',
                exchangeRate: null,
                documentType: null,
                documentId: null,
                reference: 'Inventario inicial (seeder)',
            );
        }
    }
}