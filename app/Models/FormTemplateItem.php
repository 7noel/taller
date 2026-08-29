<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class FormTemplateItem extends Model
{
    use SoftDeletes;

    public const TYPE_SELECT = 'select';
    public const TYPE_NUMBER = 'number';
    public const TYPE_CHECKBOX = 'checkbox';
    public const TYPE_RADIO = 'radio';
    public const TYPE_TEXTAREA = 'textarea';
    public const TYPE_TEXT = 'text';

    public const TYPE_OPTIONS = [
        self::TYPE_SELECT => 'Lista desplegable (select)',
        self::TYPE_RADIO => 'Opciones únicas (radio)',
        self::TYPE_CHECKBOX => 'Casilla de verificación (checkbox)',
        self::TYPE_NUMBER => 'Número',
        self::TYPE_TEXT => 'Texto corto',
        self::TYPE_TEXTAREA => 'Texto largo (textarea)',
    ];

    protected $fillable = [
        'form_template_section_id',
        'type',
        'key',
        'label',
        'options',
        'is_required',
        'order',
    ];

    protected $casts = [
        'options' => 'array',
        'is_required' => 'boolean',
    ];

    public function section(): BelongsTo
    {
        return $this->belongsTo(FormTemplateSection::class, 'form_template_section_id');
    }

    /**
     * Opciones como lista [value, label] para select/radio.
     */
    public function getOptionListAttribute(): array
    {
        return collect($this->options ?? [])->map(fn ($o) => [
            'value' => $o['value'] ?? '',
            'label' => $o['label'] ?? '',
        ])->values()->all();
    }

    /**
     * Opciones como texto "value|label" (una por línea) para el editor.
     */
    public function getOptionsTextAttribute(): string
    {
        return collect($this->option_list)
            ->map(fn ($o) => $o['value'] . '|' . $o['label'])
            ->implode("\n");
    }
}
