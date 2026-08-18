<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class UserSeeder extends Seeder
{
    public function run()
    {
        $user = User::firstOrCreate(
            ['email' => 'admin@taller.com'],
            [
                'name' => 'Administrador',
                'password' => Hash::make('password'),
                'establishment_id' => 1,
                'phone' => '999888777',
            ]
        );

        if (! $user->hasRole('Administrador')) {
            $user->assignRole('Administrador');
        }
    }
}