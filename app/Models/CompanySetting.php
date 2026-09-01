<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;

class CompanySetting extends Model
{
    protected $fillable = [
        'ruc',
        'razon_social',
        'nombre_comercial',
        'direccion',
        'ubigeo_code',
        'telefono',
        'celular',
        'email',
        'logo_path',
        'favicon_path',
        'detraccion_account',
        'detraccion_rate',
        'igv_rate',
        'default_number_source',
        'facturador_provider',
        'facturador_api_url',
        'facturador_api_key',
        'facturador_secret',
        'whatsapp_api_url',
        'whatsapp_api_token',
        'whatsapp_instance_name',
        'whatsapp_enabled',
        'qc_require_assignments_completed',
        'maintenance_interval_km',
        'maintenance_default_days',
        'maintenance_history_visits',
    ];

    protected $casts = [
        'default_number_source' => 'string',
        'facturador_provider' => 'string',
        'whatsapp_enabled' => 'boolean',
        'qc_require_assignments_completed' => 'boolean',
        'maintenance_interval_km' => 'integer',
        'maintenance_default_days' => 'integer',
        'maintenance_history_visits' => 'integer',
    ];

    /**
     * Obtiene la configuración global (una sola fila).
     */
    public static function get(): ?self
    {
        return static::query()->first();
    }

    public function ubigeo(): BelongsTo
    {
        return $this->belongsTo(Ubigeo::class, 'ubigeo_code', 'code');
    }

    public function getLogoUrlAttribute(): ?string
    {
        return $this->logo_path ? Storage::disk('public')->url($this->logo_path) : null;
    }

    public function getFaviconUrlAttribute(): ?string
    {
        return $this->favicon_path ? Storage::disk('public')->url($this->favicon_path) : null;
    }
}