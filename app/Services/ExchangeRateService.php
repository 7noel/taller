<?php

namespace App\Services;

use App\Models\ExchangeRate;
use Illuminate\Support\Facades\Log;

class ExchangeRateService
{
    /**
     * Convención del sistema: el tipo de cambio se expresa en soles (PEN)
     * por 1 dólar (USD). Para PEN el tipo de cambio es 1.
     */
    public const BASE_CURRENCY = 'PEN';

    public const RATE_CURRENCY = 'USD';

    public function __construct(protected SunatExchangeService $sunat)
    {
    }

    /**
     * Garantiza un tipo de cambio para una fecha con la estrategia:
     * 1) buscar en BD; 2) si no existe, consultar la API SUNAT y persistir;
     * 3) si la API falla o no trae dato, usar el último registrado (≤ fecha).
     *
     * Se usa el tipo de cambio de venta (sell_rate), convención del sistema.
     */
    public function ensureRateForDate(string $date, string $currency = self::RATE_CURRENCY): ?ExchangeRate
    {
        if ($currency === self::BASE_CURRENCY) {
            return null; // PEN no necesita tipo de cambio (1).
        }

        // 1) ¿Ya está en BD para esa fecha?
        $existing = ExchangeRate::query()
            ->where('date', $date)
            ->where('currency', $currency)
            ->first();

        if ($existing) {
            return $existing;
        }

        // 2) Consultar SUNAT y persistir.
        try {
            $data = $this->sunat->getTipoCambio($date);

            if ($data && (float) $data->venta > 0) {
                return ExchangeRate::updateOrCreate(
                    ['date' => $date, 'currency' => $currency],
                    [
                        'buy_rate' => $data->compra,
                        'sell_rate' => $data->venta,
                        'source' => 'SUNAT',
                    ]
                );
            }
        } catch (\Throwable $e) {
            Log::warning("No se pudo obtener el tipo de cambio SUNAT para {$date}: {$e->getMessage()}");
        }

        // 3) Fallback: último tipo de cambio registrado.
        return $this->latestFor($currency, $date);
    }

    /**
     * Último tipo de cambio registrado para una moneda (hasta una fecha).
     */
    public function latestFor(string $currency, ?string $date = null): ?ExchangeRate
    {
        if ($currency === self::BASE_CURRENCY) {
            return null; // PEN no necesita tipo de cambio (1).
        }

        $query = ExchangeRate::query()
            ->where('currency', $currency)
            ->orderByDesc('date')
            ->orderByDesc('id');

        if ($date) {
            $query->whereDate('date', '<=', $date);
        }

        return $query->first();
    }

    /**
     * Tasa sugerida para una moneda: 1 para PEN, el último sell_rate para
     * USD (fallback 1 si aún no hay registros).
     */
    public function suggestRate(string $currency, ?string $date = null): float
    {
        if ($currency === self::BASE_CURRENCY) {
            return 1.0;
        }

        $rate = $this->latestFor($currency, $date);

        return $rate ? (float) $rate->sell_rate : 1.0;
    }

    /**
     * Convierte un monto entre monedas según la convención del sistema
     * (exchange_rate = soles por 1 dólar).
     *
     * - PEN → otra: monto / tipo (cada dólar vale $rate soles).
     * - otra → PEN: monto × tipo.
     * - misma moneda: sin cambios.
     */
    public function convert(float $amount, string $from, string $to, float $rate): float
    {
        if ($from === $to) {
            return round($amount, 4);
        }

        $rate = max($rate, 0.0001);

        if ($from === self::BASE_CURRENCY) {
            return round($amount / $rate, 4);
        }

        return round($amount * $rate, 4);
    }
}
