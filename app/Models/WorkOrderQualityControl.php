<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class WorkOrderQualityControl extends Model
{
    use SoftDeletes;

    public const RESULT_APPROVED = 'approved';
    public const RESULT_REJECTED = 'rejected';

    public const RESULT_LABELS = [
        self::RESULT_APPROVED => 'Aprobado',
        self::RESULT_REJECTED => 'Rechazado',
    ];

    /**
     * Causas de rechazo del control de calidad (lógica de negocio del flujo:
     * rechazado => la OT vuelve a reparación). Fijas por diseño.
     */
    public const REJECTION_REASONS = [
        'technical_failure' => 'Falla técnica en reparación, avería no resuelta total o parcialmente',
        'vehicle_state' => 'Estado inapropiado del vehículo en cuanto a limpieza y desinfección',
        'fluids_incomplete' => 'Fluidos incompletos',
        'assembly_incomplete' => 'Proceso de armado incompleto (faltan piezas)',
    ];

    protected $fillable = [
        'work_order_id',
        'form_template_id',
        'result',
        'rejection_reason',
        'rejection_details',
        'answers',
        'reviewed_by',
        'reviewed_at',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'answers' => 'array',
        'reviewed_at' => 'datetime',
    ];

    public function getResultLabelAttribute(): string
    {
        return self::RESULT_LABELS[$this->result] ?? $this->result ?? '';
    }

    public function getRejectionReasonLabelAttribute(): ?string
    {
        return self::REJECTION_REASONS[$this->rejection_reason] ?? $this->rejection_reason;
    }

    public function workOrder(): BelongsTo
    {
        return $this->belongsTo(WorkOrder::class);
    }

    public function template(): BelongsTo
    {
        return $this->belongsTo(FormTemplate::class, 'form_template_id');
    }

    public function reviewer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reviewed_by');
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
