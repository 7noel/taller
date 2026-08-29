<?php

namespace App\Http\Requests;

use App\Models\FormTemplate;
use App\Models\FormTemplateItem;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class FormTemplateRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $rules = [
            'name' => ['required', 'string', 'max:150'],
            'establishment_id' => ['nullable', 'integer', 'exists:establishments,id'],
            'is_active' => ['sometimes', 'boolean'],
            'sections' => ['nullable', 'array'],
            'sections.*.id' => ['nullable', 'integer'],
            'sections.*.name' => ['required', 'string', 'max:150'],
            'sections.*.order' => ['nullable', 'integer', 'min:0'],
            'sections.*.items' => ['nullable', 'array'],
            'sections.*.items.*.id' => ['nullable', 'integer'],
            'sections.*.items.*.type' => ['required', 'string', Rule::in([
                FormTemplateItem::TYPE_SELECT,
                FormTemplateItem::TYPE_NUMBER,
                FormTemplateItem::TYPE_CHECKBOX,
                FormTemplateItem::TYPE_RADIO,
                FormTemplateItem::TYPE_TEXTAREA,
                FormTemplateItem::TYPE_TEXT,
            ])],
            'sections.*.items.*.key' => ['nullable', 'string', 'max:80', 'regex:/^[a-z0-9_]+$/'],
            'sections.*.items.*.label' => ['required', 'string', 'max:255'],
            'sections.*.items.*.options' => ['nullable', 'string', 'max:3000'],
            'sections.*.items.*.is_required' => ['nullable', 'boolean'],
            'sections.*.items.*.order' => ['nullable', 'integer', 'min:0'],
        ];

        if ($this->isMethod('POST')) {
            $rules['type'] = ['required', 'string', Rule::in(array_keys(FormTemplate::TYPES))];
        }

        return $rules;
    }

    public function messages(): array
    {
        return [
            'name.required' => 'Indique el nombre de la plantilla.',
            'type.required' => 'Seleccione el tipo de formulario.',
            'type.in' => 'El tipo de formulario no es válido.',
            'establishment_id.exists' => 'El establecimiento seleccionado no existe.',
            'sections.*.name.required' => 'Cada sección necesita un nombre.',
            'sections.*.items.*.type.required' => 'Cada pregunta necesita un tipo.',
            'sections.*.items.*.type.in' => 'El tipo de pregunta no es válido.',
            'sections.*.items.*.label.required' => 'Cada pregunta necesita un texto.',
            'sections.*.items.*.key.regex' => 'El identificador solo admite minúsculas, números y guion bajo (ej. fl_aceite_motor).',
        ];
    }
}
