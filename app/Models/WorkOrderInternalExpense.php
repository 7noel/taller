<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

/**
 * Gasto interno asumido por el taller dentro de una Orden de Trabajo
 * (responsabilidad propia: arañazo, repuesto malogrado, otro error).
 *
 * NO genera presupuesto ni factura: registra el evento, el responsable y el
 * monto para que WorkOrderCostService refleje la utilidad real de la OT.
 */
class WorkOrderInternalExpense extends Model
{
    use HasFactory;
    use SoftDeletes;
    use LogsActivity;

    public const TYPES = [
        'scratch' => 'Arañazo / planchado y pintura',
        'damaged_part' => 'Repuesto malogrado',
        'other' => 'Otro',
    ];

    protected $fillable = [
        'work_order_id',
        'type',
        'description',
        'amount',
        'currency',
        'exchange_rate',
        'responsible_user_id',
        'occurred_at',
        'notes',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'amount' => 'float',
        'exchange_rate' => 'float',
        'occurred_at' => 'date',
    ];

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['type', 'description', 'amount', 'currency', 'exchange_rate', 'responsible_user_id', 'occurred_at', 'notes'])
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs()
            ->useLogName('work_order_internal_expense');
    }

    public function getTypeLabelAttribute(): string
    {
        return self::TYPES[$this->type] ?? ($this->type ?? '');
    }

    public function workOrder(): BelongsTo
    {
        return $this->belongsTo(WorkOrder::class);
    }

    public function responsible(): BelongsTo
    {
        return $this->belongsTo(User::class, 'responsible_user_id');
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
