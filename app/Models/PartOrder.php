<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PartOrder extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'part_id', 'estimate_id', 'quantity', 'status',
        'ordered_at', 'expected_delivery', 'delivered_at',
        'tracking_number', 'notes', 'created_by', 'updated_by',
    ];

    protected $casts = [
        'quantity' => 'float',
        'ordered_at' => 'date',
        'expected_delivery' => 'date',
        'delivered_at' => 'date',
    ];

    public function part() { return $this->belongsTo(Part::class); }

    // Relación con Estimate pendiente: cuando exista el modelo, descomentar:
    // public function estimate() { return $this->belongsTo(Estimate::class); }

    public function creator() { return $this->belongsTo(User::class, 'created_by'); }
    public function updater() { return $this->belongsTo(User::class, 'updated_by'); }
}