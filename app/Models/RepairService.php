<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class RepairService extends Model
{
    use HasFactory, SoftDeletes, LogsActivity;

    protected $table = 'repair_services';

    protected $fillable = [
        'name', 'description', 'service_category_id', 'pricing_type', 'uom',
        'estimated_hours', 'min_hours',
        'sell_price', 'currency', 'cost_price', 'cost_currency',
        'default_provider_id', 'is_outsourced', 'is_active',
        'created_by', 'updated_by',
    ];

    protected $casts = [
        'estimated_hours' => 'float',
        'min_hours' => 'float',
        'sell_price' => 'float',
        'cost_price' => 'float',
        'is_outsourced' => 'boolean',
        'is_active' => 'boolean',
    ];

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['name', 'service_category_id', 'pricing_type', 'uom', 'sell_price', 'currency', 'cost_price', 'cost_currency', 'default_provider_id', 'is_outsourced', 'is_active'])
            ->logOnlyDirty()->dontSubmitEmptyLogs()->useLogName('repair_service');
    }

    public function provider() { return $this->belongsTo(Party::class, 'default_provider_id'); }
    public function category() { return $this->belongsTo(ServiceCategory::class, 'service_category_id'); }
    public function unitMeasure() { return $this->belongsTo(UnitMeasure::class, 'uom', 'code'); }
    public function creator() { return $this->belongsTo(User::class, 'created_by'); }
    public function updater() { return $this->belongsTo(User::class, 'updated_by'); }
}