<?php

namespace Database\Factories;

use App\Models\Party;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Party>
 */
class PartyFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'type' => 'person',
            'document_type' => 'DNI',
            'document_number' => 'dni_' . $this->faker->unique()->numerify('########'),
            'first_name' => $this->faker->firstName(),
            'last_name' => $this->faker->lastName(),
            'business_name' => null,
            'email' => $this->faker->unique()->safeEmail(),
            'phone' => $this->faker->phoneNumber(),
            'mobile' => $this->faker->phoneNumber(),
            'address' => $this->faker->address(),
            'ubigeo_code' => null,
            'is_insurance_company' => false,
            'insurance_hourly_rate' => null,
            'insurance_panel_rate' => null,
            'receive_promotions' => true,
        ];
    }

    public function person(): static
    {
        return $this->state(fn (array $attributes) => [
            'type' => 'person',
            'document_type' => $this->faker->randomElement(['DNI', 'PAS', 'CEX']),
            'document_number' => $this->faker->unique()->numerify('########'),
            'first_name' => $this->faker->firstName(),
            'last_name' => $this->faker->lastName(),
            'business_name' => null,
        ]);
    }

    public function company(): static
    {
        return $this->state(fn (array $attributes) => [
            'type' => 'company',
            'document_type' => 'RUC',
            'document_number' => $this->faker->unique()->numerify('20###########'),
            'first_name' => null,
            'last_name' => null,
            'business_name' => $this->faker->company(),
        ]);
    }

    public function insuranceCompany(): static
    {
        return $this->company()->state(fn (array $attributes) => [
            'is_insurance_company' => true,
            'insurance_hourly_rate' => $this->faker->randomFloat(2, 45, 90),
            'insurance_panel_rate' => $this->faker->randomFloat(2, 100, 350),
        ]);
    }
}