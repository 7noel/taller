<?php

namespace App\Services;

use App\Models\FormTemplate;
use App\Models\FormTemplateItem;
use Illuminate\Validation\Rule;

/**
 * Construye reglas de validación y normaliza las respuestas de un formulario
 * generado a partir de una plantilla (form_templates / sections / items).
 */
class FormAnswerService
{
    /**
     * Reglas de validación para el campo answers.* según la plantilla.
     */
    public function rulesFor(FormTemplate $template): array
    {
        $rules = [];

        foreach ($template->sections as $section) {
            foreach ($section->items as $item) {
                $rules['answers.' . $item->key] = $this->itemRules($item);
            }
        }

        return $rules;
    }

    public function messagesFor(FormTemplate $template): array
    {
        $messages = [];

        foreach ($template->sections as $section) {
            foreach ($section->items as $item) {
                $messages['answers.' . $item->key . '.required'] = 'Debe completar "' . $item->label . '".';
                $messages['answers.' . $item->key . '.integer'] = '"' . $item->label . '" debe ser un número entero.';
                $messages['answers.' . $item->key . '.min'] = '"' . $item->label . '" no puede ser menor que 0.';
                $messages['answers.' . $item->key . '.in'] = 'La opción seleccionada en "' . $item->label . '" no es válida.';
                $messages['answers.' . $item->key . '.max'] = '"' . $item->label . '" supera el máximo de caracteres.';
            }
        }

        return $messages;
    }

    /**
     * Normaliza las respuestas enviadas a un arreglo [key => value] limpio,
     * transformando checkboxes a boolean y descartando vacíos opcionales.
     */
    public function normalize(array $input, FormTemplate $template): array
    {
        $answers = [];

        foreach ($template->sections as $section) {
            foreach ($section->items as $item) {
                $key = $item->key;
                $value = $input[$key] ?? null;

                if ($item->type === FormTemplateItem::TYPE_CHECKBOX) {
                    $answers[$key] = (bool) $value;
                    continue;
                }

                if ($value === null || $value === '') {
                    if ($item->is_required) {
                        $answers[$key] = (string) $value;
                    }
                    continue;
                }

                $answers[$key] = (string) $value;
            }
        }

        return $answers;
    }

    protected function itemRules(FormTemplateItem $item): array
    {
        $rules = $item->is_required ? ['required'] : ['nullable'];

        switch ($item->type) {
            case FormTemplateItem::TYPE_NUMBER:
                $rules[] = 'integer';
                $rules[] = 'min:0';
                break;

            case FormTemplateItem::TYPE_CHECKBOX:
                $rules[] = 'boolean';
                break;

            case FormTemplateItem::TYPE_SELECT:
            case FormTemplateItem::TYPE_RADIO:
                $rules[] = 'string';
                if (! empty($item->options)) {
                    $rules[] = Rule::in(array_column($item->options, 'value'));
                }
                break;

            case FormTemplateItem::TYPE_TEXTAREA:
                $rules[] = 'string';
                $rules[] = 'max:2000';
                break;

            default:
                $rules[] = 'string';
                $rules[] = 'max:500';
                break;
        }

        return $rules;
    }
}
