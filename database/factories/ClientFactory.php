<?php

namespace Database\Factories;

use App\Models\Client;
use App\Models\Establishment;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Client>
 */
class ClientFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'document_type' => $this->faker->randomElement(['DNI', 'RUC', 'PAS', 'CEX']),
            'document_number' => $this->faker->unique()->numerify('########'),
            'business_name' => $this->faker->name(),
            'ubigeo_code' => null,
            'address' => $this->faker->address(),
            'phone' => $this->faker->phoneNumber(),
            'mobile' => $this->faker->phoneNumber(),
            'email' => $this->faker->unique()->safeEmail(),
            'is_insurance_company' => false,
            'insurance_hourly_rate' => null,
            'insurance_panel_rate' => null,
            'establishment_id' => Establishment::first()?->id ?? 1,
        ];
    }

    public function insuranceCompany(): static
    {
        return $this->state(fn (array $attributes) => [
            'document_type' => 'RUC',
            'business_name' => $this->faker->company(),
            'is_insurance_company' => true,
            'insurance_hourly_rate' => $this->faker->randomFloat(2, 50, 200),
            'insurance_panel_rate' => $this->faker->randomFloat(2, 100, 400),
        ]);
    }
}