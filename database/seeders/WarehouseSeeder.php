<?php

namespace Database\Seeders;

use App\Models\Establishment;
use App\Models\Warehouse;
use Illuminate\Database\Seeder;

class WarehouseSeeder extends Seeder
{
    public function run(): void
    {
        $establishment = Establishment::first();

        if (! $establishment) {
            return;
        }

        Warehouse::updateOrCreate(
            ['code' => 'ALM-01'],
            [
                'establishment_id' => $establishment->id,
                'name' => 'Almacén Principal',
                'location' => 'Patio central',
                'is_active' => true,
            ]
        );
    }
}