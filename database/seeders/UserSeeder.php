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
        $user = User::create([
            'name' => 'Administrador',
            'email' => 'admin@taller.com',
            'password' => Hash::make('password'),
            'establishment_id' => 1,
            'phone' => '999888777',
        ]);
        $user->assignRole('Administrador');
    }
}