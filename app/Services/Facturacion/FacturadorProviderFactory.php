<?php

namespace App\Services\Facturacion;

use App\Models\CompanySetting;
use InvalidArgumentException;

/**
 * Resuelve el proveedor de facturación configurado en company_settings.
 */
class FacturadorProviderFactory
{
    public const NUBEFACT = 'nubefact';
    public const PROPIO = 'propio';

    public static function make(): FacturadorProviderInterface
    {
        $provider = CompanySetting::get()?->facturador_provider ?? self::NUBEFACT;

        if ($provider === 'local' || $provider === null) {
            throw new InvalidArgumentException(
                'El facturador está configurado como LOCAL. Configure Nubefact o Propio en Configuración → Integraciones para emitir comprobantes electrónicos.'
            );
        }

        return match ($provider) {
            self::NUBEFACT => new NubefactProvider(),
            self::PROPIO => new FacturaPeruProvider(),
            default => throw new InvalidArgumentException("Proveedor de facturación no soportado: {$provider}"),
        };
    }
}
