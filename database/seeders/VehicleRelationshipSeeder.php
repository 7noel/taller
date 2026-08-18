<?php

namespace Database\Seeders;

use App\Models\VehicleRelationship;
use Illuminate\Database\Seeder;

class VehicleRelationshipSeeder extends Seeder
{
    public function run(): void
    {
        $relationships = [
            // Vehículo 1 (ABC-123): dueño Juan Pérez, chofer Pedro Huamán, aseguradora Rímac
            [
                'vehicle_id' => 1,
                'party_id' => 1,
                'role' => 'owner',
                'is_primary_commercial' => true,
                'notes' => 'Cliente principal y aprobador.',
            ],
            [
                'vehicle_id' => 1,
                'party_id' => 6,
                'role' => 'driver',
                'is_primary_commercial' => false,
                'notes' => 'Chofer habitual.',
            ],
            [
                'vehicle_id' => 1,
                'party_id' => 11,
                'role' => 'insurance_company',
                'is_primary_commercial' => false,
                'notes' => 'Póliza todo riesgo.',
            ],

            // Vehículo 2 (XYZ-456): dueño Transportes del Norte, operador Roberto, aseguradora Pacífico
            [
                'vehicle_id' => 2,
                'party_id' => 3,
                'role' => 'owner',
                'is_primary_commercial' => true,
                'notes' => 'Flota corporativa.',
            ],
            [
                'vehicle_id' => 2,
                'party_id' => 3,
                'role' => 'operator',
                'is_primary_commercial' => false,
                'notes' => 'Operador de la flota.',
            ],
            [
                'vehicle_id' => 2,
                'party_id' => 12,
                'role' => 'insurance_company',
                'is_primary_commercial' => false,
                'notes' => null,
            ],

            // Vehículo 3 (DEF-789): dueña María López, aprobador Carlos Ramírez
            [
                'vehicle_id' => 3,
                'party_id' => 2,
                'role' => 'owner',
                'is_primary_commercial' => true,
                'notes' => 'Cliente particular.',
            ],
            [
                'vehicle_id' => 3,
                'party_id' => 4,
                'role' => 'approver',
                'is_primary_commercial' => false,
                'notes' => 'Contacto de aprobación de presupuestos.',
            ],

            // Vehículo 4 (GHI-101): dueño Carlos Ramírez, aseguradora Mapfre
            [
                'vehicle_id' => 4,
                'party_id' => 4,
                'role' => 'owner',
                'is_primary_commercial' => true,
                'notes' => null,
            ],
            [
                'vehicle_id' => 4,
                'party_id' => 13,
                'role' => 'insurance_company',
                'is_primary_commercial' => false,
                'notes' => 'Póliza terceros completo.',
            ],

            // Vehículo 5 (JKL-202): dueño Pedro Huamán, cobranza Proseg, aseguradora La Positiva
            [
                'vehicle_id' => 5,
                'party_id' => 6,
                'role' => 'owner',
                'is_primary_commercial' => true,
                'notes' => 'Dueño y contacto principal.',
            ],
            [
                'vehicle_id' => 5,
                'party_id' => 10,
                'role' => 'billing',
                'is_primary_commercial' => false,
                'notes' => 'Empresa que factura los servicios.',
            ],
            [
                'vehicle_id' => 5,
                'party_id' => 14,
                'role' => 'insurance_company',
                'is_primary_commercial' => false,
                'notes' => null,
            ],
            [
                'vehicle_id' => 5,
                'party_id' => 9,
                'role' => 'emergency_contact',
                'is_primary_commercial' => false,
                'notes' => 'Contacto de emergencia.',
            ],
        ];

        foreach ($relationships as $relationship) {
            VehicleRelationship::create($relationship);
        }
    }
}