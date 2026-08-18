<?php

namespace Database\Seeders;

use App\Models\Client;
use App\Models\Vehicle;
use App\Models\VehicleContact;
use Illuminate\Database\Seeder;

class VehicleSeeder extends Seeder
{
    public function run(): void
    {
        // Vehículo 1 - pertenece al cliente 1 (Juan Pérez)
        $vehicle1 = Vehicle::create([
            'plate' => 'ABC-123',
            'client_id' => 1,
            'brand' => 'Toyota',
            'model' => 'Corolla',
            'body_type' => 'sedan',
            'color' => 'Blanco',
            'vin' => 'JTDBT123456789012',
            'engine_number' => '2ZR-1234567',
            'year' => 2019,
            'establishment_id' => 1,
        ]);

        VehicleContact::create([
            'vehicle_id' => $vehicle1->id,
            'type' => 'approver',
            'name' => 'Juan Carlos Pérez Gutiérrez',
            'phone' => '987654321',
            'email' => 'juan.perez@example.com',
        ]);

        VehicleContact::create([
            'vehicle_id' => $vehicle1->id,
            'type' => 'driver',
            'name' => 'Luis Alberto Pérez',
            'phone' => '988776655',
            'email' => 'luis.perez@example.com',
        ]);

        // Vehículo 2 - pertenece al cliente 3 (Transportes del Norte)
        $vehicle2 = Vehicle::create([
            'plate' => 'XYZ-456',
            'client_id' => 3,
            'brand' => 'Nissan',
            'model' => 'Frontier',
            'body_type' => 'pickup',
            'color' => 'Gris',
            'vin' => 'JN8BUT1A-123456789',
            'engine_number' => 'YD25-987654',
            'year' => 2020,
            'establishment_id' => 1,
        ]);

        VehicleContact::create([
            'vehicle_id' => $vehicle2->id,
            'type' => 'operator',
            'name' => 'Roberto Sánchez Díaz',
            'phone' => '944556677',
            'email' => 'r.sanchez@transportesnorte.com',
            'company_name' => 'Transportes del Norte S.A.C.',
        ]);

        // Vehículo 3 - pertenece al cliente 2 (María López)
        $vehicle3 = Vehicle::create([
            'plate' => 'DEF-789',
            'client_id' => 2,
            'brand' => 'Hyundai',
            'model' => 'Tucson',
            'body_type' => 'suv',
            'color' => 'Rojo',
            'vin' => 'KM8J33AL4-987654321',
            'engine_number' => 'G4NA-112233',
            'year' => 2021,
            'establishment_id' => 1,
        ]);

        VehicleContact::create([
            'vehicle_id' => $vehicle3->id,
            'type' => 'approver',
            'name' => 'María Fernanda López Ruiz',
            'phone' => '999888777',
            'email' => 'maria.lopez@example.com',
        ]);
    }
}