<?php

namespace Database\Seeders;

use App\Models\CompanySetting;
use Illuminate\Database\Seeder;

class CompanySettingSeeder extends Seeder
{
    public function run(): void
    {
        CompanySetting::updateOrCreate(
            ['id' => 1],
            [
                'ruc' => '20123456789',
                'razon_social' => 'Taller Mecánico El Motor S.A.C.',
                'nombre_comercial' => 'Taller Mecánico',
                'direccion' => 'Av. Principal 123, San Isidro',
                'ubigeo_code' => '150101',
                'telefono' => '011234567',
                'celular' => '987654321',
                'email' => 'contacto@tallermotor.com',
                'logo_path' => null,
                'favicon_path' => null,
                'detraccion_account' => '00123456789012',
                'igv_rate' => 0.1800,
                'default_number_source' => 'LOCAL',
                'facturador_provider' => 'local',
                'facturador_api_url' => 'https://facturador.example.com/api',
                'facturador_api_key' => 'demo-facturador-key',
                'facturador_secret' => 'demo-facturador-secret',
                'whatsapp_api_url' => 'https://waba.example.com/api',
                'whatsapp_api_token' => 'demo-whatsapp-token',
                // Guard de control de calidad: true = obligatorio completar asignaciones
                // antes de aprobar el QC; false = solo advertir.
                'qc_require_assignments_completed' => true,
                'camera_capture_mode' => 'integrated',
            ]
        );
    }
}