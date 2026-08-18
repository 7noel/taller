<?php

namespace App\Services;

use App\Models\Brand;
use Illuminate\Support\Facades\Auth;

class BrandService
{
    public function findOrCreateBrand(string $name): Brand
    {
        $name = mb_strtoupper(trim($name));

        return Brand::firstOrCreate(
            ['name' => $name],
            ['created_by' => Auth::id()]
        );
    }
}