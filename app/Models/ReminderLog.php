<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ReminderLog extends Model
{
    public const STATUS_PENDING = 'pending';
    public const STATUS_SENT = 'sent';
    public const STATUS_FAILED = 'failed';

    public const STATUS_LABELS = [
        self::STATUS_PENDING => 'Programado',
        self::STATUS_SENT => 'Enviado',
        self::STATUS_FAILED => 'Fallido',
    ];

    protected $fillable = [
        'type', 'target_type', 'target_id', 'trigger_date',
        'recipient_type', 'phone', 'recipient_name', 'message',
        'status', 'error', 'sent_at',
    ];

    protected $casts = [
        'trigger_date' => 'date',
        'sent_at' => 'datetime',
    ];

    public function getStatusLabelAttribute(): string
    {
        return self::STATUS_LABELS[$this->status] ?? $this->status ?? '';
    }
}
