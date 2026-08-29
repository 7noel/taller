<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class Part extends Model
{
    use HasFactory, SoftDeletes, LogsActivity;

    protected $fillable = [
        'name', 'description', 'sku', 'manufacturer_code', 'barcode',
        'part_brand_id', 'part_category_id', 'uom', 'min_stock',
        'cost_price', 'cost_currency', 'sell_price', 'currency', 'is_active',
        'created_by', 'updated_by',
    ];

    protected $casts = [
        'min_stock' => 'integer',
        'cost_price' => 'float',
        'sell_price' => 'float',
        'is_active' => 'boolean',
    ];

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['name', 'sku', 'barcode', 'part_brand_id', 'part_category_id', 'uom', 'cost_price', 'cost_currency', 'sell_price', 'currency', 'is_active'])
            ->logOnlyDirty()->dontSubmitEmptyLogs()->useLogName('part');
    }

    public function creator() { return $this->belongsTo(User::class, 'created_by'); }
    public function updater() { return $this->belongsTo(User::class, 'updated_by'); }
    public function brand() { return $this->belongsTo(PartBrand::class, 'part_brand_id'); }
    public function category() { return $this->belongsTo(PartCategory::class, 'part_category_id'); }
    public function unitMeasure() { return $this->belongsTo(UnitMeasure::class, 'uom', 'code'); }
    public function stocks() { return $this->hasMany(WarehouseStock::class); }
    public function movements() { return $this->hasMany(StockMovement::class); }
    public function partOrders() { return $this->hasMany(PartOrder::class); }
}