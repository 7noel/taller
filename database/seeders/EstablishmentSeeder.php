<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Establishment;

class EstablishmentSeeder extends Seeder
{
    public function run()
    {
        Establishment::firstOrCreate(
            ['code' => '001'],
            [
                'name' => 'Sede Central',
                'address' => 'Av. Principal 123',
                'phone' => '987654321',
                'email' => 'central@taller.com',
            ]
        );
    }
}