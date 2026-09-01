<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DispatchItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'dispatch_id',
        'codigo_interno',
        'description',
        'quantity',
        'uom',
        'sort_order',
    ];

    protected $casts = [
        'quantity' => 'float',
        'sort_order' => 'integer',
    ];

    public function dispatch()
    {
        return $this->belongsTo(Dispatch::class);
    }
}
