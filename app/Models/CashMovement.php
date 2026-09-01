<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CashMovement extends Model
{
    use HasFactory;

    public const TYPE_INCOME = 'income';
    public const TYPE_EXPENSE = 'expense';

    public const TYPE_LABELS = [
        'income' => 'Ingreso',
        'expense' => 'Salida',
    ];

    protected $fillable = [
        'cash_register_id',
        'payment_id',
        'type',
        'amount',
        'payment_method_id',
        'bank_id',
        'description',
        'reference',
        'movement_date',
        'created_by',
    ];

    protected $casts = [
        'amount' => 'float',
        'movement_date' => 'date',
    ];

    public function cashRegister()
    {
        return $this->belongsTo(CashRegister::class);
    }

    public function payment()
    {
        return $this->belongsTo(Payment::class);
    }

    public function paymentMethod()
    {
        return $this->belongsTo(PaymentMethod::class);
    }

    public function bank()
    {
        return $this->belongsTo(Bank::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function getTypeLabelAttribute(): string
    {
        return self::TYPE_LABELS[$this->type] ?? $this->type;
    }
}
