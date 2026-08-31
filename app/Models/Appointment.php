<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class Appointment extends Model
{
    use HasFactory;
    use SoftDeletes;
    use LogsActivity;

    public const STATUS_LABELS = [
        'scheduled' => 'Agendada',
        'confirmed' => 'Confirmada',
        'cancelled' => 'Cancelada',
        'completed' => 'Realizada',
    ];

    public const STATUS_BADGES = [
        'scheduled' => 'bg-blue-50 text-blue-700',
        'confirmed' => 'bg-green-50 text-green-700',
        'cancelled' => 'bg-red-50 text-red-700',
        'completed' => 'bg-gray-100 text-gray-600',
    ];

    protected $fillable = [
        'establishment_id',
        'vehicle_id',
        'party_id',
        'advisor_id',
        'contact_name',
        'contact_phone',
        'contact_email',
        'scheduled_at',
        'service_type',
        'reason',
        'status',
        'check_in_id',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'scheduled_at' => 'datetime',
    ];

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly([
                'establishment_id', 'vehicle_id', 'party_id', 'advisor_id',
                'contact_name', 'contact_phone', 'contact_email',
                'scheduled_at', 'service_type', 'reason', 'status', 'check_in_id',
            ])
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs()
            ->useLogName('appointment');
    }

    public function getStatusLabelAttribute(): string
    {
        return self::STATUS_LABELS[$this->status] ?? $this->status ?? '';
    }

    public function getServiceTypeLabelAttribute(): string
    {
        return CheckIn::SERVICE_TYPES[$this->service_type] ?? $this->service_type ?? '';
    }

    public function getScheduledAtDisplayAttribute(): string
    {
        return $this->scheduled_at?->format('d/m/Y H:i') ?? '';
    }

    public function vehicle()
    {
        return $this->belongsTo(Vehicle::class)->withTrashed();
    }

    public function party()
    {
        return $this->belongsTo(Party::class);
    }

    public function advisor()
    {
        return $this->belongsTo(User::class, 'advisor_id');
    }

    public function checkIn()
    {
        return $this->belongsTo(CheckIn::class, 'check_in_id')->withTrashed();
    }

    public function establishment()
    {
        return $this->belongsTo(Establishment::class);
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
