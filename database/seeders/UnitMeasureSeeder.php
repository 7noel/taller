<?php

namespace Database\Seeders;

use App\Models\UnitMeasure;
use Illuminate\Database\Seeder;

class UnitMeasureSeeder extends Seeder
{
    public function run(): void
    {
        $units = [
            ['code' => 'NIU', 'name' => 'Unidad'],
            ['code' => 'PZ', 'name' => 'Pieza'],
            ['code' => 'ZZ', 'name' => 'Servicio'],
            ['code' => 'KGM', 'name' => 'Kilogramo'],
            ['code' => 'GRM', 'name' => 'Gramo'],
            ['code' => 'LBR', 'name' => 'Libra'],
            ['code' => 'TNE', 'name' => 'Tonelada métrica'],
            ['code' => 'MTR', 'name' => 'Metro'],
            ['code' => 'MMT', 'name' => 'Milímetro'],
            ['code' => 'MTK', 'name' => 'Metro cuadrado'],
            ['code' => 'MTQ', 'name' => 'Metro cúbico'],
            ['code' => 'LTR', 'name' => 'Litro'],
            ['code' => 'HUR', 'name' => 'Hora'],
            ['code' => 'DIA', 'name' => 'Día'],
            ['code' => 'SEM', 'name' => 'Semana'],
            ['code' => 'MES', 'name' => 'Mes'],
            ['code' => 'SET', 'name' => 'Set'],
            ['code' => 'C62', 'name' => 'Ciento'],
        ];

        foreach ($units as $unit) {
            UnitMeasure::updateOrCreate(['code' => $unit['code']], [
                'name' => $unit['name'],
                'is_active' => true,
            ]);
        }
    }
}
