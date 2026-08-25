<?php

namespace Database\Seeders;

use App\Models\PartBrand;
use App\Models\PartCategory;
use App\Models\ServiceCategory;
use Illuminate\Database\Seeder;

class CatalogSeeder extends Seeder
{
    public function run(): void
    {
        $serviceCategories = ['Mantenimiento', 'Frenos', 'Carrocería', 'Suspensión', 'Diagnóstico', 'Eléctrico'];
        foreach ($serviceCategories as $name) {
            ServiceCategory::updateOrCreate(['name' => $name], ['is_active' => true]);
        }

        $partCategories = ['Filtros', 'Lubricantes', 'Frenos', 'Encendido', 'Suspensión', 'Eléctrico'];
        foreach ($partCategories as $name) {
            PartCategory::updateOrCreate(['name' => $name], ['is_active' => true]);
        }

        $partBrands = ['Mann Filter', 'Mobil', 'Bosch', 'NGK', 'K&N', 'Febi', 'Sachs', 'Denso'];
        foreach ($partBrands as $name) {
            PartBrand::updateOrCreate(['name' => $name], ['is_active' => true]);
        }
    }
}