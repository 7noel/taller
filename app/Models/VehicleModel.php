<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class VehicleModel extends Model
{
    use SoftDeletes;

    protected $table = 'models';

    protected $fillable = ['brand_id', 'name', 'created_by', 'updated_by'];

    public function brand()
    {
        return $this->belongsTo(Brand::class, 'brand_id');
    }

    public function vehicles()
    {
        return $this->hasMany(Vehicle::class, 'model_id');
    }
}