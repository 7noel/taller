<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class Party extends Model
{
    use HasFactory, SoftDeletes, LogsActivity;

    protected $fillable = [
        'type', 'document_type', 'document_number', 'first_name', 'last_name',
        'business_name', 'email', 'phone', 'mobile', 'address', 'ubigeo_code',
        'is_insurance_company', 'insurance_hourly_rate', 'insurance_panel_rate',
        'receive_promotions', 'created_by', 'updated_by',
    ];

    protected $casts = [
        'is_insurance_company' => 'boolean',
        'receive_promotions' => 'boolean',
    ];

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['type', 'document_type', 'document_number', 'first_name', 'last_name', 'business_name', 'email', 'phone', 'mobile', 'address', 'ubigeo_code', 'is_insurance_company', 'insurance_hourly_rate', 'insurance_panel_rate', 'receive_promotions'])
            ->logOnlyDirty()->dontSubmitEmptyLogs()->useLogName('party');
    }

    public function getDisplayNameAttribute(): string
    {
        return $this->type === 'company' ? ($this->business_name ?? $this->document_number) : trim("{$this->first_name} {$this->last_name}");
    }

    public function ubigeo() { return $this->belongsTo(Ubigeo::class, 'ubigeo_code', 'code'); }
    public function creator() { return $this->belongsTo(User::class, 'created_by'); }
    public function updater() { return $this->belongsTo(User::class, 'updated_by'); }
    public function contacts() { return $this->hasMany(PartyContact::class); }
    public function vehicleRelationships() { return $this->hasMany(VehicleRelationship::class); }
    public function vehicles() { return $this->belongsToMany(Vehicle::class, 'vehicle_relationships')->withPivot(['role', 'is_primary_commercial', 'notes'])->wherePivotNull('deleted_at')->withTimestamps(); }
}