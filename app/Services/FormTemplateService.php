<?php

namespace App\Services;

use App\Models\FormTemplate;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use RuntimeException;

/**
 * Administración de plantillas de formulario (control de calidad / encuestas):
 * sincroniza secciones e ítems con diff/upsert (nunca borra y reinserta todo).
 */
class FormTemplateService
{
    public function create(array $data): FormTemplate
    {
        return DB::transaction(function () use ($data) {
            $template = FormTemplate::create([
                'establishment_id' => $data['establishment_id'] ?: null,
                'type' => $data['type'],
                'name' => $data['name'],
                'is_active' => (bool) ($data['is_active'] ?? true),
                'created_by' => Auth::id(),
                'updated_by' => Auth::id(),
            ]);

            return $template;
        });
    }

    /**
     * Actualiza el encabezado y sincroniza secciones/ítems (diff/upsert).
     */
    public function update(FormTemplate $template, array $data): FormTemplate
    {
        DB::transaction(function () use ($template, $data) {
            $template->update([
                'name' => $data['name'],
                'establishment_id' => $data['establishment_id'] ?: null,
                'is_active' => (bool) ($data['is_active'] ?? true),
                'updated_by' => Auth::id(),
            ]);

            $this->syncStructure($template, $data['sections'] ?? []);
        });

        return $template->fresh(['sections.items']);
    }

    public function delete(FormTemplate $template): bool
    {
        return (bool) $template->delete();
    }

    /**
     * Clona una plantilla (secciones e ítems) hacia un establecimiento destino.
     */
    public function duplicate(FormTemplate $template, array $data): FormTemplate
    {
        return DB::transaction(function () use ($template, $data) {
            $template->load('sections.items');

            $clone = FormTemplate::create([
                'establishment_id' => $data['establishment_id'] ?? $template->establishment_id,
                'type' => $template->type,
                'name' => trim($data['name'] ?? '') !== '' ? trim($data['name']) : ('Copia de ' . $template->name),
                'is_active' => true,
                'created_by' => Auth::id(),
                'updated_by' => Auth::id(),
            ]);

            foreach ($template->sections as $section) {
                $newSection = $clone->sections()->create([
                    'name' => $section->name,
                    'order' => $section->order,
                ]);

                foreach ($section->items as $item) {
                    $newSection->items()->create([
                        'type' => $item->type,
                        'key' => $item->key,
                        'label' => $item->label,
                        'options' => $item->options,
                        'is_required' => $item->is_required,
                        'order' => $item->order,
                    ]);
                }
            }

            return $clone;
        });
    }

    /**
     * Sincroniza secciones e ítems: actualiza los existentes por id, crea los
     * nuevos, mueve los que cambian de sección (move_to_section_id) y elimina
     * únicamente los que ya no vienen en el request.
     */
    protected function syncStructure(FormTemplate $template, array $sections): void
    {
        $submittedSectionIds = [];
        $submittedItemIds = [];
        $allKeys = [];

        foreach ($sections as $sectionData) {
            $section = null;
            $sectionId = (int) ($sectionData['id'] ?? 0);

            if ($sectionId) {
                $section = $template->sections()->find($sectionId);
            }

            if (! $section) {
                $section = $template->sections()->create([
                    'name' => $sectionData['name'] ?? 'Sección',
                    'order' => (int) ($sectionData['order'] ?? 0),
                ]);
            } else {
                $section->update([
                    'name' => $sectionData['name'] ?? $section->name,
                    'order' => (int) ($sectionData['order'] ?? $section->order),
                ]);
            }

            $submittedSectionIds[] = $section->id;

            foreach ($sectionData['items'] ?? [] as $itemData) {
                $item = null;
                $itemId = (int) ($itemData['id'] ?? 0);

                if ($itemId) {
                    $item = $section->items()->find($itemId);

                    // Pregunta existente pero en otra sección de la plantilla
                    // (se está moviendo hacia esta sección).
                    if (! $item) {
                        $item = $template->items()->find($itemId);
                    }
                }

                $key = $this->resolveKey($itemData['key'] ?? '', $itemData['label'] ?? '');
                if (in_array($key, $allKeys, true)) {
                    throw new RuntimeException("El identificador de pregunta '{$key}' está repetido en la plantilla.");
                }
                $allKeys[] = $key;

                $fields = [
                    'type' => $itemData['type'] ?? 'text',
                    'key' => $key,
                    'label' => $itemData['label'] ?? '',
                    'options' => $this->parseOptions($itemData['options'] ?? null),
                    'is_required' => (bool) ($itemData['is_required'] ?? false),
                    'order' => (int) ($itemData['order'] ?? 0),
                ];

                // Destino del movimiento (si es distinto de la sección envolvente).
                $moveTo = (int) ($itemData['move_to_section_id'] ?? 0);
                $targetSection = $moveTo && $moveTo !== (int) $section->id
                    ? $template->sections()->find($moveTo)
                    : null;

                if ($item) {
                    if ($targetSection) {
                        $fields['form_template_section_id'] = $targetSection->id;
                    } else {
                        $fields['form_template_section_id'] = $section->id;
                    }

                    $item->update($fields);
                } else {
                    $container = $targetSection ?? $section;
                    $item = $container->items()->create($fields);
                }

                $submittedItemIds[] = $item->id;
            }
        }

        // Eliminar solo lo que ya no viene en el request (secciones e ítems).
        $template->items()->whereNotIn('form_template_items.id', $submittedItemIds)->delete();
        $template->sections()->whereNotIn('id', $submittedSectionIds)->delete();
    }

    /**
     * Genera el identificador (key) desde el label si viene vacío.
     */
    protected function resolveKey(string $key, string $label): string
    {
        $key = trim($key);

        if ($key === '') {
            $key = Str::slug($label, '_');
        }

        $key = Str::lower(Str::slug($key, '_'));

        if ($key === '') {
            throw new RuntimeException('Cada pregunta necesita un identificador o un texto para generarlo automáticamente.');
        }

        return $key;
    }

    /**
     * Convierte el texto "value|label" (una por línea) en el arreglo de opciones.
     */
    protected function parseOptions(?string $text): ?array
    {
        $text = trim((string) $text);

        if ($text === '') {
            return null;
        }

        $options = [];

        foreach (preg_split('/\r\n|\r|\n/', $text) as $line) {
            $line = trim($line);
            if ($line === '') {
                continue;
            }

            [$value, $label] = array_pad(explode('|', $line, 2), 2, '');
            $value = trim($value);
            $label = trim($label) !== '' ? trim($label) : $value;

            if ($value !== '') {
                $options[] = ['value' => $value, 'label' => $label];
            }
        }

        return $options ?: null;
    }
}
