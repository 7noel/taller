<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PurchaseOrder extends Model
{
    use HasFactory, SoftDeletes;

    public const STATUS_DRAFT = 'draft';
    public const STATUS_ORDERED = 'ordered';
    public const STATUS_RECEIVED = 'received';
    public const STATUS_CANCELLED = 'cancelled';

    public const STATUS_LABELS = [
        'draft' => 'Borrador',
        'ordered' => 'Pedido',
        'received' => 'Recibida',
        'cancelled' => 'Anulada',
    ];

    protected $fillable = [
        'establishment_id', 'document_series_id',
        'document_type_code', 'document_serie', 'document_number', 'document_sn',
        'provider_id', 'warehouse_id',
        'order_date', 'expected_delivery', 'status',
        'currency', 'exchange_rate',
        'subtotal', 'iva', 'total',
        'provider_invoice', 'provider_guide', 'received_at',
        'notes', 'created_by', 'updated_by',
    ];

    protected $casts = [
        'document_number' => 'integer',
        'order_date' => 'date',
        'expected_delivery' => 'date',
        'received_at' => 'date',
        'exchange_rate' => 'float',
        'subtotal' => 'float',
        'iva' => 'float',
        'total' => 'float',
    ];

    public function getStatusLabelAttribute(): string
    {
        return self::STATUS_LABELS[$this->status] ?? $this->status ?? '';
    }

    public function establishment() { return $this->belongsTo(Establishment::class); }
    public function documentSeries() { return $this->belongsTo(DocumentSeries::class); }
    public function provider() { return $this->belongsTo(Party::class, 'provider_id'); }
    public function warehouse() { return $this->belongsTo(Warehouse::class); }
    public function items() { return $this->hasMany(PurchaseOrderItem::class); }
    public function movements() { return $this->hasMany(StockMovement::class, 'purchase_order_id'); }
    public function inventoryGuides() { return $this->hasMany(InventoryGuide::class, 'purchase_order_id'); }
}
