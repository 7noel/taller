<?php

namespace App\Services;

use App\Models\FollowUp;
use Illuminate\Support\Facades\Auth;

class FollowUpService
{
    public function create(array $data): FollowUp
    {
        $data['created_by'] = Auth::id();
        $data['updated_by'] = Auth::id();

        return FollowUp::create($data);
    }

    public function update(FollowUp $followUp, array $data): FollowUp
    {
        $data['updated_by'] = Auth::id();

        $followUp->update($data);

        return $followUp->fresh();
    }

    public function delete(FollowUp $followUp): bool
    {
        return (bool) $followUp->delete();
    }

    public function markDone(FollowUp $followUp): FollowUp
    {
        $followUp->update([
            'done' => true,
            'done_at' => now(),
            'updated_by' => Auth::id(),
        ]);

        return $followUp->fresh();
    }
}
