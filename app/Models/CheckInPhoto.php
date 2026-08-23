<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CheckInPhoto extends Model
{
    use HasFactory;

    protected $fillable = [
        'check_in_id',
        'path',
        'order',
        'description',
    ];

    protected $casts = [
        'order' => 'integer',
    ];

    public function checkIn()
    {
        return $this->belongsTo(CheckIn::class);
    }

    public function getUrlAttribute(): string
    {
        return asset('storage/' . $this->path);
    }
}