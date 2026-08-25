<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EstimateDiscount extends Model
{
    use HasFactory;

    protected $fillable = [
        'estimate_id',
        'source',
        'type',
        'value',
        'amount',
        'applied_to',
        'created_by',
    ];

    protected $casts = [
        'value' => 'float',
        'amount' => 'float',
    ];

    public const SOURCES = [
        'global' => 'Descuento global',
        'line' => 'Descuentos por línea',
        'promotion' => 'Promoción',
        'insurance' => 'Seguro / Franquicia',
        'other' => 'Otro',
    ];

    public const TYPES = [
        'percentage' => 'Porcentaje',
        'fixed' => 'Monto fijo',
    ];

    public const APPLIED_TO = [
        'subtotal' => 'Subtotal',
        'total' => 'Total',
    ];

    public function getSourceLabelAttribute(): string
    {
        return self::SOURCES[$this->source] ?? $this->source ?? '';
    }

    public function estimate()
    {
        return $this->belongsTo(Estimate::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}