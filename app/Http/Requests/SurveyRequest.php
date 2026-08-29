<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SurveyRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * La validación de respuestas es dinámica (según la plantilla del
     * establecimiento): se construye en el controlador con validateTemplateAnswers().
     */
    public function rules(): array
    {
        return [
            'answers' => ['required', 'array'],
        ];
    }

    public function messages(): array
    {
        return [
            'answers.required' => 'Debe responder la encuesta.',
        ];
    }
}
