<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PartBrand extends Model
{
    protected $fillable = ['name', 'is_active'];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function parts()
    {
        return $this->hasMany(Part::class);
    }
}