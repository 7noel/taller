<?php

namespace App\Models;

use App\Models\Concerns\HasStatusHistory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class WorkOrder extends Model
{
    use HasFactory;
    use SoftDeletes;
    use LogsActivity;
    use HasStatusHistory;

    protected $fillable = [
        'vehicle_id',
        'client_id',
        'establishment_id',
        'document_series_id',
        'document_type_code',
        'document_serie',
        'document_number',
        'document_sn',
        'status',
        'start_date',
        'estimated_end_date',
        'notes',
        'delivered_at',
        'delivered_by',
        'survey_sent_at',
        'survey_sent_to',
        'survey_sent_to_phone',
        'last_sent_to',
        'last_sent_to_phone',
        'last_sent_at',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'document_number' => 'integer',
        'start_date' => 'date',
        'estimated_end_date' => 'date',
        'delivered_at' => 'datetime',
        'survey_sent_at' => 'datetime',
        'last_sent_at' => 'datetime',
    ];

    public const STATUS_LABELS = [
        'open' => 'Abierta',
        'in_progress' => 'En progreso',
        'waiting_parts' => 'En espera de repuestos',
        'quality_control' => 'En control de calidad',
        'ready_for_delivery' => 'Lista para entrega',
        'delivered' => 'Entregada',
        'delivered_pending' => 'Entregado con pendientes',
        'closed' => 'Cerrada',
    ];

    public const FINAL_STATUSES = ['closed'];

    /**
     * Acción que debe realizar el usuario según el estado actual de la OT.
     * Se muestra en el listado y en un banner del detalle para guiar el flujo.
     */
    public const NEXT_ACTIONS = [
        'open' => 'Iniciar el trabajo: asignar técnicos y pasar a "En progreso".',
        'in_progress' => 'Completar las asignaciones y enviar a "Control de calidad".',
        'waiting_parts' => 'Esperando repuestos: reanudar el trabajo cuando lleguen.',
        'quality_control' => 'Realizar el control de calidad: aprobar o rechazar la OT.',
        'ready_for_delivery' => 'Avisar al cliente que su vehículo está listo para recoger.',
        'delivered' => 'Enviar la encuesta de satisfacción y cerrar la OT.',
        'delivered_pending' => 'Pendiente por repuestos: reanudar el trabajo para completar la entrega.',
        'closed' => 'OT cerrada: sin acciones pendientes.',
    ];

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly([
                'vehicle_id', 'client_id', 'establishment_id',
                'document_series_id', 'document_type_code', 'document_serie',
                'document_number', 'document_sn', 'status',
                'start_date', 'estimated_end_date', 'notes',
            ])
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs()
            ->useLogName('work_order');
    }

    public function getStatusLabelAttribute(): string
    {
        return self::STATUS_LABELS[$this->status] ?? $this->status ?? '';
    }

    public function getFormattedDocumentNumberAttribute(): ?string
    {
        return $this->document_sn;
    }

    public function getIsFinalAttribute(): bool
    {
        return in_array($this->status, self::FINAL_STATUSES, true);
    }

    public function getNextActionAttribute(): string
    {
        return self::NEXT_ACTIONS[$this->status] ?? 'Sin acciones pendientes.';
    }

    public function vehicle()
    {
        return $this->belongsTo(Vehicle::class)->withTrashed();
    }

    public function client()
    {
        return $this->belongsTo(Party::class, 'client_id');
    }

    public function establishment()
    {
        return $this->belongsTo(Establishment::class);
    }

    public function documentSeries()
    {
        return $this->belongsTo(DocumentSeries::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updater()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    /**
     * Presupuestos que componen el alcance de la OT.
     * Pueden venir de distintos check-ins (reingresos o adicionales aprobados).
     */
    public function estimates()
    {
        return $this->hasMany(Estimate::class, 'work_order_id');
    }

    public function invoices()
    {
        return $this->hasMany(Invoice::class);
    }

    /**
     * Visitas físicas vinculadas a la OT: el check-in original y los reingresos
     * (por ejemplo, retorno para instalar un repuesto pendiente).
     */
    public function checkIns()
    {
        return $this->hasMany(CheckIn::class, 'work_order_id');
    }

    public function assignments()
    {
        return $this->hasMany(WorkOrderAssignment::class)->orderBy('id');
    }

    /**
     * Comprobantes de servicio tercerizado (vales CST01) de la OT.
     */
    public function serviceVouchers()
    {
        return $this->hasMany(ServiceVoucher::class)->orderBy('execution_date')->orderBy('id');
    }

    public function qualityControls()
    {
        return $this->hasMany(WorkOrderQualityControl::class)->orderByDesc('id');
    }

    public function satisfactionSurvey()
    {
        return $this->hasOne(WorkOrderSatisfactionSurvey::class)->latestOfMany();
    }

    public function deliveredBy()
    {
        return $this->belongsTo(User::class, 'delivered_by');
    }
}
