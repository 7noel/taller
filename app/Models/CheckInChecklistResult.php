<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CheckInChecklistResult extends Model
{
    use HasFactory;

    protected $fillable = [
        'check_in_id',
        'checklist_item_id',
        'status',
        'observations',
    ];

    public const STATUS_LABELS = [
        'good' => 'Bueno',
        'regular' => 'Regular',
        'bad' => 'Malo',
        'not_applicable' => 'No aplica',
    ];

    public function getStatusLabelAttribute(): string
    {
        return self::STATUS_LABELS[$this->status] ?? $this->status ?? '';
    }

    public function checkIn()
    {
        return $this->belongsTo(CheckIn::class);
    }

    public function checklistItem()
    {
        return $this->belongsTo(CheckInChecklistItem::class);
    }
}