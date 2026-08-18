<?php

namespace Database\Factories;

use App\Models\Party;
use App\Models\Vehicle;
use App\Models\VehicleRelationship;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<VehicleRelationship>
 */
class VehicleRelationshipFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $roles = ['owner', 'driver', 'approver', 'operator', 'billing', 'insurance_company', 'emergency_contact', 'other'];

        return [
            'vehicle_id' => Vehicle::factory(),
            'party_id' => Party::factory(),
            'role' => $this->faker->randomElement($roles),
            'is_primary_commercial' => false,
            'notes' => $this->faker->optional()->sentence(),
        ];
    }

    public function owner(): static
    {
        return $this->state(fn (array $attributes) => [
            'role' => 'owner',
            'is_primary_commercial' => true,
        ]);
    }
}