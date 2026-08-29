<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StockMovement extends Model
{
    use HasFactory;

    public const UPDATED_AT = null;

    protected $fillable = [
        'part_id', 'warehouse_id', 'type', 'quantity',
        'currency', 'exchange_rate',
        'unit_cost', 'total_cost',
        'unit_cost_pen', 'total_cost_pen',
        'document_type', 'document_id', 'reference',
        'movement_reason_code', 'inventory_guide_id', 'purchase_order_id', 'work_order_id',
        'notes', 'created_by',
    ];

    protected $casts = [
        'quantity' => 'float',
        'exchange_rate' => 'float',
        'unit_cost' => 'float',
        'total_cost' => 'float',
        'unit_cost_pen' => 'float',
        'total_cost_pen' => 'float',
    ];

    public const TYPES = [
        'entry' => 'Entrada',
        'exit' => 'Salida',
        'adjustment' => 'Ajuste',
    ];

    public function getTypeLabelAttribute(): string
    {
        return self::TYPES[$this->type] ?? $this->type ?? '';
    }

    public function part() { return $this->belongsTo(Part::class); }
    public function warehouse() { return $this->belongsTo(Warehouse::class); }
    public function creator() { return $this->belongsTo(User::class, 'created_by'); }
    public function movementReason() { return $this->belongsTo(InventoryMovementReason::class, 'movement_reason_code', 'code'); }
    public function inventoryGuide() { return $this->belongsTo(InventoryGuide::class); }
    public function purchaseOrder() { return $this->belongsTo(PurchaseOrder::class); }
    public function workOrder() { return $this->belongsTo(WorkOrder::class); }

    /**
     * Documento que origina el movimiento (para el kardex).
     */
    public function getDocumentSnAttribute(): ?string
    {
        if ($this->inventoryGuide) {
            return $this->inventoryGuide->document_sn;
        }
        if ($this->purchaseOrder) {
            return $this->purchaseOrder->document_sn;
        }
        if ($this->workOrder) {
            return $this->workOrder->document_sn;
        }
        return null;
    }
}