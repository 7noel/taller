<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class VehicleContact extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'vehicle_id',
        'type',
        'name',
        'phone',
        'email',
        'company_name',
        'created_by',
        'updated_by',
    ];

    public function vehicle()
    {
        return $this->belongsTo(Vehicle::class);
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