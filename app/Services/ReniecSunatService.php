<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class ReniecSunatService
{
    protected string $baseUrl = 'https://dniruc.apisperu.com/api/v1';
    protected string $token;

    public function __construct()
    {
        $this->token = env('APIS_PERU_TOKEN', 'eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJlbWFpbCI6Im5vZWwubG9nYW5AZ21haWwuY29tIn0.pSSHu1Rh3RUgPubnjemiDNyMAN0ZjgTCXaupa8VsEYY');
    }

    /**
     * Consultar DNI (persona natural).
     */
    public function getDni(string $dni): ?array
    {
        // El DNI tiene 8 dígitos y puede empezar con cero
        if (! preg_match('/^\d{8}$/', $dni)) {
            return null;
        }

        try {
            $response = Http::withOptions(['verify' => false])
                ->acceptJson()
                ->get("{$this->baseUrl}/dni/{$dni}", ['token' => $this->token]);

            if ($response->failed()) {
                return null;
            }

            $data = $response->json();

            if (empty($data) || ($data['success'] ?? false) !== true) {
                return null;
            }

            return [
                'document_type' => '1', // DNI (código SUNAT)
                'document_number' => $data['dni'] ?? $dni,
                // Formato: apellidos primero, luego nombre
                'last_name' => trim(mb_strtoupper(($data['apellidoPaterno'] ?? '') . ' ' . ($data['apellidoMaterno'] ?? ''))),
                'first_name' => mb_strtoupper($data['nombres'] ?? ''),
            ];
        } catch (\Exception $e) {
            return null;
        }
    }

    /**
     * Consultar RUC (empresa).
     */
    public function getRuc(string $ruc): ?array
    {
        if (! preg_match('/^\d{11}$/', $ruc)) {
            return null;
        }

        try {
            $response = Http::withOptions(['verify' => false])
                ->acceptJson()
                ->get("{$this->baseUrl}/ruc/{$ruc}", ['token' => $this->token]);

            if ($response->failed()) {
                return null;
            }

            $data = $response->json();

            // Igual que el JS original: aceptar cualquier respuesta no vacía
            if (empty($data)) {
                return null;
            }

            // Nombre: RUC de empresa usa "razonSocial"; persona natural usa "nombre_o_razon_social"
            $businessName = $data['razonSocial'] ?? $data['nombre_o_razon_social'] ?? '';

            // Domicilio fiscal puede venir en plano o anidado
            $domicilio = $data['domicilio_fiscal'] ?? $data;

            $ubigeoCode = $domicilio['ubigeo'] ?? $data['ubigeo'] ?? null;

            // Obtener departamento/provincia/distrito desde la tabla local ubigeos cuando exista el código
            $ubigeo = $ubigeoCode ? \App\Models\Ubigeo::where('code', $ubigeoCode)->first() : null;

            if ($ubigeo) {
                $department = mb_strtoupper($ubigeo->departamento);
                $province = mb_strtoupper($ubigeo->provincia);
                $district = mb_strtoupper($ubigeo->distrito);
            } else {
                $department = mb_strtoupper($domicilio['departamento'] ?? '');
                $province = mb_strtoupper($domicilio['provincia'] ?? '');
                $district = mb_strtoupper($domicilio['distrito'] ?? '');
            }

            $direccion = mb_strtoupper($domicilio['direccion'] ?? '');

            // Limpiar dirección: quitar el sufijo " DEPARTAMENTO PROVINCIA DISTRITO"
            foreach ([$department, $province, $district] as $part) {
                if ($part !== '') {
                    $direccion = str_replace(" {$part}", '', $direccion);
                }
            }

            return [
                'document_type' => '6', // RUC (código SUNAT)
                'document_number' => $data['ruc'] ?? $ruc,
                'business_name' => mb_strtoupper($businessName),
                'address' => $direccion,
                'department' => $department,
                'province' => $province,
                'district' => $district,
                'ubigeo_code' => $domicilio['ubigeo'] ?? $data['ubigeo'] ?? null,
            ];
        } catch (\Exception $e) {
            return null;
        }
    }

    /**
     * Consultar DNI o RUC según el código de documento SUNAT.
     * 1=DNI, 6=RUC.
     */
    public function searchByDocument(string $documentType, string $documentNumber): ?array
    {
        return match ($documentType) {
            '1' => $this->getDni($documentNumber),
            '6' => $this->getRuc($documentNumber),
            default => null,
        };
    }
}