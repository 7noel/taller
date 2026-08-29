<?php

namespace App\Http\Requests;

use App\Models\CheckInChecklistItem;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class CheckInChecklistItemRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:100'],
            'category' => ['required', 'string', Rule::in(array_keys(CheckInChecklistItem::CATEGORIES))],
            'order' => ['nullable', 'integer', 'min:0'],
            'is_active' => ['sometimes', 'boolean'],
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'Indique el nombre del ítem del checklist.',
            'category.required' => 'Seleccione la categoría.',
            'category.in' => 'La categoría no es válida.',
            'order.min' => 'El orden no puede ser negativo.',
        ];
    }
}
