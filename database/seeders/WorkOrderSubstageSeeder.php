<?php

namespace Database\Seeders;

use App\Models\WorkOrderSubstage;
use Illuminate\Database\Seeder;

class WorkOrderSubstageSeeder extends Seeder
{
    public function run(): void
    {
        $substages = [
            ['name' => 'Recepción y diagnóstico', 'description' => 'Ingreso, verificación y diagnóstico del vehículo', 'order' => 1],
            ['name' => 'Desabolladura y pintura', 'description' => 'Trabajos de carrocería, masilla y pintura', 'order' => 2],
            ['name' => 'Mecánica', 'description' => 'Trabajos mecánicos de motor y transmisión', 'order' => 3],
            ['name' => 'Electricidad', 'description' => 'Sistema eléctrico y electrónico del vehículo', 'order' => 4],
            ['name' => 'Instalación de repuestos', 'description' => 'Montaje de repuestos recibidos o importados', 'order' => 5],
            ['name' => 'Control de calidad', 'description' => 'Verificación final antes de la entrega', 'order' => 6],
        ];

        foreach ($substages as $substage) {
            WorkOrderSubstage::updateOrCreate(
                ['name' => $substage['name']],
                [
                    'description' => $substage['description'],
                    'order' => $substage['order'],
                    'is_active' => true,
                ]
            );
        }
    }
}
