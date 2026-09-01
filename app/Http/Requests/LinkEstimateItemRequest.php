<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

/**
 * Valida el vínculo de una línea del presupuesto al catálogo.
 * Según la ruta (link-part / link-service) exige el campo correspondiente.
 */
class LinkEstimateItemRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // la autorización se hace con Gate sobre el presupuesto
    }

    public function rules(): array
    {
        $isPart = $this->routeIs('api.estimate-items.link-part');

        return $isPart
            ? ['part_id' => ['required', 'integer', Rule::exists('parts', 'id')]]
            : ['service_id' => ['required', 'integer', Rule::exists('repair_services', 'id')]];
    }
}
