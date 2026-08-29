<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WorkOrderAssignment extends Model
{
    protected $fillable = [
        'work_order_id',
        'substage_id',
        'user_id',
        'hours',
        'cost',
        'status',
        'notes',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'hours' => 'float',
        'cost' => 'float',
    ];

    public const STATUS_LABELS = [
        'pending' => 'Pendiente',
        'in_progress' => 'En progreso',
        'done' => 'Completado',
    ];

    public const TRANSITIONS = [
        'pending' => ['in_progress', 'done'],
        'in_progress' => ['done', 'pending'],
        'done' => ['pending', 'in_progress'],
    ];

    public function getStatusLabelAttribute(): string
    {
        return self::STATUS_LABELS[$this->status] ?? $this->status ?? '';
    }

    public function workOrder()
    {
        return $this->belongsTo(WorkOrder::class);
    }

    public function substage()
    {
        return $this->belongsTo(WorkOrderSubstage::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updater()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }
}
