<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class SeriesRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // La autorización se maneja en el controlador con Gate::authorize
    }

    public function rules(): array
    {
        $establishmentId = $this->route('establishment')?->id;
        $seriesId = $this->route('series')?->id;

        return [
            'document_type_id' => [
                Rule::requiredIf($this->isMethod('post')),
                'integer',
                'exists:document_types,id',
            ],
            'prefix_serie' => [
                'required',
                'string',
                'max:10',
                Rule::unique('document_series', 'prefix_serie')
                    ->where('establishment_id', $establishmentId)
                    ->where('document_type_id', $this->input('document_type_id'))
                    ->ignore($seriesId),
            ],
            'current_number' => ['required', 'integer', 'min:0'],
            'number_source' => ['required', 'string', Rule::in(['LOCAL', 'API'])],
            'status' => ['nullable', 'boolean'],
        ];
    }

    public function messages(): array
    {
        return [
            'document_type_id.required' => 'Seleccione el tipo de documento.',
            'document_type_id.exists' => 'El tipo de documento seleccionado no existe.',
            'prefix_serie.required' => 'Ingrese el prefijo de la serie.',
            'prefix_serie.unique' => 'Ya existe una serie con ese prefijo para el tipo de documento seleccionado.',
            'current_number.required' => 'Ingrese el número correlativo inicial.',
            'current_number.integer' => 'El número correlativo debe ser un entero.',
            'number_source.required' => 'Seleccione el origen del número.',
            'number_source.in' => 'El origen del número debe ser LOCAL o API.',
        ];
    }
}