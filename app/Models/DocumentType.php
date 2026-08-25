<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class DocumentType extends Model
{
    protected $fillable = [
        'code',
        'name',
        'is_electronic',
        'is_active',
    ];

    protected $casts = [
        'is_electronic' => 'boolean',
        'is_active' => 'boolean',
    ];

    public function series(): HasMany
    {
        return $this->hasMany(DocumentSeries::class);
    }
}