<?php

namespace Database\Factories;

use App\Models\Party;
use App\Models\PartyContact;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<PartyContact>
 */
class PartyContactFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'party_id' => Party::factory(),
            'name' => $this->faker->name(),
            'phone' => $this->faker->phoneNumber(),
            'email' => $this->faker->unique()->safeEmail(),
            'position' => $this->faker->optional()->jobTitle(),
            'is_primary' => false,
        ];
    }
}