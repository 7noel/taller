<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class Vehicle extends Model
{
    use HasFactory;
    use SoftDeletes;
    use LogsActivity;

    protected $fillable = [
        'plate', 'brand_id', 'model_id', 'color', 'vin', 'engine_number',
        'year', 'body_type', 'soat_expiration', 'technical_review_date', 'review_reminder_days',
        'last_maintenance_date', 'last_maintenance_mileage', 'next_maintenance_date',
        'maintenance_reminder_days', 'maintenance_source',
        'access_token', 'access_token_created_at',
        'created_by', 'updated_by',
    ];

    protected $casts = [
        'soat_expiration' => 'date',
        'technical_review_date' => 'date',
        'review_reminder_days' => 'integer',
        'last_maintenance_date' => 'date',
        'last_maintenance_mileage' => 'integer',
        'next_maintenance_date' => 'date',
        'maintenance_reminder_days' => 'integer',
        'access_token_created_at' => 'datetime',
    ];

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly([
                'plate', 'brand_id', 'model_id', 'color', 'vin', 'engine_number',
                'year', 'body_type', 'soat_expiration', 'technical_review_date', 'review_reminder_days',
                'last_maintenance_date', 'last_maintenance_mileage', 'next_maintenance_date',
                'maintenance_reminder_days', 'maintenance_source',
            ])
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs()
            ->useLogName('vehicle');
    }

    // Mutadores: guardar en mayúsculas
    public function setPlateAttribute($value): void
    {
        $this->attributes['plate'] = mb_strtoupper($value ?? '');
    }

    public function setVinAttribute($value): void
    {
        $this->attributes['vin'] = $value ? mb_strtoupper($value) : null;
    }

    public function setEngineNumberAttribute($value): void
    {
        $this->attributes['engine_number'] = $value ? mb_strtoupper($value) : null;
    }

    public function setColorAttribute($value): void
    {
        $this->attributes['color'] = $value ? mb_strtoupper($value) : null;
    }

    public function brand()
    {
        return $this->belongsTo(Brand::class, 'brand_id');
    }

    public function vehicleModel()
    {
        return $this->belongsTo(VehicleModel::class, 'model_id');
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updater()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    public function relationships()
    {
        return $this->hasMany(VehicleRelationship::class);
    }

    public function parties()
    {
        return $this->belongsToMany(Party::class, 'vehicle_relationships')
            ->withPivot(['role', 'is_primary_commercial', 'notes'])
            ->wherePivotNull('deleted_at')
            ->withTimestamps();
    }

    public function owner()
    {
        return $this->hasOne(VehicleRelationship::class)->where('role', 'owner');
    }

    public function checkIns()
    {
        return $this->hasMany(CheckIn::class);
    }

    public function estimates()
    {
        return $this->hasMany(Estimate::class);
    }

    /**
     * Genera un token de acceso público único para el portal del cliente.
     * Se guarda en la columna access_token de vehicles (token plano, no hasheado)
     * para poder reconstruir y copiar el enlace en cualquier momento.
     */
    public static function generateAccessToken(): string
    {
        return \Illuminate\Support\Str::random(64);
    }

    /**
     * Enlace público del portal del vehículo (ej. {APP_URL}/c/Ab3xY...).
     */
    public function getPublicLinkAttribute(): ?string
    {
        return $this->access_token ? url('/c/' . $this->access_token) : null;
    }

    public function approvalLogs()
    {
        return $this->hasMany(PublicApprovalLog::class);
    }
}