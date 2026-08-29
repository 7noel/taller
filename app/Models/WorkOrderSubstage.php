<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WorkOrderSubstage extends Model
{
    protected $fillable = [
        'name',
        'description',
        'order',
        'is_active',
    ];

    protected $casts = [
        'order' => 'integer',
        'is_active' => 'boolean',
    ];

    public function assignments()
    {
        return $this->hasMany(WorkOrderAssignment::class);
    }
}
