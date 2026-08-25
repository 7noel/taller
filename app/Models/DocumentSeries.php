<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DocumentSeries extends Model
{
    protected $fillable = [
        'establishment_id',
        'document_type_id',
        'prefix_serie',
        'current_number',
        'number_source',
        'status',
    ];

    protected $casts = [
        'current_number' => 'integer',
        'status' => 'boolean',
    ];

    public function establishment(): BelongsTo
    {
        return $this->belongsTo(Establishment::class);
    }

    public function documentType(): BelongsTo
    {
        return $this->belongsTo(DocumentType::class);
    }

    /**
     * Devuelve el número con formato, ej. IV01-000001.
     * Si la serie aún no se ha usado (current_number = 0), devuelve solo el prefijo.
     */
    public function getFormattedNumberAttribute(): ?string
    {
        if ($this->number_source === 'API') {
            return null;
        }

        if ($this->current_number === 0) {
            return $this->prefix_serie;
        }

        return sprintf('%s-%06d', $this->prefix_serie, $this->current_number);
    }
}
