<?php

namespace Database\Seeders;

use App\Models\Party;
use Illuminate\Database\Seeder;

class PartySeeder extends Seeder
{
    public function run(): void
    {
        $parties = [
            [
                'type' => 'person',
                'document_type' => 'DNI',
                'document_number' => '12345678',
                'first_name' => 'Juan Carlos',
                'last_name' => 'Pérez Gutiérrez',
                'email' => 'juan.perez@example.com',
                'phone' => '011234567',
                'mobile' => '987654321',
                'address' => 'Av. Los Rosales 123, San Isidro',
            ],
            [
                'type' => 'person',
                'document_type' => 'DNI',
                'document_number' => '87654321',
                'first_name' => 'María Fernanda',
                'last_name' => 'López Ruiz',
                'email' => 'maria.lopez@example.com',
                'phone' => '014445566',
                'mobile' => '999888777',
                'address' => 'Jr. Los Laureles 456, Miraflores',
            ],
            [
                'type' => 'company',
                'document_type' => 'RUC',
                'document_number' => '20123456789',
                'business_name' => 'Transportes del Norte S.A.C.',
                'email' => 'contacto@transportesnorte.com',
                'phone' => '014774455',
                'mobile' => '955112233',
                'address' => 'Av. Industrial 789, Callao',
            ],
            [
                'type' => 'person',
                'document_type' => 'DNI',
                'document_number' => '45678912',
                'first_name' => 'Carlos Alberto',
                'last_name' => 'Ramírez Torres',
                'email' => 'carlos.ramirez@example.com',
                'phone' => '012223344',
                'mobile' => '966778899',
                'address' => 'Av. Primavera 890, Surco',
            ],
            [
                'type' => 'person',
                'document_type' => 'DNI',
                'document_number' => '75234198',
                'first_name' => 'Lucía',
                'last_name' => 'Gómez Salas',
                'email' => 'lucia.gomez@example.com',
                'phone' => '015556677',
                'mobile' => '933445566',
                'address' => 'Calle Los Pinos 321, La Molina',
                'receive_promotions' => true,
            ],
            [
                'type' => 'person',
                'document_type' => 'DNI',
                'document_number' => '69874521',
                'first_name' => 'Pedro Pablo',
                'last_name' => 'Huamán Vilca',
                'email' => 'pedro.huaman@example.com',
                'phone' => '015445566',
                'mobile' => '911223344',
                'address' => 'Av. Los Álamos 654, Los Olivos',
            ],
            [
                'type' => 'person',
                'document_type' => 'PAS',
                'document_number' => 'A1234567',
                'first_name' => 'Andrés Felipe',
                'last_name' => 'Sánchez Mendoza',
                'email' => 'andres.sanchez@example.com',
                'phone' => '012224455',
                'mobile' => '977889900',
                'address' => 'Jr. Los Claveles 789, San Borja',
            ],
            [
                'type' => 'company',
                'document_type' => 'RUC',
                'document_number' => '20512345678',
                'business_name' => 'Distribuidora Automotriz Lima S.A.',
                'email' => 'ventas@dalima.com',
                'phone' => '016665544',
                'mobile' => '955334422',
                'address' => 'Av. Javier Prado Este 2500, La Molina',
                'receive_promotions' => true,
            ],
            [
                'type' => 'person',
                'document_type' => 'CEX',
                'document_number' => '001234567',
                'first_name' => 'Ana Belén',
                'last_name' => 'Mendoza Ríos',
                'email' => 'ana.mendoza@example.com',
                'phone' => '015554433',
                'mobile' => '988776655',
                'address' => 'Calle Las Gardenias 100, Santiago de Surco',
            ],
            [
                'type' => 'company',
                'document_type' => 'RUC',
                'document_number' => '20678912345',
                'business_name' => 'Empresa de Seguridad Proseg S.A.C.',
                'email' => 'info@proseg.com',
                'phone' => '014443322',
                'mobile' => '966112233',
                'address' => 'Av. Elmer Faucett 1450, Callao',
                'receive_promotions' => false,
            ],
        ];

        foreach ($parties as $party) {
            Party::create(array_merge($party, [
                'establishment_id' => 1,
            ]));
        }
    }
}