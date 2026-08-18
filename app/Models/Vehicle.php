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
        'plate',
        'model_id',
        'color',
        'vin',
        'engine_number',
        'year',
        'body_type',
        'technical_review_date',
        'review_reminder_days',
        'establishment_id',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'technical_review_date' => 'date',
        'review_reminder_days' => 'integer',
    ];

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly([
                'plate',
                'model_id',
                'color',
                'vin',
                'engine_number',
                'year',
                'body_type',
                'technical_review_date',
                'review_reminder_days',
                'establishment_id',
            ])
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs()
            ->useLogName('vehicle');
    }

    public function vehicleModel()
    {
        return $this->belongsTo(VehicleModel::class, 'model_id');
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

    public function primaryCommercial()
    {
        return $this->hasOne(VehicleRelationship::class)->where('is_primary_commercial', true);
    }
}