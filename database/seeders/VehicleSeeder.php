<?php

namespace Database\Seeders;

use App\Models\Brand;
use App\Models\Vehicle;
use App\Models\VehicleModel;
use Illuminate\Database\Seeder;

class VehicleSeeder extends Seeder
{
    public function run(): void
    {
        $vehicles = [
            ['plate' => 'ABC123', 'brand' => 'TOYOTA', 'model' => 'COROLLA', 'body_type' => 'sedan', 'color' => 'Blanco', 'vin' => 'JTDBT123456789012', 'engine_number' => '2ZR1234567', 'year' => 2019, 'technical_review_date' => now()->addMonths(6)->toDateString(), 'review_reminder_days' => 15],
            ['plate' => 'XYZ456', 'brand' => 'NISSAN', 'model' => 'FRONTIER', 'body_type' => 'pickup', 'color' => 'Gris', 'vin' => 'JN8BUT1A123456789', 'engine_number' => 'YD25987654', 'year' => 2020, 'technical_review_date' => now()->addMonths(3)->toDateString(), 'review_reminder_days' => 10],
            ['plate' => 'DEF789', 'brand' => 'HYUNDAI', 'model' => 'TUCSON', 'body_type' => 'suv', 'color' => 'Rojo', 'vin' => 'KM8J33AL4987654321', 'engine_number' => 'G4NA112233', 'year' => 2021, 'technical_review_date' => now()->addMonths(9)->toDateString(), 'review_reminder_days' => 15],
            ['plate' => 'GHI1234', 'brand' => 'KIA', 'model' => 'SPORTAGE', 'body_type' => 'suv', 'color' => 'Azul', 'vin' => 'KNAPB811AB7731234', 'engine_number' => 'G4KD445566', 'year' => 2022, 'technical_review_date' => now()->addMonths(12)->toDateString(), 'review_reminder_days' => 20],
            ['plate' => 'JKL202', 'brand' => 'HONDA', 'model' => 'CIVIC', 'body_type' => 'sedan', 'color' => 'Negro', 'vin' => '2HGFC2F59JH123456', 'engine_number' => 'R18778899', 'year' => 2018, 'technical_review_date' => now()->addMonths(2)->toDateString(), 'review_reminder_days' => 10],
        ];

        foreach ($vehicles as $vehicle) {
            $brandName = $vehicle['brand'];
            $modelName = $vehicle['model'];
            unset($vehicle['brand'], $vehicle['model']);

            $brand = Brand::firstOrCreate(['name' => $brandName]);
            $model = VehicleModel::firstOrCreate(['brand_id' => $brand->id, 'name' => $modelName]);
            $vehicle['brand_id'] = $brand->id;
            $vehicle['model_id'] = $model->id;

            // Todo vehículo se crea con su token de acceso público (portal del cliente).
            $vehicle['access_token'] = Vehicle::generateAccessToken();
            $vehicle['access_token_created_at'] = now();

            Vehicle::firstOrCreate(['plate' => $vehicle['plate']], $vehicle);
        }
    }
}