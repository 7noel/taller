<?php

namespace Database\Factories;

use App\Models\Vehicle;
use App\Models\VehicleContact;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<VehicleContact>
 */
class VehicleContactFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'vehicle_id' => Vehicle::factory(),
            'type' => $this->faker->randomElement(['approver', 'driver', 'operator']),
            'name' => $this->faker->name(),
            'phone' => $this->faker->phoneNumber(),
            'email' => $this->faker->unique()->safeEmail(),
            'company_name' => null,
        ];
    }
}