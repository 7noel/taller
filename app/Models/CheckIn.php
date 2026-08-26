<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class CheckIn extends Model
{
    use HasFactory;
    use SoftDeletes;
    use LogsActivity;

    protected $fillable = [
        'vehicle_id',
        'client_id',
        'insurance_company_id',
        'establishment_id',
        'document_series_id',
        'document_type_code',
        'document_serie',
        'document_number',
        'document_sn',
        'created_by',
        'updated_by',
        'service_type',
        'claim_number',
        'mileage',
        'fuel_level',
        'property_card',
        'soat_expiration',
        'technical_review_expiration',
        'keys_count',
        'has_remote_control',
        'client_request',
        'observations',
        'status',
        'approved_by_user_id',
        'approved_by_recipient',
        'approved_by_phone',
        'approved_at',
        'rejected_by_user_id',
        'rejected_by_recipient',
        'rejected_by_phone',
        'rejection_reason',
        'rejected_at',
        'last_sent_to',
        'last_sent_to_phone',
        'last_sent_at',
    ];

    protected $casts = [
        'document_number' => 'integer',
        'has_remote_control' => 'boolean',
        'soat_expiration' => 'date',
        'technical_review_expiration' => 'date',
        'keys_count' => 'integer',
        'mileage' => 'integer',
        'approved_at' => 'datetime',
        'rejected_at' => 'datetime',
        'last_sent_at' => 'datetime',
    ];

    public const STATUS_LABELS = [
        'draft' => 'Borrador',
        'pending_approval' => 'Pendiente aprobación cliente',
        'approved' => 'Aprobado',
        'rejected' => 'Rechazado',
        'closed' => 'Cerrado',
    ];

    public const SERVICE_TYPES = [
        'siniestro' => 'Siniestro',
        'preventivo' => 'Preventivo',
        'correctivo' => 'Correctivo',
        'otro' => 'Otro',
    ];

    public const FUEL_LEVELS = [
        'reserva' => 'Reserva',
        'cuarto' => '1/4',
        'medio' => 'Medio',
        'tres_cuartos' => '3/4',
        'full' => 'Full',
    ];

    public const PROPERTY_CARDS = [
        'fisica' => 'Física',
        'virtual' => 'Virtual',
        'no_tiene' => 'No tiene',
    ];

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly([
                'vehicle_id', 'client_id', 'insurance_company_id', 'establishment_id',
                'document_series_id', 'document_type_code', 'document_serie', 'document_number', 'document_sn',
                'service_type', 'claim_number', 'mileage', 'fuel_level', 'property_card',
                'soat_expiration', 'technical_review_expiration', 'keys_count',
                'has_remote_control', 'client_request', 'observations', 'status',
            ])
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs()
            ->useLogName('check_in');
    }

    public function getStatusLabelAttribute(): string
    {
        return self::STATUS_LABELS[$this->status] ?? $this->status ?? '';
    }

    public function getServiceTypeLabelAttribute(): string
    {
        return self::SERVICE_TYPES[$this->service_type] ?? $this->service_type ?? '';
    }

    public function getFuelLevelLabelAttribute(): string
    {
        return self::FUEL_LEVELS[$this->fuel_level] ?? $this->fuel_level ?? '';
    }

    public function getPropertyCardLabelAttribute(): string
    {
        return self::PROPERTY_CARDS[$this->property_card] ?? $this->property_card ?? '';
    }

    /**
     * Documento formateado con el correlativo, ej. 'IV01-000001'.
     * Prefiere la columna snapshot document_sn (SSSS-XXXXXX).
     */
    public function getFormattedDocumentNumberAttribute(): ?string
    {
        return $this->document_sn;
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

    public function checklistResults()
    {
        return $this->hasMany(CheckInChecklistResult::class);
    }

    public function damages()
    {
        return $this->hasMany(CheckInDamage::class);
    }

    public function photos()
    {
        return $this->hasMany(CheckInPhoto::class)->orderBy('order');
    }

    public function estimates()
    {
        return $this->hasMany(Estimate::class, 'check_in_id');
    }

    public function approvedBy()
    {
        return $this->belongsTo(User::class, 'approved_by_user_id');
    }

    public function rejectedBy()
    {
        return $this->belongsTo(User::class, 'rejected_by_user_id');
    }

    /**
     * Etiqueta legible de quién aprobó el inventario (usuario interno o cliente vía portal).
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

        return 'Sin aprobación registrada';
    }

    /**
     * Etiqueta legible de quién rechazó el inventario.
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

        return 'Sin rechazo registrado';
    }
}