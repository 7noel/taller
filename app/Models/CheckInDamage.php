<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CheckInDamage extends Model
{
    use HasFactory;

    protected $fillable = [
        'check_in_id',
        'damage_type',
        'side',
        'pos_x',
        'pos_y',
        'notes',
    ];

    protected $casts = [
        'pos_x' => 'integer',
        'pos_y' => 'integer',
    ];

    public const DAMAGE_TYPE_LABELS = [
        'scratch' => 'Rayón',
        'dent' => 'Abolladura',
        'crack' => 'Quiñe',
    ];

    public const SIDE_LABELS = [
        'front' => 'Frente',
        'rear' => 'Posterior',
        'left' => 'Lateral izquierdo',
        'right' => 'Lateral derecho',
        'top' => 'Techo',
    ];

    public function getDamageTypeLabelAttribute(): string
    {
        return self::DAMAGE_TYPE_LABELS[$this->damage_type] ?? $this->damage_type ?? '';
    }

    public function getSideLabelAttribute(): string
    {
        return self::SIDE_LABELS[$this->side] ?? $this->side ?? '';
    }

    public function checkIn()
    {
        return $this->belongsTo(CheckIn::class);
    }
}