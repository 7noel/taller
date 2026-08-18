<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class SunatExchangeService
{
    protected string $baseUrl = 'https://api.apis.net.pe/v2/sunat/tipo-cambio';
    protected string $token;

    public function __construct()
    {
        $this->token = env('SUNAT_API_TOKEN', 'apis-token-4614.TQ4y0Hx1PBGUUXxkLI4qPeB9DpkVbwpi');
    }

    protected function client()
    {
        return Http::withHeaders([
            'Authorization' => 'Bearer ' . $this->token,
            'Accept' => 'application/json',
        ]);
    }

    /**
     * Obtener tipo de cambio de una fecha específica (Y-m-d).
     */
    public function getTipoCambio(string $fecha): ?object
    {
        try {
            $response = $this->client()->get($this->baseUrl, [
                'fecha' => $fecha,
            ]);

            if ($response->failed()) {
                return null;
            }

            $data = $response->json();

            if (empty($data)) {
                return null;
            }

            return (object) [
                'fecha' => $data['fecha'] ?? $fecha,
                'compra' => (float) ($data['compra'] ?? 0),
                'venta' => (float) ($data['venta'] ?? 0),
            ];
        } catch (\Exception $e) {
            return null;
        }
    }

    /**
     * Obtener tipo de cambio de un mes completo (año, mes).
     */
    public function getTipoCambioMes(int $year, int $month): ?array
    {
        try {
            $response = $this->client()->get("{$this->baseUrl}/mes", [
                'mes' => $month,
                'anio' => $year,
            ]);

            if ($response->failed()) {
                return null;
            }

            $data = $response->json();

            if (empty($data) || !is_array($data)) {
                return null;
            }

            return array_map(fn ($item) => (object) [
                'fecha' => $item['fecha'] ?? null,
                'compra' => (float) ($item['compra'] ?? 0),
                'venta' => (float) ($item['venta'] ?? 0),
            ], $data);
        } catch (\Exception $e) {
            return null;
        }
    }
}