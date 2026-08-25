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
                'document_type' => '1',
                'document_number' => '12345678',
                'first_name' => 'Juan Carlos',
                'last_name' => 'Pérez Gutiérrez',
                'email' => 'juan.perez@example.com',
                'phone' => '011234567',
                'mobile' => '987654321',
                'address' => 'Av. Los Rosales 123, San Isidro',
                'ubigeo_code' => '150101',
            ],
            [
                'document_type' => '1',
                'document_number' => '87654321',
                'first_name' => 'María Fernanda',
                'last_name' => 'López Ruiz',
                'email' => 'maria.lopez@example.com',
                'phone' => '014445566',
                'mobile' => '999888777',
                'address' => 'Jr. Los Laureles 456, Miraflores',
                'ubigeo_code' => '150122',
            ],
            [
                'document_type' => '6',
                'document_number' => '20123456789',
                'business_name' => 'Transportes del Norte S.A.C.',
                'email' => 'contacto@transportesnorte.com',
                'phone' => '014774455',
                'mobile' => '955112233',
                'address' => 'Av. Industrial 789, Callao',
                'ubigeo_code' => '070101',
            ],
            [
                'document_type' => '1',
                'document_number' => '45678912',
                'first_name' => 'Carlos Alberto',
                'last_name' => 'Ramírez Torres',
                'email' => 'carlos.ramirez@example.com',
                'phone' => '012223344',
                'mobile' => '966778899',
                'address' => 'Av. Primavera 890, Surco',
                'ubigeo_code' => '150140',
            ],
            [
                'document_type' => '1',
                'document_number' => '75234198',
                'first_name' => 'Lucía',
                'last_name' => 'Gómez Salas',
                'email' => 'lucia.gomez@example.com',
                'phone' => '015556677',
                'mobile' => '933445566',
                'address' => 'Calle Los Pinos 321, La Molina',
                'ubigeo_code' => '150117',
                'receive_promotions' => true,
            ],
            [
                'document_type' => '1',
                'document_number' => '69874521',
                'first_name' => 'Pedro Pablo',
                'last_name' => 'Huamán Vilca',
                'email' => 'pedro.huaman@example.com',
                'phone' => '015445566',
                'mobile' => '911223344',
                'address' => 'Av. Los Álamos 654, Los Olivos',
                'ubigeo_code' => '150115',
            ],
            [
                'document_type' => '7',
                'document_number' => 'A1234567',
                'first_name' => 'Andrés Felipe',
                'last_name' => 'Sánchez Mendoza',
                'email' => 'andres.sanchez@example.com',
                'phone' => '012224455',
                'mobile' => '977889900',
                'address' => 'Jr. Los Claveles 789, San Borja',
                'ubigeo_code' => '150130',
            ],
            [
                'document_type' => '6',
                'document_number' => '20512345678',
                'business_name' => 'Distribuidora Automotriz Lima S.A.',
                'email' => 'ventas@dalima.com',
                'phone' => '016665544',
                'mobile' => '955334422',
                'address' => 'Av. Javier Prado Este 2500, La Molina',
                'ubigeo_code' => '150117',
                'receive_promotions' => true,
            ],
            [
                'document_type' => '4',
                'document_number' => '001234567',
                'first_name' => 'Ana Belén',
                'last_name' => 'Mendoza Ríos',
                'email' => 'ana.mendoza@example.com',
                'phone' => '015554433',
                'mobile' => '988776655',
                'address' => 'Calle Las Gardenias 100, Santiago de Surco',
                'ubigeo_code' => '150140',
            ],
            [
                'document_type' => '6',
                'document_number' => '20678912345',
                'business_name' => 'Empresa de Seguridad Proseg S.A.C.',
                'email' => 'info@proseg.com',
                'phone' => '014443322',
                'mobile' => '966112233',
                'address' => 'Av. Elmer Faucett 1450, Callao',
                'ubigeo_code' => '070101',
                'receive_promotions' => false,
            ],
        ];

        foreach ($parties as $party) {
            $documentNumber = $party['document_number'];
            unset($party['document_number']);
            Party::firstOrCreate(
                ['document_number' => $documentNumber],
                $party
            );
        }
    }
}