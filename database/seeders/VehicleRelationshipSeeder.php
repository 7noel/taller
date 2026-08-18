<?php

namespace Database\Seeders;

use App\Models\Party;
use App\Models\Vehicle;
use App\Models\VehicleRelationship;
use Illuminate\Database\Seeder;

class VehicleRelationshipSeeder extends Seeder
{
    public function run(): void
    {
        $relationships = [
            // Vehículo ABC123 (Toyota Corolla): dueño Juan Pérez, chofer Pedro Huamán, aseguradora Rímac
            ['plate' => 'ABC123', 'document_number' => '12345678', 'role' => 'owner', 'is_primary' => true, 'notes' => 'Cliente principal y aprobador.'],
            ['plate' => 'ABC123', 'document_number' => '69874521', 'role' => 'driver', 'is_primary' => false, 'notes' => 'Chofer habitual.'],
            ['plate' => 'ABC123', 'document_number' => '20100039202', 'role' => 'insurance_company', 'is_primary' => false, 'notes' => 'Póliza todo riesgo.'],

            // Vehículo XYZ456 (Nissan Frontier): dueño Transportes del Norte, aseguradora Pacífico
            ['plate' => 'XYZ456', 'document_number' => '20123456789', 'role' => 'owner', 'is_primary' => true, 'notes' => 'Flota corporativa.'],
            ['plate' => 'XYZ456', 'document_number' => '20123456789', 'role' => 'operator', 'is_primary' => false, 'notes' => 'Operador de la flota.'],
            ['plate' => 'XYZ456', 'document_number' => '20100047218', 'role' => 'insurance_company', 'is_primary' => false, 'notes' => null],

            // Vehículo DEF789 (Hyundai Tucson): dueña María López, aprobador Carlos Ramírez
            ['plate' => 'DEF789', 'document_number' => '87654321', 'role' => 'owner', 'is_primary' => true, 'notes' => 'Cliente particular.'],
            ['plate' => 'DEF789', 'document_number' => '45678912', 'role' => 'approver', 'is_primary' => false, 'notes' => 'Contacto de aprobación de presupuestos.'],

            // Vehículo GHI1234 (Kia Sportage): dueño Carlos Ramírez, aseguradora Mapfre
            ['plate' => 'GHI1234', 'document_number' => '45678912', 'role' => 'owner', 'is_primary' => true, 'notes' => null],
            ['plate' => 'GHI1234', 'document_number' => '20100194372', 'role' => 'insurance_company', 'is_primary' => false, 'notes' => 'Póliza terceros completo.'],

            // Vehículo JKL202 (Honda Civic): dueño Pedro Huamán, cobranza Proseg, aseguradora La Positiva, emergencia Ana Mendoza
            ['plate' => 'JKL202', 'document_number' => '69874521', 'role' => 'owner', 'is_primary' => true, 'notes' => 'Dueño y contacto principal.'],
            ['plate' => 'JKL202', 'document_number' => '20678912345', 'role' => 'billing', 'is_primary' => false, 'notes' => 'Empresa que factura los servicios.'],
            ['plate' => 'JKL202', 'document_number' => '20100041921', 'role' => 'insurance_company', 'is_primary' => false, 'notes' => null],
            ['plate' => 'JKL202', 'document_number' => '001234567', 'role' => 'emergency_contact', 'is_primary' => false, 'notes' => 'Contacto de emergencia.'],
        ];

        foreach ($relationships as $rel) {
            $vehicle = Vehicle::where('plate', $rel['plate'])->first();
            $party = Party::where('document_number', $rel['document_number'])->first();

            if (! $vehicle || ! $party) {
                continue;
            }

            VehicleRelationship::firstOrCreate(
                ['vehicle_id' => $vehicle->id, 'party_id' => $party->id, 'role' => $rel['role']],
                [
                    'is_primary_commercial' => $rel['is_primary'],
                    'notes' => $rel['notes'],
                ]
            );
        }
    }
}