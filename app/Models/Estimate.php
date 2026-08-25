<?php

namespace App\Models;

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

    protected $fillable = [
        'check_in_id',
        'vehicle_id',
        'client_id',
        'insurance_company_id',
        'claim_number',
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
        'status',
        'created_by',
        'updated_by',
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

    public const SUPPLY_SOURCES = [
        'internal' => 'Interno',
        'external' => 'Externo',
        'insurance' => 'Seguro',
    ];

    public const FINAL_STATUSES = ['finalized', 'rejected_insurance', 'rejected_client'];

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly([
                'check_in_id', 'vehicle_id', 'client_id', 'insurance_company_id', 'claim_number',
                'advisor_id', 'establishment_id', 'document_type_code', 'document_serie',
                'document_number', 'document_sn', 'work_days', 'contact_name', 'contact_phone',
                'contact_email', 'hourly_rate', 'panel_rate', 'currency', 'exchange_rate',
                'global_discount_type', 'global_discount_value', 'subtotal', 'discount',
                'taxable_base', 'iva', 'total', 'status',
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

    public function getIsFinalAttribute(): bool
    {
        return in_array($this->status, self::FINAL_STATUSES, true);
    }

    public function checkIn()
    {
        return $this->belongsTo(CheckIn::class)->withTrashed();
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

    public function statusHistory()
    {
        return $this->hasMany(EstimateStatusHistory::class)->orderBy('created_at');
    }

    public function discounts()
    {
        return $this->hasMany(EstimateDiscount::class);
    }

    // Nota: la relación hasOne(FranchiseCalculation) se añadirá cuando exista el
    // modelo de franquicia (módulo posterior). El presupuesto actual no la requiere.
}