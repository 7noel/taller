<?php

namespace App\Services;

use App\Models\CompanySetting;
use App\Models\Establishment;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class WhatsAppService
{
    /**
     * Envía un mensaje de texto vía Evolution API.
     *
     * Las credenciales se resuelven por establecimiento (whatsapp_api_url /
     * whatsapp_api_token / whatsapp_instance_name / whatsapp_enabled) con
     * respaldo en company_settings.
     */
    public function send(Establishment $establishment, string $phone, string $message): bool
    {
        $credentials = $this->resolveCredentials($establishment);

        if (empty($credentials['api_url']) || empty($credentials['token']) || empty($credentials['instance']) || ! $credentials['enabled']) {
            Log::warning('WhatsApp no configurado o deshabilitado.', [
                'establishment_id' => $establishment->id,
                'phone' => $phone,
            ]);

            return false;
        }

        try {
            $response = Http::timeout(15)
                ->withHeaders(['apikey' => $credentials['token']])
                ->post(
                    rtrim($credentials['api_url'], '/') . '/message/sendText/' . $credentials['instance'],
                    [
                        'number' => $this->normalizePhone($phone),
                        'text' => $message,
                    ]
                );

            if ($response->successful()) {
                return true;
            }

            Log::error('Evolution API: respuesta con error.', [
                'status' => $response->status(),
                'body' => $response->body(),
            ]);

            return false;
        } catch (\Throwable $e) {
            Log::error('Evolution API: excepción al enviar.', ['message' => $e->getMessage()]);

            return false;
        }
    }

    /**
     * Resuelve las credenciales de Evolution API: establecimiento primero,
     * company_settings como respaldo global.
     */
    public function resolveCredentials(Establishment $establishment): array
    {
        $company = CompanySetting::get();

        return [
            'api_url' => $establishment->whatsapp_api_url ?: ($company?->whatsapp_api_url ?: null),
            'token' => $establishment->whatsapp_api_token ?: ($company?->whatsapp_api_token ?: null),
            'instance' => $establishment->whatsapp_instance_name ?: ($company?->whatsapp_instance_name ?: ''),
            'enabled' => (bool) ($establishment->whatsapp_enabled ?? $company?->whatsapp_enabled ?? false),
        ];
    }

    /**
     * Normaliza un teléfono a formato internacional para wa.me / Evolution API.
     * Por defecto asume Perú (+51); configurable por país.
     */
    public function normalizePhone(string $phone, string $countryCode = '51'): string
    {
        $phone = preg_replace('/\D/', '', (string) $phone);

        if ($phone === '') {
            return '';
        }

        // Si ya viene con el código de país, devolver tal cual.
        if (str_starts_with($phone, $countryCode)) {
            return $phone;
        }

        // Quitar prefijo 00 (internacional) o 0 local.
        if (str_starts_with($phone, '00')) {
            $phone = substr($phone, 2);
        } elseif (str_starts_with($phone, '0')) {
            $phone = substr($phone, 1);
        }

        return $countryCode . $phone;
    }

    /**
     * Enlace wa.me para que el asesor envíe manualmente desde su WhatsApp.
     */
    public function buildWaLink(string $phone, string $message): string
    {
        return 'https://wa.me/' . $this->normalizePhone($phone) . '?text=' . rawurlencode($message);
    }
}
