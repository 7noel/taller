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
    ];

    protected $casts = [
        'has_remote_control' => 'boolean',
        'soat_expiration' => 'date',
        'technical_review_expiration' => 'date',
        'keys_count' => 'integer',
        'mileage' => 'integer',
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
}