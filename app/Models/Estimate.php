<?php

namespace App\Models;

use App\Models\Concerns\HasStatusHistory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class Estimate extends Model
{
    use HasFactory;
    use SoftDeletes;
    use LogsActivity;
    use HasStatusHistory;

    protected $fillable = [
        'check_in_id',
        'vehicle_id',
        'client_id',
        'insurance_company_id',
        'claim_number',
        'service_type',
        'advisor_id',
        'establishment_id',
        'document_series_id',
        'document_type_code',
        'document_serie',
        'document_number',
        'document_sn',
        'work_days',
        'contact_name',
        'contact_phone',
        'contact_email',
        'comments',
        'hourly_rate',
        'panel_rate',
        'currency',
        'exchange_rate',
        'global_discount_type',
        'global_discount_value',
        'subtotal',
        'discount',
        'taxable_base',
        'iva',
        'total',
        'franchise_minimum_amount',
        'franchise_percentage',
        'franchise_minimum_includes_tax',
        'franchise_minimum_without_tax',
        'franchise_base',
        'franchise_percentage_applied',
        'franchise_amount',
        'status',
        'work_order_id',
        'parent_estimate_id',
        'created_by',
        'updated_by',
        'approved_by_user_id',
        'approved_by_recipient',
        'approved_by_phone',
        'approved_at',
        'rejected_by_user_id',
        'rejected_by_recipient',
        'rejected_by_phone',
        'rejection_reason',
        'rejected_at',
        'insurance_approved_by_user_id',
        'insurance_approved_at',
        'insurance_rejected_by_user_id',
        'insurance_rejected_at',
        'insurance_rejection_reason',
        'last_sent_to',
        'last_sent_to_phone',
        'last_sent_at',
    ];

    protected $casts = [
        'document_number' => 'integer',
        'work_days' => 'integer',
        'hourly_rate' => 'float',
        'panel_rate' => 'float',
        'exchange_rate' => 'float',
        'global_discount_value' => 'float',
        'subtotal' => 'float',
        'discount' => 'float',
        'taxable_base' => 'float',
        'iva' => 'float',
        'total' => 'float',
        'franchise_minimum_amount' => 'float',
        'franchise_percentage' => 'float',
        'franchise_minimum_includes_tax' => 'boolean',
        'franchise_minimum_without_tax' => 'float',
        'franchise_base' => 'float',
        'franchise_percentage_applied' => 'float',
        'franchise_amount' => 'float',
        'approved_at' => 'datetime',
        'rejected_at' => 'datetime',
        'insurance_approved_at' => 'datetime',
        'insurance_rejected_at' => 'datetime',
        'last_sent_at' => 'datetime',
    ];

    public const STATUS_LABELS = [
        'draft' => 'Borrador',
        'sent_insurance' => 'En aprobación seguro',
        'approved_insurance' => 'Aprobado seguro',
        'rejected_insurance' => 'Rechazado seguro',
        'sent_client' => 'En aprobación cliente',
        'approved_client' => 'Aprobado cliente',
        'rejected_client' => 'Rechazado cliente',
        'in_repair' => 'En reparación',
        'finalized' => 'Finalizado',
    ];

    /**
     * Acción que debe realizar el usuario según el estado actual.
     * Se muestra en el tablero Kanban para guiar el flujo.
     */
    public const NEXT_ACTIONS = [
        'draft' => 'Completar los ítems y enviar al seguro o al cliente.',
        'sent_insurance' => 'Esperando la aprobación del seguro.',
        'approved_insurance' => 'Enviar el presupuesto al cliente para su aprobación.',
        'rejected_insurance' => 'Revisar las observaciones del seguro, corregir y reenviar.',
        'sent_client' => 'Esperando la aprobación del cliente.',
        'approved_client' => 'Generar la orden de trabajo.',
        'rejected_client' => 'Revisar las observaciones del cliente, corregir y reenviar.',
        'in_repair' => 'En reparación: ver la orden de trabajo.',
        'finalized' => 'Presupuesto finalizado: sin acciones pendientes.',
    ];

    public const SUPPLY_SOURCES = [
        'internal' => 'Interno',
        'external' => 'Externo',
        'insurance' => 'Seguro',
    ];

    public const FINAL_STATUSES = ['finalized'];

    /**
     * Estados en los que un presupuesto puede facturarse (guarda contra
     * doble facturación de borradores/rechazados).
     */
    public const BILLABLE_STATUSES = [
        'approved_insurance',
        'approved_client',
        'in_repair',
        'finalized',
    ];

    // Tipos de servicio: misma fuente que el inventario (CheckIn::SERVICE_TYPES).
    // Agrega nuevos tipos en CheckIn::SERVICE_TYPES y aparecerán en ambos módulos.
    public const SERVICE_TYPES = CheckIn::SERVICE_TYPES;

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly([
                'check_in_id', 'vehicle_id', 'client_id', 'insurance_company_id', 'claim_number', 'service_type',
                'advisor_id', 'establishment_id', 'document_type_code', 'document_serie',
                'document_number', 'document_sn', 'work_days', 'contact_name', 'contact_phone',
                'contact_email', 'hourly_rate', 'panel_rate', 'currency', 'exchange_rate',
                'global_discount_type', 'global_discount_value', 'subtotal', 'discount',
                'taxable_base', 'iva', 'total',
                'franchise_minimum_amount', 'franchise_percentage', 'franchise_minimum_includes_tax',
                'franchise_minimum_without_tax', 'franchise_base', 'franchise_percentage_applied',
                'franchise_amount', 'status', 'work_order_id', 'parent_estimate_id', 'insurance_approved_at', 'insurance_rejected_at',
                'insurance_rejection_reason',
            ])
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs()
            ->useLogName('estimate');
    }

    public function getStatusLabelAttribute(): string
    {
        return self::STATUS_LABELS[$this->status] ?? $this->status ?? '';
    }

    public function getFormattedDocumentNumberAttribute(): ?string
    {
        return $this->document_sn;
    }

    public function getServiceTypeLabelAttribute(): string
    {
        return self::SERVICE_TYPES[$this->service_type] ?? $this->service_type ?? '';
    }

    public function getIsFinalAttribute(): bool
    {
        return in_array($this->status, self::FINAL_STATUSES, true);
    }

    public function checkIn()
    {
        return $this->belongsTo(CheckIn::class)->withTrashed();
    }

    /**
     * Orden de trabajo a la que pertenece el presupuesto (una sola OT).
     * La OT puede agrupar presupuestos de distintos check-ins (reingresos).
     */
    public function workOrder()
    {
        return $this->belongsTo(WorkOrder::class);
    }

    /**
     * Presupuesto principal (siniestro) del cual este presupuesto es ampliación.
     */
    public function parent()
    {
        return $this->belongsTo(Estimate::class, 'parent_estimate_id')->withTrashed();
    }

    /**
     * Ampliaciones de este presupuesto (un solo nivel: el padre es la raíz).
     */
    public function ampliaciones()
    {
        return $this->hasMany(Estimate::class, 'parent_estimate_id')->orderBy('document_sn');
    }

    /**
     * True si este presupuesto es una ampliación de otro.
     */
    public function getIsAmpliacionAttribute(): bool
    {
        return $this->parent_estimate_id !== null;
    }

    /**
     * Etiqueta legible de la relación de ampliación (para badges y listados).
     */
    public function getGrupoLabelAttribute(): string
    {
        $root = $this->parent ?? $this;

        if ($this->is_ampliacion) {
            return 'Ampliación de ' . ($root->document_sn ?? 'presupuesto');
        }

        $count = $this->ampliaciones()->count();

        return $count > 0
            ? ($root->document_sn ?? '') . ' · ' . $count . ' ampliación' . ($count === 1 ? '' : 'es')
            : ($root->document_sn ?? '');
    }

    public function vehicle()
    {
        return $this->belongsTo(Vehicle::class)->withTrashed();
    }

    public function client()
    {
        return $this->belongsTo(Party::class, 'client_id');
    }

    public function insuranceCompany()
    {
        return $this->belongsTo(Party::class, 'insurance_company_id');
    }

    public function advisor()
    {
        return $this->belongsTo(User::class, 'advisor_id');
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

    public function items()
    {
        return $this->hasMany(EstimateItem::class)->orderBy('sort_order');
    }

    public function discounts()
    {
        return $this->hasMany(EstimateDiscount::class);
    }

    public function thirdPartyOrders()
    {
        return $this->hasMany(ThirdPartyOrder::class)->orderBy('id');
    }

    public function invoices()
    {
        return $this->belongsToMany(Invoice::class, 'invoice_estimate')->withTimestamps();
    }

    public function payments()
    {
        return $this->morphMany(Payment::class, 'payable');
    }

    public function followUps()
    {
        return $this->hasMany(FollowUp::class);
    }

    public function approvedBy()
    {
        return $this->belongsTo(User::class, 'approved_by_user_id');
    }

    public function rejectedBy()
    {
        return $this->belongsTo(User::class, 'rejected_by_user_id');
    }

    public function insuranceApprovedBy()
    {
        return $this->belongsTo(User::class, 'insurance_approved_by_user_id');
    }

    public function insuranceRejectedBy()
    {
        return $this->belongsTo(User::class, 'insurance_rejected_by_user_id');
    }

    /**
     * Etiqueta legible de la aprobación del seguro (quién y fecha registrada).
     */
    public function getInsuranceApprovalLabelAttribute(): string
    {
        if ($this->insurance_approved_at) {
            $who = $this->insuranceApprovedBy?->name ?? 'Asesor';

            return 'Aprobado por el seguro el ' . $this->insurance_approved_at->format('d/m/Y') . ' · ' . $who;
        }

        return 'Sin aprobación de seguro registrada';
    }

    /**
     * Etiqueta legible del rechazo del seguro (quién, fecha y motivo).
     */
    public function getInsuranceRejectionLabelAttribute(): string
    {
        if ($this->insurance_rejected_at) {
            $who = $this->insuranceRejectedBy?->name ?? 'Asesor';

            return 'Rechazado por el seguro el ' . $this->insurance_rejected_at->format('d/m/Y') . ' · ' . $who;
        }

        return 'Sin rechazo de seguro registrado';
    }

    /**
     * Etiqueta legible de quién aprobó el gate del cliente (usuario o vía portal).
     */
    public function getApprovedByLabelAttribute(): string
    {
        if ($this->approved_by_user_id) {
            return 'Aprobado por ' . ($this->approvedBy?->name ?? 'usuario del sistema') . ' (asesor)';
        }

        if ($this->approved_by_recipient) {
            $label = 'Aprobado por ' . $this->approved_by_recipient . ' (cliente, vía WhatsApp)';
            return $this->approved_at ? $label . ' · ' . $this->approved_at->format('d/m/Y H:i') : $label;
        }

        return 'Sin aprobación de cliente registrada';
    }

    /**
     * Etiqueta legible de quién rechazó el gate del cliente.
     */
    public function getRejectedByLabelAttribute(): string
    {
        if ($this->rejected_by_user_id) {
            return 'Rechazado por ' . ($this->rejectedBy?->name ?? 'usuario del sistema') . ' (asesor)';
        }

        if ($this->rejected_by_recipient) {
            $label = 'Rechazado por ' . $this->rejected_by_recipient . ' (cliente, vía WhatsApp)';
            return $this->rejected_at ? $label . ' · ' . $this->rejected_at->format('d/m/Y H:i') : $label;
        }

        return 'Sin rechazo de cliente registrado';
    }
}
