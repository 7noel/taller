<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class Warehouse extends Model
{
    use HasFactory, SoftDeletes, LogsActivity;

    protected $fillable = [
        'establishment_id', 'name', 'code', 'location', 'is_active',
        'created_by', 'updated_by',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['establishment_id', 'name', 'code', 'location', 'is_active'])
            ->logOnlyDirty()->dontSubmitEmptyLogs()->useLogName('warehouse');
    }

    public function establishment() { return $this->belongsTo(Establishment::class); }
    public function creator() { return $this->belongsTo(User::class, 'created_by'); }
    public function updater() { return $this->belongsTo(User::class, 'updated_by'); }
    public function stocks() { return $this->hasMany(WarehouseStock::class); }
    public function movements() { return $this->hasMany(StockMovement::class); }
}