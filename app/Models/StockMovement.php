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
        'created_by',
    ];

    protected $casts = [
        'quantity' => 'float',
        'exchange_rate' => 'float',
        'unit_cost' => 'float',
        'total_cost' => 'float',
        'unit_cost_pen' => 'float',
        'total_cost_pen' => 'float',
    ];

    public function part() { return $this->belongsTo(Part::class); }
    public function warehouse() { return $this->belongsTo(Warehouse::class); }
    public function creator() { return $this->belongsTo(User::class, 'created_by'); }
}