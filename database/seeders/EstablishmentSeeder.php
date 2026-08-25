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
                'address' => 'Av. Principal 123, San Isidro',
                'ubigeo_code' => '150101',
                'phone' => '011234567',
                'celular' => '987654321',
                'email' => 'central@tallermotor.com',
            ]
        );
    }
}