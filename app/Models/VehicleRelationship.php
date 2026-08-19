<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class VehicleRelationship extends Model
{
    use HasFactory;
    use SoftDeletes;
    use LogsActivity;

    protected $fillable = [
        'vehicle_id',
        'party_id',
        'role',
        'is_primary_commercial',
        'notes',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'is_primary_commercial' => 'boolean',
    ];

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly([
                'vehicle_id',
                'party_id',
                'role',
                'is_primary_commercial',
                'notes',
            ])
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs()
            ->useLogName('vehicle_relationship');
    }

    public static function roleLabels(): array
    {
        return [
            'owner' => 'Propietario',
            'driver' => 'Conductor',
            'approver' => 'Aprobador',
            'operator' => 'Operador',
            'billing' => 'Facturación',
            'insurance_company' => 'Compañía de seguros',
            'emergency_contact' => 'Contacto de emergencia',
            'other' => 'Otro',
        ];
    }

    public function getRoleLabelAttribute(): string
    {
        return self::roleLabels()[$this->role] ?? ucfirst($this->role ?? 'Sin rol');
    }

    public function vehicle()
    {
        return $this->belongsTo(Vehicle::class);
    }

    public function party()
    {
        return $this->belongsTo(Party::class);
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