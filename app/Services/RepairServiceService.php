<?php

namespace App\Services;

use App\Models\RepairService;
use Illuminate\Support\Facades\Auth;

class RepairServiceService
{
    public function create(array $data): RepairService
    {
        $data['created_by'] = Auth::id();
        $data['updated_by'] = Auth::id();

        return RepairService::create($data);
    }

    public function update(RepairService $repairService, array $data): RepairService
    {
        $data['updated_by'] = Auth::id();
        $repairService->update($data);

        return $repairService;
    }

    public function delete(RepairService $repairService): bool
    {
        return $repairService->delete();
    }
}