<?php

namespace Database\Seeders;

use App\Models\FormTemplate;
use App\Models\FormTemplateItem;
use App\Models\FormTemplateSection;
use Illuminate\Database\Seeder;

class FormTemplateSeeder extends Seeder
{
    /**
     * Plantillas GLOBALES por defecto (establishment_id = null). Cada taller puede
     * crear su propia plantilla; mientras no la tenga, se usa la global.
     */
    public function run(): void
    {
        $this->seedQualityControl();
        $this->seedSatisfactionSurvey();
    }

    protected function seedQualityControl(): void
    {
        $template = $this->template(FormTemplate::TYPE_QUALITY_CONTROL, 'Control de calidad estándar');

        $general = $this->section($template, 'Datos generales', 0);
        $this->item($general, FormTemplateItem::TYPE_SELECT, 'fuel_level', 'Nivel de combustible', [
            ['value' => 'empty', 'label' => 'Vacío'],
            ['value' => 'quarter', 'label' => '1/4'],
            ['value' => 'half', 'label' => '1/2'],
            ['value' => 'three_quarters', 'label' => '3/4'],
            ['value' => 'full', 'label' => 'Lleno'],
        ], true, 0);
        $this->item($general, FormTemplateItem::TYPE_NUMBER, 'mileage', 'Kilometraje', null, true, 1);

        $resolution = $this->section($template, 'Resolución de la avería', 1);
        foreach ([
            'res_falla_resuelta' => 'Falla resuelta',
            'res_motor_armado' => 'Motor armado',
            'res_motor_limpio' => 'Motor limpio, libre de fuga de fluidos',
            'res_tren_armado' => 'Tren motriz armado',
            'res_tren_limpio' => 'Tren motriz limpio, libre de fuga de fluidos',
            'res_llantas_aseguradas' => 'Llantas aseguradas y verificadas',
            'res_llantas_calibradas' => 'Llantas calibradas',
            'res_partes_moviles_armadas' => 'Armado de partes móviles desarmadas para la reparación',
        ] as $key => $label) {
            $this->item($resolution, FormTemplateItem::TYPE_CHECKBOX, $key, $label, null, false, 0);
        }

        $vehicleState = $this->section($template, 'Estado del vehículo', 2);
        foreach ([
            'est_carroceria_limpia' => 'Carrocería limpia, sin manchas de grasa ni fluidos',
            'est_cabina_limpia' => 'Cabina limpia, sin basura, residuos de insumos, fluidos, cableado, etc.',
            'est_cabina_sin_herramientas' => 'Cabina libre de herramientas',
            'est_cabina_sin_equipo' => 'Cabina libre de equipo técnico como Scanner, Multímetro, etc.',
            'est_motor_sin_herramientas' => 'Compartimiento de motor libre de herramienta y equipo técnico',
        ] as $key => $label) {
            $this->item($vehicleState, FormTemplateItem::TYPE_CHECKBOX, $key, $label, null, false, 0);
        }

        $fluids = $this->section($template, 'Fluidos', 3);
        foreach ([
            'fl_aceite_motor' => 'Aceite de Motor',
            'fl_liquido_frenos' => 'Líquido de Frenos',
            'fl_liquido_embrague' => 'Líquido de Embrague',
            'fl_refrigerante' => 'Refrigerante',
            'fl_aceite_hidraulico' => 'Aceite Hidráulico',
        ] as $key => $label) {
            $this->item($fluids, FormTemplateItem::TYPE_CHECKBOX, $key, $label, null, false, 0);
        }

        $observations = $this->section($template, 'Observaciones', 4);
        $this->item($observations, FormTemplateItem::TYPE_TEXTAREA, 'observaciones', 'Observaciones', null, false, 0);
    }

    protected function seedSatisfactionSurvey(): void
    {
        $template = $this->template(FormTemplate::TYPE_SATISFACTION_SURVEY, 'Encuesta de satisfacción estándar');

        $about = $this->section($template, 'Sobre nosotros', 0);
        $this->item($about, FormTemplateItem::TYPE_RADIO, 'how_knew', '¿Cómo conoció nuestra empresa?', [
            ['value' => 'social_media', 'label' => 'Publicidad en redes sociales'],
            ['value' => 'media', 'label' => 'Publicidad en medios de comunicación'],
            ['value' => 'friend_referral', 'label' => 'Recomendación de un amigo'],
            ['value' => 'other', 'label' => 'Otro'],
        ], true, 0);
        $this->item($about, FormTemplateItem::TYPE_TEXT, 'how_knew_other', 'Si eligió "Otro", indíquenos el medio', null, false, 1);

        $ratings = $this->section($template, 'Calificación del servicio', 1);
        $this->item($ratings, FormTemplateItem::TYPE_RADIO, 'advisor_rating', 'Califique la atención recibida por su asesor de servicio.', [
            ['value' => 'good', 'label' => 'Buena'],
            ['value' => 'bad', 'label' => 'Mala'],
        ], true, 0);
        $this->item($ratings, FormTemplateItem::TYPE_RADIO, 'facilities_rating', 'Califique nuestras instalaciones.', [
            ['value' => 'good', 'label' => 'Buenas'],
            ['value' => 'bad', 'label' => 'Malas'],
        ], true, 1);
        $this->item($ratings, FormTemplateItem::TYPE_RADIO, 'timing_rating', 'Califique los tiempos de diagnóstico y reparación de su vehículo.', [
            ['value' => 'fast', 'label' => 'Rápidos'],
            ['value' => 'slow', 'label' => 'Lentos'],
        ], true, 2);
        $this->item($ratings, FormTemplateItem::TYPE_RADIO, 'service_quality_rating', 'Califique la calidad de nuestros servicios.', [
            ['value' => 'good', 'label' => 'Buena'],
            ['value' => 'bad', 'label' => 'Mala'],
        ], true, 3);
        $this->item($ratings, FormTemplateItem::TYPE_RADIO, 'price_rating', 'Califique el nivel de precios de nuestros servicios.', [
            ['value' => 'economical', 'label' => 'Económicos'],
            ['value' => 'expensive', 'label' => 'Costosos'],
        ], true, 4);

        $recommend = $this->section($template, 'Recomendación', 2);
        $scores = collect(range(1, 10))->map(fn ($n) => ['value' => (string) $n, 'label' => (string) $n])->all();
        $this->item($recommend, FormTemplateItem::TYPE_RADIO, 'recommend_score', 'De 1 a 10, ¿cuál es la probabilidad de que nos recomiende?', $scores, true, 0);
    }

    protected function template(string $type, string $name): FormTemplate
    {
        FormTemplate::whereNull('establishment_id')->where('type', $type)->forceDelete();

        return FormTemplate::create([
            'establishment_id' => null,
            'type' => $type,
            'name' => $name,
            'is_active' => true,
        ]);
    }

    protected function section(FormTemplate $template, string $name, int $order): FormTemplateSection
    {
        return FormTemplateSection::create([
            'form_template_id' => $template->id,
            'name' => $name,
            'order' => $order,
        ]);
    }

    protected function item(
        FormTemplateSection $section,
        string $type,
        string $key,
        string $label,
        ?array $options,
        bool $required,
        int $order
    ): void {
        FormTemplateItem::create([
            'form_template_section_id' => $section->id,
            'type' => $type,
            'key' => $key,
            'label' => $label,
            'options' => $options,
            'is_required' => $required,
            'order' => $order,
        ]);
    }
}

