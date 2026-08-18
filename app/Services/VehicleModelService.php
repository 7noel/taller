<?php

namespace App\Services;

use App\Models\VehicleModel;
use Illuminate\Support\Facades\Auth;

class VehicleModelService
{
    public function findOrCreateModel(int $brandId, string $modelName): VehicleModel
    {
        $modelName = mb_strtoupper(trim($modelName));

        return VehicleModel::firstOrCreate(
            ['brand_id' => $brandId, 'name' => $modelName],
            ['created_by' => Auth::id()]
        );
    }
}