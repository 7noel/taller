<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PurchaseOrderItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'purchase_order_id', 'part_id', 'quantity', 'unit_cost', 'total_cost', 'uom',
    ];

    protected $casts = [
        'quantity' => 'float',
        'unit_cost' => 'float',
        'total_cost' => 'float',
    ];

    public function purchaseOrder() { return $this->belongsTo(PurchaseOrder::class); }
    public function part() { return $this->belongsTo(Part::class); }
    public function unitMeasure() { return $this->belongsTo(UnitMeasure::class, 'uom', 'code'); }
}
