<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ExchangeRate extends Model
{
    protected $fillable = [
        'date',
        'currency',
        'buy_rate',
        'sell_rate',
        'source',
    ];

    protected $casts = [
        'date' => 'date',
        'buy_rate' => 'float',
        'sell_rate' => 'float',
    ];
}
