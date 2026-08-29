<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use Illuminate\Database\Eloquent\SoftDeletes;

class FormTemplate extends Model
{
    use SoftDeletes;

    public const TYPE_QUALITY_CONTROL = 'quality_control';
    public const TYPE_SATISFACTION_SURVEY = 'satisfaction_survey';

    public const TYPES = [
        self::TYPE_QUALITY_CONTROL => 'Control de calidad',
        self::TYPE_SATISFACTION_SURVEY => 'Encuesta de satisfacción',
    ];

    protected $fillable = [
        'establishment_id',
        'type',
        'name',
        'is_active',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    /**
     * Resuelve la plantilla vigente: la del establecimiento o, si no existe,
     * la plantilla global por defecto (establishment_id null).
     */
    public static function resolveFor(?int $establishmentId, string $type): ?self
    {
        if ($establishmentId) {
            $template = static::query()
                ->where('type', $type)
                ->where('is_active', true)
                ->where('establishment_id', $establishmentId)
                ->with('sections.items')
                ->first();

            if ($template) {
                return $template;
            }
        }

        return static::query()
            ->where('type', $type)
            ->where('is_active', true)
            ->whereNull('establishment_id')
            ->with('sections.items')
            ->first();
    }

    public function getTypeLabelAttribute(): string
    {
        return self::TYPES[$this->type] ?? $this->type ?? '';
    }

    public function establishment(): BelongsTo
    {
        return $this->belongsTo(Establishment::class);
    }

    public function sections(): HasMany
    {
        return $this->hasMany(FormTemplateSection::class)->orderBy('order');
    }

    /**
     * Todas las preguntas de la plantilla (a través de sus secciones).
     * Se usa para withCount('items') y para localizar preguntas al moverlas
     * entre secciones.
     */
    public function items(): HasManyThrough
    {
        return $this->hasManyThrough(
            FormTemplateItem::class,
            FormTemplateSection::class,
            'form_template_id',
            'form_template_section_id',
            'id',
            'id'
        );
    }
}
