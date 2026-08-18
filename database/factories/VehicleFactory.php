<?php

namespace Database\Factories;

use App\Models\Establishment;
use App\Models\Vehicle;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Vehicle>
 */
class VehicleFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'plate' => strtoupper($this->faker->bothify('???-###')),
            'brand' => $this->faker->randomElement(['Toyota', 'Honda', 'Nissan', 'Hyundai', 'Kia', 'Mazda']),
            'model' => $this->faker->randomElement(['Corolla', 'Civic', 'Sentra', 'Elantra', 'Sportage', '3']),
            'body_type' => $this->faker->randomElement(['sedan', 'suv', 'pickup', 'camioneta', 'camion', 'moto']),
            'color' => $this->faker->safeColorName(),
            'vin' => strtoupper($this->faker->bothify('??????##############')),
            'engine_number' => $this->faker->bothify('###?-####'),
            'year' => $this->faker->numberBetween(2000, 2025),
            'next_technical_review_date' => $this->faker->dateTimeBetween('now', '+1 year')->format('Y-m-d'),
            'technical_review_reminder_days' => 15,
            'establishment_id' => Establishment::first()?->id ?? 1,
        ];
    }
}