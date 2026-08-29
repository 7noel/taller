<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InventoryMovementReason extends Model
{
    use HasFactory;

    protected $primaryKey = 'code';
    public $incrementing = false;
    protected $keyType = 'string';

    public $timestamps = false;

    public const TYPES = [
        'input' => 'Ingreso',
        'output' => 'Salida',
    ];

    protected $fillable = ['code', 'name', 'type'];

    public function getTypeLabelAttribute(): string
    {
        return self::TYPES[$this->type] ?? $this->type ?? '';
    }
}
