<?php

namespace App\Models;

use App\Models\Concerns\HasStatusHistory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class Dispatch extends Model
{
    use HasFactory, SoftDeletes, LogsActivity, HasStatusHistory;

    public const TYPE_REMITENTE = 'remitente';
    public const TYPE_TRANSPORTISTA = 'transportista';

    public const TYPE_LABELS = [
        'remitente' => 'Guía Remitente',
        'transportista' => 'Guía Transportista',
    ];

    // Motivos de traslado SUNAT (catálogo 20)
    public const MOTIVOS_TRASLADO = [
        '01' => 'Venta',
        '02' => 'Traslado entre establecimientos de la misma empresa',
        '04' => 'Traslado por emisor itinerante de comprobante',
        '08' => 'Importación',
        '09' => 'Exportación',
        '13' => 'Otros',
        '14' => 'Venta sujeta a confirmación del comprador',
        '19' => 'Traslado por mercancía no vendida',
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
        'dispatch_type',
        'party_id',
        'vehicle_id',
        'motivo_traslado',
        'descripcion_motivo_traslado',
        'modo_transporte',
        'fecha_de_traslado',
        'fecha_de_entrega',
        'peso_total',
        'unidad_peso',
        'numero_de_bultos',
        'punto_partida_ubigeo',
        'punto_partida_direccion',
        'punto_partida_codigo_establecimiento',
        'punto_llegada_ubigeo',
        'punto_llegada_direccion',
        'punto_llegada_codigo_establecimiento',
        'transportista_documento_tipo',
        'transportista_documento_numero',
        'transportista_denominacion',
        'conductor_documento_tipo',
        'conductor_documento_numero',
        'conductor_nombre',
        'conductor_apellidos',
        'conductor_numero_licencia',
        'vehiculo_placa',
        'vehiculo_marca',
        'vehiculo_modelo',
        'invoice_id',
        'provider',
        'status',
        'observations',
        'issued_at',
        'external_id',
        'accepted_by_sunat',
        'sunat_description',
        'sunat_note',
        'sunat_responsecode',
        'enlace_pdf',
        'enlace_xml',
        'enlace_cdr',
        'codigo_hash',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'document_number' => 'integer',
        'fecha_de_traslado' => 'date',
        'fecha_de_entrega' => 'date',
        'peso_total' => 'float',
        'numero_de_bultos' => 'integer',
        'issued_at' => 'datetime',
        'accepted_by_sunat' => 'boolean',
    ];

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['status', 'external_id', 'document_sn', 'motivo_traslado', 'fecha_de_traslado'])
            ->logOnlyDirty()->dontSubmitEmptyLogs()->useLogName('dispatch');
    }

    public function establishment()
    {
        return $this->belongsTo(Establishment::class);
    }

    public function documentSeries()
    {
        return $this->belongsTo(DocumentSeries::class);
    }

    public function party()
    {
        return $this->belongsTo(Party::class);
    }

    public function vehicle()
    {
        return $this->belongsTo(Vehicle::class);
    }

    public function invoice()
    {
        return $this->belongsTo(Invoice::class);
    }

    public function items()
    {
        return $this->hasMany(DispatchItem::class)->orderBy('sort_order');
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updater()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    public function getTypeLabelAttribute(): string
    {
        return self::TYPE_LABELS[$this->dispatch_type] ?? $this->dispatch_type;
    }

    public function getStatusLabelAttribute(): string
    {
        return self::STATUS_LABELS[$this->status] ?? $this->status;
    }

    public function getMotivoTrasladoLabelAttribute(): string
    {
        return self::MOTIVOS_TRASLADO[$this->motivo_traslado] ?? $this->motivo_traslado;
    }
}
