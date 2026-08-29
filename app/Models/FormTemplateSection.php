<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class FormTemplateSection extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'form_template_id',
        'name',
        'order',
    ];

    public function template(): BelongsTo
    {
        return $this->belongsTo(FormTemplate::class, 'form_template_id');
    }

    public function items(): HasMany
    {
        return $this->hasMany(FormTemplateItem::class)->orderBy('order');
    }
}
