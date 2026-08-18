<?php

namespace Database\Seeders;

use App\Models\Party;
use Illuminate\Database\Seeder;

class InsuranceCompanySeeder extends Seeder
{
    public function run(): void
    {
        $companies = [
            [
                'document_number' => '20100039202',
                'business_name' => 'Rímac Seguros y Reaseguros',
                'email' => 'servicioalcliente@rimac.com.pe',
                'phone' => '014110200',
                'insurance_hourly_rate' => 55.00,
                'insurance_panel_rate' => 180.00,
            ],
            [
                'document_number' => '20100047218',
                'business_name' => 'Pacífico Seguros',
                'email' => 'contacto@pacifico.com.pe',
                'phone' => '015183400',
                'insurance_hourly_rate' => 52.00,
                'insurance_panel_rate' => 170.00,
            ],
            [
                'document_number' => '20100194372',
                'business_name' => 'Mapfre Perú',
                'email' => 'contacto@mapfre.com.pe',
                'phone' => '016157800',
                'insurance_hourly_rate' => 50.00,
                'insurance_panel_rate' => 165.00,
            ],
            [
                'document_number' => '20100041921',
                'business_name' => 'La Positiva Seguros',
                'email' => 'servicios@lapositiva.com.pe',
                'phone' => '016194000',
                'insurance_hourly_rate' => 48.00,
                'insurance_panel_rate' => 160.00,
            ],
            [
                'document_number' => '20507007631',
                'business_name' => 'Protecta Compañía de Seguros',
                'email' => 'contacto@protecta.com.pe',
                'phone' => '016119600',
                'insurance_hourly_rate' => 47.00,
                'insurance_panel_rate' => 155.00,
            ],
            [
                'document_number' => '20100047226',
                'business_name' => 'Interseguro',
                'email' => 'contacto@interseguro.com.pe',
                'phone' => '016159000',
                'insurance_hourly_rate' => 46.00,
                'insurance_panel_rate' => 150.00,
            ],
        ];

        foreach ($companies as $company) {
            Party::create([
                'type' => 'company',
                'document_type' => 'RUC',
                'document_number' => $company['document_number'],
                'business_name' => $company['business_name'],
                'email' => $company['email'],
                'phone' => $company['phone'],
                'is_insurance_company' => true,
                'insurance_hourly_rate' => $company['insurance_hourly_rate'],
                'insurance_panel_rate' => $company['insurance_panel_rate'],
                'receive_promotions' => false,
                'establishment_id' => 1,
            ]);
        }
    }
}