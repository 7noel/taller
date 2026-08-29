<?php

namespace Database\Seeders;

use App\Models\Part;
use App\Models\PartBrand;
use App\Models\PartCategory;
use Illuminate\Database\Seeder;

class PartSeeder extends Seeder
{
    public function run(): void
    {
        $brand = fn (string $name) => PartBrand::where('name', $name)->value('id');
        $category = fn (string $name) => PartCategory::where('name', $name)->value('id');

        $parts = [
            [
                'name' => 'Filtro de aceite',
                'sku' => 'FO-001',
                'manufacturer_code' => 'FO-001-MFG',
                'barcode' => '7750000000011',
                'part_brand_id' => $brand('Mann Filter'),
                'part_category_id' => $category('Filtros'),
                'uom' => 'NIU',
                'min_stock' => 10,
                'cost_price' => 25.00,
                'cost_currency' => 'PEN',
                'sell_price' => 45.00,
                'currency' => 'PEN',
                'is_active' => true,
            ],
            [
                'name' => 'Aceite sintético 5W-30 (1 galón)',
                'sku' => 'AC-5W30',
                'manufacturer_code' => null,
                'barcode' => '7750000000028',
                'part_brand_id' => $brand('Mobil'),
                'part_category_id' => $category('Lubricantes'),
                'uom' => 'NIU',
                'min_stock' => 20,
                'cost_price' => 95.00,
                'cost_currency' => 'PEN',
                'sell_price' => 140.00,
                'currency' => 'PEN',
                'is_active' => true,
            ],
            [
                'name' => 'Pastillas de freno delanteras',
                'sku' => 'PF-001',
                'manufacturer_code' => 'PF-001-MFG',
                'barcode' => '7750000000035',
                'part_brand_id' => $brand('Bosch'),
                'part_category_id' => $category('Frenos'),
                'uom' => 'NIU',
                'min_stock' => 5,
                'cost_price' => 110.00,
                'cost_currency' => 'PEN',
                'sell_price' => 190.00,
                'currency' => 'PEN',
                'is_active' => true,
            ],
            [
                'name' => 'Bujía de encendido (x4)',
                'sku' => 'BU-004',
                'manufacturer_code' => null,
                'barcode' => '7750000000042',
                'part_brand_id' => $brand('NGK'),
                'part_category_id' => $category('Encendido'),
                'uom' => 'NIU',
                'min_stock' => 15,
                'cost_price' => 60.00,
                'cost_currency' => 'PEN',
                'sell_price' => 100.00,
                'currency' => 'PEN',
                'is_active' => true,
            ],
            [
                'name' => 'Filtro de aire',
                'sku' => 'FA-001',
                'manufacturer_code' => 'FA-001-MFG',
                'barcode' => '7750000000059',
                'part_brand_id' => $brand('K&N'),
                'part_category_id' => $category('Filtros'),
                'uom' => 'NIU',
                'min_stock' => 8,
                'cost_price' => 55.00,
                'cost_currency' => 'PEN',
                'sell_price' => 95.00,
                'currency' => 'PEN',
                'is_active' => true,
            ],
        ];

        foreach ($parts as $part) {
            Part::updateOrCreate(['sku' => $part['sku']], $part);
        }
    }
}