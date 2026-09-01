<?php

namespace App\Models;

use App\Models\Concerns\HasStatusHistory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class Invoice extends Model
{
    use HasFactory, SoftDeletes, LogsActivity, HasStatusHistory;

    // Tipos de facturación (regla de negocio del taller)
    public const TYPE_ADVANCE = 'advance';
    public const TYPE_FRANCHISE = 'franchise';
    public const TYPE_INSURANCE = 'insurance';
    public const TYPE_REGULAR = 'regular'; // cierre/regularización que agrupa anticipos
    public const TYPE_FREE = 'free';

    public const TYPE_LABELS = [
        'advance' => 'Adelanto',
        'franchise' => 'Franquicia',
        'insurance' => 'Aseguradora',
        'regular' => 'Cierre',
        'free' => 'Libre',
    ];

    public const ORIGIN_ESTIMATE = 'estimate';
    public const ORIGIN_OT = 'ot';
    public const ORIGIN_FREE = 'free';

    public const ORIGIN_LABELS = [
        'estimate' => 'Presupuesto(s)',
        'ot' => 'Orden de trabajo',
        'free' => 'Libre',
    ];

    // Códigos SUNAT de tipo de documento
    public const DOC_INVOICE = '01';
    public const DOC_RECEIPT = '03';
    public const DOC_CREDIT_NOTE = '07';
    public const DOC_DEBIT_NOTE = '08';

    public const DOC_LABELS = [
        '01' => 'Factura',
        '03' => 'Boleta',
        '07' => 'Nota de Crédito',
        '08' => 'Nota de Débito',
    ];

    public const STATUS_DRAFT = 'draft';
    public const STATUS_PENDING = 'pending';
    public const STATUS_EMITTED = 'emitted';
    public const STATUS_ACCEPTED = 'accepted';
    public const STATUS_REJECTED = 'rejected';
    public const STATUS_VOIDED = 'voided';

    public const STATUS_LABELS = [
        'draft' => 'Borrador',
        'pending' => 'Pendiente',
        'emitted' => 'Emitida',
        'accepted' => 'Aceptada',
        'rejected' => 'Rechazada',
        'voided' => 'Anulada',
    ];

    protected $fillable = [
        'establishment_id',
        'document_series_id',
        'document_type_code',
        'document_serie',
        'document_number',
        'document_sn',
        'invoice_type',
        'origin',
        'work_order_id',
        'vehicle_id',
        'party_id',
        'related_invoice_id',
        'documento_que_se_modifica_tipo',
        'documento_que_se_modifica_serie',
        'documento_que_se_modifica_numero',
        'tipo_de_nota',
        'motivo_nota',
        'provider',
        'sunat_transaction',
        'currency',
        'exchange_rate',
        'subtotal',
        'discount',
        'taxable_base',
        'iva',
        'total',
        'total_advances',
        'observations',
        'status',
        'invoice_date',
        'issued_at',
        'enviar_automaticamente_a_la_sunat',
        'enviar_automaticamente_al_cliente',
        'external_id',
        'accepted_by_sunat',
        'sunat_description',
        'sunat_note',
        'sunat_responsecode',
        'enlace_pdf',
        'enlace_xml',
        'enlace_cdr',
        'cadena_qr',
        'codigo_hash',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'document_number' => 'integer',
        'exchange_rate' => 'float',
        'subtotal' => 'float',
        'discount' => 'float',
        'taxable_base' => 'float',
        'iva' => 'float',
        'total' => 'float',
        'total_advances' => 'float',
        'invoice_date' => 'date',
        'issued_at' => 'datetime',
        'enviar_automaticamente_a_la_sunat' => 'boolean',
        'enviar_automaticamente_al_cliente' => 'boolean',
        'accepted_by_sunat' => 'boolean',
        'documento_que_se_modifica_numero' => 'integer',
    ];

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['status', 'external_id', 'accepted_by_sunat', 'sunat_description', 'document_sn', 'total'])
            ->logOnlyDirty()->dontSubmitEmptyLogs()->useLogName('invoice');
    }

    // Relaciones
    public function establishment()
    {
        return $this->belongsTo(Establishment::class);
    }

    public function documentSeries()
    {
        return $this->belongsTo(DocumentSeries::class);
    }

    public function workOrder()
    {
        return $this->belongsTo(WorkOrder::class);
    }

    public function vehicle()
    {
        return $this->belongsTo(Vehicle::class);
    }

    public function party()
    {
        return $this->belongsTo(Party::class);
    }

    public function relatedInvoice()
    {
        return $this->belongsTo(Invoice::class, 'related_invoice_id');
    }

    public function items()
    {
        return $this->hasMany(InvoiceItem::class)->orderBy('sort_order');
    }

    public function discounts()
    {
        return $this->hasMany(InvoiceDiscount::class);
    }

    public function estimates()
    {
        return $this->belongsToMany(Estimate::class, 'invoice_estimate')->withTimestamps();
    }

    public function payments()
    {
        return $this->morphMany(Payment::class, 'payable');
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updater()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    // Accessors
    public function getTypeLabelAttribute(): string
    {
        return self::TYPE_LABELS[$this->invoice_type] ?? $this->invoice_type;
    }

    public function getOriginLabelAttribute(): string
    {
        return self::ORIGIN_LABELS[$this->origin] ?? $this->origin;
    }

    public function getDocTypeLabelAttribute(): string
    {
        return self::DOC_LABELS[$this->document_type_code] ?? $this->document_type_code;
    }

    public function getStatusLabelAttribute(): string
    {
        return self::STATUS_LABELS[$this->status] ?? $this->status;
    }

    public function getIsCreditNoteAttribute(): bool
    {
        return $this->document_type_code === self::DOC_CREDIT_NOTE;
    }

    public function getIsDebitNoteAttribute(): bool
    {
        return $this->document_type_code === self::DOC_DEBIT_NOTE;
    }

    public function getIsNoteAttribute(): bool
    {
        return in_array($this->document_type_code, [self::DOC_CREDIT_NOTE, self::DOC_DEBIT_NOTE], true);
    }

    /**
     * Monto del descuento global de la factura.
     * Los descuentos por anticipos (código 04) no cuentan como descuento global.
     */
    public function globalDiscountAmount(): float
    {
        $global = $this->discounts->first(fn ($d) => $d->code === '02');

        return (float) ($global?->amount ?? 0);
    }
}

