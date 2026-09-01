<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CashRegister extends Model
{
    use HasFactory, SoftDeletes;

    public const STATUS_OPEN = 'open';
    public const STATUS_CLOSED = 'closed';

    public const STATUS_LABELS = [
        'open' => 'Abierta',
        'closed' => 'Cerrada',
    ];

    protected $fillable = [
        'establishment_id',
        'name',
        'opening_date',
        'opening_amount',
        'closing_date',
        'closing_amount',
        'expected_amount',
        'notes',
        'status',
        'opened_by',
        'closed_by',
    ];

    protected $casts = [
        'opening_date' => 'datetime',
        'opening_amount' => 'float',
        'closing_date' => 'datetime',
        'closing_amount' => 'float',
        'expected_amount' => 'float',
    ];

    public function establishment()
    {
        return $this->belongsTo(Establishment::class);
    }

    public function movements()
    {
        return $this->hasMany(CashMovement::class);
    }

    public function payments()
    {
        return $this->hasMany(Payment::class);
    }

    public function openedBy()
    {
        return $this->belongsTo(User::class, 'opened_by');
    }

    public function closedBy()
    {
        return $this->belongsTo(User::class, 'closed_by');
    }

    public function getStatusLabelAttribute(): string
    {
        return self::STATUS_LABELS[$this->status] ?? $this->status;
    }
}
