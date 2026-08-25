<?php

namespace Database\Seeders;

use App\Models\RepairService;
use App\Models\ServiceCategory;
use Illuminate\Database\Seeder;

class RepairServiceSeeder extends Seeder
{
    public function run(): void
    {
        $category = fn (string $name) => ServiceCategory::where('name', $name)->value('id');

        $services = [
            [
                'name' => 'Cambio de aceite y filtro',
                'service_category_id' => $category('Mantenimiento'),
                'pricing_type' => 'fixed',
                'estimated_hours' => 1.0,
                'min_hours' => null,
                'sell_price' => 120.00,
                'currency' => 'PEN',
                'cost_price' => 55.00,
                'cost_currency' => 'PEN',
                'is_outsourced' => false,
                'is_active' => true,
            ],
            [
                'name' => 'Frenos (pastillas delanteras)',
                'service_category_id' => $category('Frenos'),
                'pricing_type' => 'fixed',
                'estimated_hours' => 1.5,
                'min_hours' => null,
                'sell_price' => 180.00,
                'currency' => 'PEN',
                'cost_price' => 90.00,
                'cost_currency' => 'PEN',
                'is_outsourced' => false,
                'is_active' => true,
            ],
            [
                'name' => 'Planchado y pintura por paño',
                'service_category_id' => $category('Carrocería'),
                'pricing_type' => 'time_based',
                'estimated_hours' => 4.0,
                'min_hours' => 2.0,
                'sell_price' => 80.00,
                'currency' => 'USD',
                'cost_price' => 35.00,
                'cost_currency' => 'USD',
                'is_outsourced' => false,
                'is_active' => true,
            ],
            [
                'name' => 'Alineamiento y balanceo',
                'service_category_id' => $category('Suspensión'),
                'pricing_type' => 'fixed',
                'estimated_hours' => 1.0,
                'min_hours' => null,
                'sell_price' => 90.00,
                'currency' => 'PEN',
                'cost_price' => 20.00,
                'cost_currency' => 'PEN',
                'is_outsourced' => true,
                'is_active' => true,
            ],
            [
                'name' => 'Diagnóstico electrónico (scanner)',
                'service_category_id' => $category('Diagnóstico'),
                'pricing_type' => 'fixed',
                'estimated_hours' => 0.5,
                'min_hours' => null,
                'sell_price' => 60.00,
                'currency' => 'PEN',
                'cost_price' => 10.00,
                'cost_currency' => 'PEN',
                'is_outsourced' => false,
                'is_active' => true,
            ],
        ];

        foreach ($services as $service) {
            RepairService::updateOrCreate(['name' => $service['name']], $service);
        }
    }
}