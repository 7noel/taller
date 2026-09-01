<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InvoiceItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'invoice_id',
        'estimate_id',
        'codigo_interno',
        'description',
        'quantity',
        'unit_price',
        'price',
        'discount',
        'subtotal',
        'affectation_igv_type',
        'igv',
        'total',
        'uom',
        'codigo_producto_sunat',
        'is_advance_line',
        'advance_invoice_id',
        'sort_order',
    ];

    protected $casts = [
        'quantity' => 'float',
        'unit_price' => 'float',
        'price' => 'float',
        'discount' => 'float',
        'subtotal' => 'float',
        'igv' => 'float',
        'total' => 'float',
        'is_advance_line' => 'boolean',
        'sort_order' => 'integer',
    ];

    public function invoice()
    {
        return $this->belongsTo(Invoice::class);
    }

    public function estimate()
    {
        return $this->belongsTo(Estimate::class);
    }

    public function advanceInvoice()
    {
        return $this->belongsTo(Invoice::class, 'advance_invoice_id');
    }

    public function getUomLabelAttribute(): string
    {
        return $this->uom === 'ZZ' ? 'Servicio' : ($this->uom === 'NIU' ? 'Producto' : $this->uom);
    }
}
