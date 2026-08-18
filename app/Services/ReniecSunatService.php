<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class ReniecSunatService
{
    protected string $baseUrl = 'https://api.apisperu.com/api/v1';
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
        if (strlen($dni) !== 8 || !ctype_digit($dni)) {
            return null;
        }

        try {
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $this->token,
                'Accept' => 'application/json',
            ])->get("{$this->baseUrl}/dni/{$dni}");

            if ($response->failed()) {
                return null;
            }

            $data = $response->json();

            if (empty($data) || ($data['success'] ?? false) !== true) {
                return null;
            }

            return [
                'document_type' => 'DNI',
                'document_number' => $data['dni'] ?? $dni,
                'first_name' => mb_strtoupper($data['nombres'] ?? ''),
                'last_name' => trim(mb_strtoupper(($data['apellidoPaterno'] ?? '') . ' ' . ($data['apellidoMaterno'] ?? ''))),
                'type' => 'person',
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
        if (strlen($ruc) !== 11 || !ctype_digit($ruc)) {
            return null;
        }

        try {
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $this->token,
                'Accept' => 'application/json',
            ])->get("{$this->baseUrl}/ruc/{$ruc}");

            if ($response->failed()) {
                return null;
            }

            $data = $response->json();

            if (empty($data) || ($data['success'] ?? false) !== true) {
                return null;
            }

            return [
                'document_type' => 'RUC',
                'document_number' => $data['ruc'] ?? $ruc,
                'business_name' => mb_strtoupper($data['razonSocial'] ?? ''),
                'address' => mb_strtoupper($data['direccion'] ?? ''),
                'department' => mb_strtoupper($data['departamento'] ?? ''),
                'province' => mb_strtoupper($data['provincia'] ?? ''),
                'district' => mb_strtoupper($data['distrito'] ?? ''),
                'ubigeo_code' => $data['ubigeo'] ?? null,
                'type' => 'company',
            ];
        } catch (\Exception $e) {
            return null;
        }
    }

    /**
     * Consultar DNI o RUC según el tipo de documento.
     */
    public function searchByDocument(string $documentType, string $documentNumber): ?array
    {
        return match ($documentType) {
            '1', 'DNI' => $this->getDni($documentNumber),
            '6', 'RUC' => $this->getRuc($documentNumber),
            default => null,
        };
    }
}