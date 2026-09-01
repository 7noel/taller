<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ThirdPartyOrder extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $fillable = [
        'estimate_id',
        'description',
        'amount_without_iva',
        'provider_name',
        'currency',
        'exchange_rate',
    ];

    protected $casts = [
        'amount_without_iva' => 'float',
        'exchange_rate' => 'float',
    ];

    public function estimate()
    {
        return $this->belongsTo(Estimate::class);
    }
}