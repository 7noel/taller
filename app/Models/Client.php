<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Client extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'document_type',
        'document_number',
        'business_name',
        'ubigeo_code',
        'address',
        'phone',
        'mobile',
        'email',
        'is_insurance_company',
        'insurance_hourly_rate',
        'insurance_panel_rate',
        'establishment_id',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'is_insurance_company' => 'boolean',
        'insurance_hourly_rate' => 'decimal:2',
        'insurance_panel_rate' => 'decimal:2',
    ];

    public function ubigeo()
    {
        return $this->belongsTo(Ubigeo::class, 'ubigeo_code', 'code');
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

    public function vehicles()
    {
        return $this->hasMany(Vehicle::class);
    }
}