<?php

namespace Database\Seeders;

use App\Models\CheckInChecklistItem;
use Illuminate\Database\Seeder;

class ChecklistItemsSeeder extends Seeder
{
    /**
     * Lee y limpia una línea CSV (maneja comillas dobles y saltos internos).
     */
    private function parseCsvLine(string $line): array
    {
        // Quitar BOM y saltos de línea
        $line = preg_replace('/^\xEF\xBB\xBF/', '', $line);
        $line = trim($line);
        if ($line === '') {
            return [];
        }

        // Si no tiene comillas, dividir simple
        if (!str_contains($line, '"')) {
            $parts = explode(',', $line);
            return array_map('trim', $parts);
        }

        // Parser manual: soporta comillas dobles y comas dentro de comillas
        $fields = [];
        $current = '';
        $inQuotes = false;
        $length = strlen($line);

        for ($i = 0; $i < $length; $i++) {
            $char = $line[$i];

            if ($inQuotes) {
                if ($char === '"' && $i + 1 < $length && $line[$i + 1] === '"') {
                    $current .= '"';
                    $i++;
                } elseif ($char === '"') {
                    $inQuotes = false;
                } else {
                    $current .= $char;
                }
            } else {
                if ($char === '"') {
                    $inQuotes = true;
                } elseif ($char === ',') {
                    $fields[] = $current;
                    $current = '';
                } else {
                    $current .= $char;
                }
            }
        }
        $fields[] = $current;

        return array_map('trim', $fields);
    }

    /**
     * Lista manual de respaldo si el CSV no está disponible.
     */
    private function manualItems(): array
    {
        return [
            // EXTERIOR
            ['PLUMILLAS', 'EXTERIOR'], ['PARABRISA DELANTERO', 'EXTERIOR'],
            ['FARO POSTERIOR', 'EXTERIOR'], ['SEGURO DE AROS', 'EXTERIOR'],
            ['TAPA DE COMBUSTIBLE', 'EXTERIOR'], ['BRAZO DE PLUMILLAS', 'EXTERIOR'],
            ['PARABRISA POSTERIOR', 'EXTERIOR'], ['MANIJA EXTERIOR', 'EXTERIOR'],
            ['MOL. PUERTA', 'EXTERIOR'], ['LLANTAS', 'EXTERIOR'],
            ['ESPEJOS EXTERIORES', 'EXTERIOR'], ['FARO DELANTERO', 'EXTERIOR'],
            ['NEBLINEROS', 'EXTERIOR'], ['SEGURO DE VASOS', 'EXTERIOR'],
            ['ANTENA', 'EXTERIOR'], ['VASO/COPA', 'EXTERIOR'],
            // MOTOR
            ['BATERIA', 'MOTOR'], ['PURIFICADOR', 'MOTOR'],
            ['TAPA LIQUI. EMBRAGUE', 'MOTOR'], ['TAPA DE RADIADOR', 'MOTOR'],
            ['SOPORTE DE BATERIA', 'MOTOR'], ['TAPA LIQUI. FRENO', 'MOTOR'],
            ['TAPA LIQUI. DIRECCION', 'MOTOR'], ['TAPA DE ACEITE', 'MOTOR'],
            ['GNV-GLP', 'MOTOR'], ['VARILLA ATM', 'MOTOR'],
            ['VARILLA DE ACEITE', 'MOTOR'], ['GATA', 'MOTOR'],
            // INTERIOR
            ['TABLERO', 'INTERIOR'], ['CENICERO', 'INTERIOR'],
            ['CABEZAL DE ASIENTO', 'INTERIOR'], ['ABRE PUERTAS', 'INTERIOR'],
            ['PISOS SOBRE ALFOMBRAS', 'INTERIOR'], ['TAPIZ DE ASIENTOS', 'INTERIOR'],
            ['ENCENDEDOR', 'INTERIOR'], ['RADIO', 'INTERIOR'],
            ['ALZA LUNAS', 'INTERIOR'], ['TAPASOL', 'INTERIOR'],
            ['TAPIZ DE PUERTA', 'INTERIOR'], ['ESPEJOS INTERIORES', 'INTERIOR'],
            ['RELOJ', 'INTERIOR'], ['CLAXON', 'INTERIOR'],
            ['CODERAS', 'INTERIOR'], ['ALARMA', 'INTERIOR'],
            // HERRAMIENTAS/EMERGENCIA
            ['ALFOMBRA EN MALETERA', 'HERRAMIENTAS/EMERGENCIA'],
            ['PALANCA DE GATA', 'HERRAMIENTAS/EMERGENCIA'],
            ['HERRAMIENTAS', 'HERRAMIENTAS/EMERGENCIA'],
            ['LLAVE DE RUEDAS', 'HERRAMIENTAS/EMERGENCIA'],
            ['EXTINGUIDOR', 'HERRAMIENTAS/EMERGENCIA'],
            ['TRIANGULO', 'HERRAMIENTAS/EMERGENCIA'],
            ['COCODRILOS', 'HERRAMIENTAS/EMERGENCIA'],
            ['LLANTA DE REPUESTO', 'HERRAMIENTAS/EMERGENCIA'],
        ];
    }

    public function run(): void
    {
        $csvPath = 'C:\Users\Noel\Downloads\checklist_details.csv';

        $items = [];
        if (file_exists($csvPath)) {
            $handle = fopen($csvPath, 'r');
            if ($handle !== false) {
                $first = true;
                $buffer = '';
                while (($line = fgets($handle)) !== false) {
                    if ($first) {
                        $first = false;
                        continue; // saltar encabezado
                    }

                    // Concatenar líneas partidas cuando hay comillas sin cerrar
                    $buffer .= $line;
                    if (substr_count($buffer, '"') % 2 !== 0) {
                        continue; // comilla sin cerrar: seguir leyendo
                    }

                    $fields = $this->parseCsvLine($buffer);
                    $buffer = ''; // reset del buffer

                    // Sanear: solo líneas con name y category válidos
                    if (count($fields) >= 2 && trim($fields[0]) !== '' && trim($fields[1]) !== '') {
                        $items[] = [
                            'name' => mb_strtoupper(trim($fields[0])),
                            'category' => mb_strtoupper(trim($fields[1])),
                        ];
                    }
                }
                fclose($handle);
            }
        }

        // Fallback a lista manual si el CSV no se pudo leer o está vacío
        if (empty($items)) {
            $items = $this->manualItems();
        }

        // Insertar de forma idempotente
        foreach ($items as $index => $item) {
            CheckInChecklistItem::updateOrCreate(
                ['name' => $item['name']],
                [
                    'category' => $item['category'],
                    'order' => $index + 1,
                    'is_active' => true,
                ]
            );
        }
    }
}