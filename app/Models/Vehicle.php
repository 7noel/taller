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
        'brand',
        'model',
        'body_type',
        'color',
        'vin',
        'engine_number',
        'year',
        'next_technical_review_date',
        'technical_review_reminder_days',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'next_technical_review_date' => 'date',
        'technical_review_reminder_days' => 'integer',
    ];

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly([
                'plate',
                'brand',
                'model',
                'body_type',
                'color',
                'vin',
                'engine_number',
                'year',
                'next_technical_review_date',
                'technical_review_reminder_days',
            ])
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs()
            ->useLogName('vehicle');
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

    /**
     * The owner party of the vehicle.
     */
    public function owner()
    {
        return $this->hasOne(VehicleRelationship::class)
            ->where('role', 'owner');
    }

    /**
     * The primary commercial contact.
     */
    public function primaryCommercial()
    {
        return $this->hasOne(VehicleRelationship::class)
            ->where('is_primary_commercial', true);
    }
}