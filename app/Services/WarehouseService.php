<?php

namespace App\Services;

use App\Models\Warehouse;
use Illuminate\Support\Facades\Auth;

class WarehouseService
{
    public function create(array $data): Warehouse
    {
        $data['created_by'] = Auth::id();
        $data['updated_by'] = Auth::id();

        return Warehouse::create($data);
    }

    public function update(Warehouse $warehouse, array $data): Warehouse
    {
        $data['updated_by'] = Auth::id();
        $warehouse->update($data);

        return $warehouse;
    }

    public function delete(Warehouse $warehouse): bool
    {
        return $warehouse->delete();
    }
}