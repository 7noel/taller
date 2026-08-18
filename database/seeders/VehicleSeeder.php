<?php

namespace Database\Seeders;

use App\Models\Vehicle;
use Illuminate\Database\Seeder;

class VehicleSeeder extends Seeder
{
    public function run(): void
    {
        $vehicles = [
            [
                'plate' => 'ABC123',
                'brand' => 'Toyota',
                'model' => 'Corolla',
                'body_type' => 'sedan',
                'color' => 'Blanco',
                'vin' => 'JTDBT123456789012',
                'engine_number' => '2ZR1234567',
                'year' => 2019,
                'next_technical_review_date' => now()->addMonths(6)->toDateString(),
                'technical_review_reminder_days' => 15,
            ],
            [
                'plate' => 'XYZ456',
                'brand' => 'Nissan',
                'model' => 'Frontier',
                'body_type' => 'pickup',
                'color' => 'Gris',
                'vin' => 'JN8BUT1A123456789',
                'engine_number' => 'YD25987654',
                'year' => 2020,
                'next_technical_review_date' => now()->addMonths(3)->toDateString(),
                'technical_review_reminder_days' => 10,
            ],
            [
                'plate' => 'DEF789',
                'brand' => 'Hyundai',
                'model' => 'Tucson',
                'body_type' => 'suv',
                'color' => 'Rojo',
                'vin' => 'KM8J33AL4987654321',
                'engine_number' => 'G4NA112233',
                'year' => 2021,
                'next_technical_review_date' => now()->addMonths(9)->toDateString(),
                'technical_review_reminder_days' => 15,
            ],
            [
                'plate' => 'GHI1234',
                'brand' => 'Kia',
                'model' => 'Sportage',
                'body_type' => 'suv',
                'color' => 'Azul',
                'vin' => 'KNAPB811AB7731234',
                'engine_number' => 'G4KD445566',
                'year' => 2022,
                'next_technical_review_date' => now()->addMonths(12)->toDateString(),
                'technical_review_reminder_days' => 20,
            ],
            [
                'plate' => 'JKL202',
                'brand' => 'Honda',
                'model' => 'Civic',
                'body_type' => 'sedan',
                'color' => 'Negro',
                'vin' => '2HGFC2F59JH123456',
                'engine_number' => 'R18778899',
                'year' => 2018,
                'next_technical_review_date' => now()->addMonths(2)->toDateString(),
                'technical_review_reminder_days' => 10,
            ],
        ];

        foreach ($vehicles as $vehicle) {
            Vehicle::create($vehicle);
        }
    }
}