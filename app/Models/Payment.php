<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class Payment extends Model
{
    use HasFactory, SoftDeletes, LogsActivity;

    public const DIRECTION_IN = 'in';
    public const DIRECTION_OUT = 'out';

    public const DIRECTION_LABELS = [
        'in' => 'Ingreso',
        'out' => 'Egreso',
    ];

    protected $fillable = [
        'payable_type',
        'payable_id',
        'party_id',
        'amount',
        'payment_method_id',
        'bank_id',
        'cash_register_id',
        'invoice_id',
        'reference',
        'payment_date',
        'direction',
        'notes',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'amount' => 'float',
        'payment_date' => 'date',
    ];

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['amount', 'payment_method_id', 'bank_id', 'cash_register_id', 'invoice_id', 'reference', 'payment_date', 'direction', 'notes'])
            ->logOnlyDirty()->dontSubmitEmptyLogs()->useLogName('payment');
    }

    public function payable()
    {
        return $this->morphTo();
    }

    public function party()
    {
        return $this->belongsTo(Party::class);
    }

    public function paymentMethod()
    {
        return $this->belongsTo(PaymentMethod::class);
    }

    public function bank()
    {
        return $this->belongsTo(Bank::class);
    }

    public function cashRegister()
    {
        return $this->belongsTo(CashRegister::class);
    }

    public function invoice()
    {
        return $this->belongsTo(Invoice::class);
    }

    public function cashMovements()
    {
        return $this->hasMany(CashMovement::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updater()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    public function getDirectionLabelAttribute(): string
    {
        return self::DIRECTION_LABELS[$this->direction] ?? $this->direction;
    }
}
