<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WarehouseStock extends Model
{
    use HasFactory;

    protected $fillable = [
        'part_id', 'warehouse_id', 'quantity', 'average_cost',
    ];

    protected $casts = [
        'quantity' => 'float',
        'average_cost' => 'float',
    ];

    public function part() { return $this->belongsTo(Part::class); }
    public function warehouse() { return $this->belongsTo(Warehouse::class); }
}