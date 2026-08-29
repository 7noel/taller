<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CheckInChecklistItem extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $fillable = [
        'name',
        'category',
        'order',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'order' => 'integer',
    ];

    public const CATEGORIES = [
        'EXTERIOR' => 'Exterior',
        'MOTOR' => 'Motor',
        'INTERIOR' => 'Interior',
        'HERRAMIENTAS/EMERGENCIA' => 'Herramientas / Emergencia',
    ];

    public function getCategoryLabelAttribute(): string
    {
        return self::CATEGORIES[$this->category] ?? $this->category ?? '';
    }

    public function checklistResults()
    {
        return $this->hasMany(CheckInChecklistResult::class);
    }
}