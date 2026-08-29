<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EstimateItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'estimate_id',
        'service_id',
        'part_id',
        'item_type',
        'service_category_id',
        'part_category_id',
        'description',
        'quantity',
        'unit_price',
        'uom',
        'discount_pct',
        'subtotal',
        'discount_amount',
        'net_line',
        'iva_line',
        'total_line',
        'supply_source',
        'cost_price',
        'sort_order',
    ];

    protected $casts = [
        'quantity' => 'float',
        'unit_price' => 'float',
        'discount_pct' => 'float',
        'subtotal' => 'float',
        'discount_amount' => 'float',
        'net_line' => 'float',
        'iva_line' => 'float',
        'total_line' => 'float',
        'cost_price' => 'float',
        'sort_order' => 'integer',
    ];

    public const ITEM_TYPES = [
        'service' => 'Servicio',
        'part' => 'Repuesto',
    ];

    public function getItemTypeLabelAttribute(): string
    {
        return self::ITEM_TYPES[$this->item_type] ?? $this->item_type ?? '';
    }

    public function getSupplySourceLabelAttribute(): string
    {
        return Estimate::SUPPLY_SOURCES[$this->supply_source] ?? $this->supply_source ?? '';
    }

    public function estimate()
    {
        return $this->belongsTo(Estimate::class);
    }

    public function service()
    {
        return $this->belongsTo(RepairService::class, 'service_id');
    }

    public function part()
    {
        return $this->belongsTo(Part::class, 'part_id');
    }

    public function unitMeasure()
    {
        return $this->belongsTo(UnitMeasure::class, 'uom', 'code');
    }

    public function serviceCategory()
    {
        return $this->belongsTo(ServiceCategory::class, 'service_category_id');
    }

    public function partCategory()
    {
        return $this->belongsTo(PartCategory::class, 'part_category_id');
    }
}