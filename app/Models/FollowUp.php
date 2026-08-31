<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class FollowUp extends Model
{
    use HasFactory;
    use SoftDeletes;
    use LogsActivity;

    public const TYPE_LABELS = [
        'call' => 'Llamada',
        'whatsapp' => 'WhatsApp',
        'email' => 'Email',
        'visit' => 'Visita',
    ];

    protected $fillable = [
        'party_id',
        'vehicle_id',
        'date',
        'type',
        'notes',
        'next_action_date',
        'done',
        'done_at',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'date' => 'date',
        'next_action_date' => 'date',
        'done' => 'boolean',
        'done_at' => 'datetime',
    ];

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly([
                'party_id', 'vehicle_id', 'date', 'type', 'notes',
                'next_action_date', 'done', 'done_at',
            ])
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs()
            ->useLogName('follow_up');
    }

    public function getTypeLabelAttribute(): string
    {
        return self::TYPE_LABELS[$this->type] ?? $this->type ?? '';
    }

    public function party()
    {
        return $this->belongsTo(Party::class);
    }

    public function vehicle()
    {
        return $this->belongsTo(Vehicle::class)->withTrashed();
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
