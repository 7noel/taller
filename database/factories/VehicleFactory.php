<?php

namespace Database\Factories;

use App\Models\Brand;
use App\Models\Vehicle;
use App\Models\VehicleModel;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Vehicle>
 */
class VehicleFactory extends Factory
{
    public function definition(): array
    {
        $brand = Brand::first() ?: Brand::create(['name' => 'TOYOTA']);
        $model = VehicleModel::first() ?: VehicleModel::create(['brand_id' => $brand->id, 'name' => 'COROLLA']);

        return [
            'plate' => strtoupper($this->faker->randomElement([
                $this->faker->bothify('???###'),
                $this->faker->bothify('???####'),
            ])),
            'brand_id' => $brand->id,
            'model_id' => $model->id,
            'color' => $this->faker->safeColorName(),
            'vin' => strtoupper($this->faker->bothify('??????##############')),
            'engine_number' => strtoupper($this->faker->bothify('###?-####')),
            'year' => $this->faker->numberBetween(2000, 2025),
            'body_type' => $this->faker->randomElement(['sedan', 'suv', 'pickup', 'camioneta', 'camion', 'moto']),
            'technical_review_date' => $this->faker->dateTimeBetween('now', '+1 year')->format('Y-m-d'),
            'review_reminder_days' => 15,
        ];
    }
}