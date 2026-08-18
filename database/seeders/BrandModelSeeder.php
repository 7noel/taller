<?php

namespace Database\Seeders;

use App\Models\Brand;
use App\Models\VehicleModel;
use Illuminate\Database\Seeder;

class BrandModelSeeder extends Seeder
{
    public function run(): void
    {
        $brands = [
            'TOYOTA' => ['ETIOS', 'YARIS', 'COROLLA', 'RAV4', 'HILUX', 'FORTUNER'],
            'HYUNDAI' => ['ACCENT', 'ELANTRA', 'TUCSON', 'SANTA FE', 'CRETA'],
            'KIA' => ['RIO', 'CERATO', 'SPORTAGE', 'SORENTO'],
            'CHEVROLET' => ['CRUZE', 'MALIBU', 'CAPTIVA', 'TRACKER', 'ONIX'],
            'FORD' => ['FOCUS', 'FIESTA', 'ESCAPE', 'RANGER'],
            'NISSAN' => ['VERSA', 'SENTRA', 'X-TRAIL', 'KICKS', 'FRONTIER'],
            'HONDA' => ['CIVIC', 'ACCORD', 'CR-V', 'HR-V'],
            'VOLKSWAGEN' => ['GOL', 'POLO', 'VENTO', 'T-CROSS', 'TAOS'],
            'MITSUBISHI' => ['LANCER', 'ASX', 'OUTLANDER', 'MONTERO'],
            'MAZDA' => ['3', '6', 'CX-3', 'CX-5'],
            'SUBARU' => ['LEGACY', 'FORESTER', 'XV'],
            'RENAULT' => ['LOGAN', 'SANDERO', 'DUSTER', 'KOLEOS'],
            'PEUGEOT' => ['208', '308', '3008'],
            'CITROËN' => ['C3', 'C4', 'C5'],
            'FIAT' => ['PALIO', 'CRONOS', 'ARGO'],
            'JEEP' => ['CHEROKEE', 'COMPASS', 'RENEGADE'],
            'SUZUKI' => ['SWIFT', 'VITARA', 'GRAND VITARA'],
        ];

        foreach ($brands as $brandName => $models) {
            $brand = Brand::firstOrCreate(['name' => $brandName]);
            foreach ($models as $modelName) {
                VehicleModel::firstOrCreate([
                    'brand_id' => $brand->id,
                    'name' => $modelName,
                ]);
            }
        }
    }
}