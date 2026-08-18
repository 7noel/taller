<?php

namespace Database\Seeders;

use App\Models\Client;
use Illuminate\Database\Seeder;

class ClientSeeder extends Seeder
{
    public function run(): void
    {
        $clients = [
            [
                'document_type' => 'DNI',
                'document_number' => '12345678',
                'business_name' => 'Juan Carlos Pérez Gutiérrez',
                'ubigeo_code' => null,
                'address' => 'Av. Los Rosales 123, San Isidro',
                'phone' => '011234567',
                'mobile' => '987654321',
                'email' => 'juan.perez@example.com',
                'is_insurance_company' => false,
                'insurance_hourly_rate' => null,
                'insurance_panel_rate' => null,
                'establishment_id' => 1,
            ],
            [
                'document_type' => 'DNI',
                'document_number' => '87654321',
                'business_name' => 'María Fernanda López Ruiz',
                'ubigeo_code' => null,
                'address' => 'Jr. Los Laureles 456, Miraflores',
                'phone' => '014445566',
                'mobile' => '999888777',
                'email' => 'maria.lopez@example.com',
                'is_insurance_company' => false,
                'insurance_hourly_rate' => null,
                'insurance_panel_rate' => null,
                'establishment_id' => 1,
            ],
            [
                'document_type' => 'RUC',
                'document_number' => '20123456789',
                'business_name' => 'Transportes del Norte S.A.C.',
                'ubigeo_code' => null,
                'address' => 'Av. Industrial 789, Callao',
                'phone' => '014774455',
                'mobile' => '955112233',
                'email' => 'contacto@transportesnorte.com',
                'is_insurance_company' => false,
                'insurance_hourly_rate' => null,
                'insurance_panel_rate' => null,
                'establishment_id' => 1,
            ],
            [
                'document_type' => 'RUC',
                'document_number' => '20512345678',
                'business_name' => 'Seguros del Perú S.A.',
                'ubigeo_code' => null,
                'address' => 'Av. La Marina 1500, San Miguel',
                'phone' => '016667788',
                'mobile' => '944556677',
                'email' => 'ventas@segurosdelperu.com',
                'is_insurance_company' => true,
                'insurance_hourly_rate' => 45.00,
                'insurance_panel_rate' => 120.00,
                'establishment_id' => 1,
            ],
            [
                'document_type' => 'DNI',
                'document_number' => '45678912',
                'business_name' => 'Carlos Alberto Ramírez Torres',
                'ubigeo_code' => null,
                'address' => 'Av. Primavera 890, Surco',
                'phone' => '012223344',
                'mobile' => '966778899',
                'email' => 'carlos.ramirez@example.com',
                'is_insurance_company' => false,
                'insurance_hourly_rate' => null,
                'insurance_panel_rate' => null,
                'establishment_id' => 1,
            ],
        ];

        foreach ($clients as $client) {
            Client::create($client);
        }
    }
}