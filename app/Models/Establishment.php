<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Establishment extends Model
{
    protected $fillable = [
        'name',
        'address',
        'ubigeo_code',
        'phone',
        'celular',
        'email',
        'code',
        'igv_rate',
        'base_currency',
        'prices_include_tax',
        'default_hourly_rate',
        'default_panel_rate',
    ];

    protected $casts = [
        'igv_rate' => 'float',
        'prices_include_tax' => 'boolean',
        'default_hourly_rate' => 'float',
        'default_panel_rate' => 'float',
    ];

    public function ubigeo(): BelongsTo
    {
        return $this->belongsTo(Ubigeo::class, 'ubigeo_code', 'code');
    }

    public function documentSeries(): HasMany
    {
        return $this->hasMany(DocumentSeries::class);
    }

    public function checkIns(): HasMany
    {
        return $this->hasMany(CheckIn::class);
    }
}