<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class InventoryGuide extends Model
{
    use HasFactory, SoftDeletes;

    public const TYPE_CODES = [
        'U2' => ['serie' => 'NIA1', 'label' => 'Guía de Ingreso'],
        'U3' => ['serie' => 'NSA1', 'label' => 'Guía de Salida'],
        'U4' => ['serie' => 'NTA1', 'label' => 'Guía de Transferencia'],
    ];

    public const STATUS_POSTED = 'posted';
    public const STATUS_CANCELLED = 'cancelled';

    protected $fillable = [
        'establishment_id', 'document_series_id',
        'document_type_code', 'document_serie', 'document_number', 'document_sn',
        'movement_reason_code',
        'origin_warehouse_id', 'destination_warehouse_id',
        'provider_id', 'work_order_id', 'purchase_order_id',
        'provider_invoice', 'provider_guide', 'movement_date',
        'status', 'notes', 'created_by', 'updated_by',
    ];

    protected $casts = [
        'document_number' => 'integer',
        'movement_date' => 'date',
    ];

    public function getTypeLabelAttribute(): string
    {
        return self::TYPE_CODES[$this->document_type_code]['label'] ?? $this->document_type_code;
    }

    public function getSeriePrefixAttribute(): string
    {
        return self::TYPE_CODES[$this->document_type_code]['serie'] ?? $this->document_serie;
    }

    public function documentSeries() { return $this->belongsTo(DocumentSeries::class); }
    public function movementReason() { return $this->belongsTo(InventoryMovementReason::class, 'movement_reason_code', 'code'); }
    public function originWarehouse() { return $this->belongsTo(Warehouse::class, 'origin_warehouse_id'); }
    public function destinationWarehouse() { return $this->belongsTo(Warehouse::class, 'destination_warehouse_id'); }
    public function provider() { return $this->belongsTo(Party::class, 'provider_id'); }
    public function workOrder() { return $this->belongsTo(WorkOrder::class); }
    public function purchaseOrder() { return $this->belongsTo(PurchaseOrder::class); }
    public function establishment() { return $this->belongsTo(Establishment::class); }
    public function movements() { return $this->hasMany(StockMovement::class); }
}
