<?php

namespace Database\Seeders;

use App\Models\CheckInChecklistItem;
use Illuminate\Database\Seeder;

class ChecklistItemsSeeder extends Seeder
{
    /**
     * Lista manual de ítems del checklist vehicular (única fuente de datos).
     */
    private function manualItems(): array
    {
        return [
            // EXTERIOR
            ['name' => 'PLUMILLAS', 'category' => 'EXTERIOR'],
            ['name' => 'PARABRISA DELANTERO', 'category' => 'EXTERIOR'],
            ['name' => 'FARO POSTERIOR', 'category' => 'EXTERIOR'],
            ['name' => 'SEGURO DE AROS', 'category' => 'EXTERIOR'],
            ['name' => 'TAPA DE COMBUSTIBLE', 'category' => 'EXTERIOR'],
            ['name' => 'BRAZO DE PLUMILLAS', 'category' => 'EXTERIOR'],
            ['name' => 'PARABRISA POSTERIOR', 'category' => 'EXTERIOR'],
            ['name' => 'MANIJA EXTERIOR', 'category' => 'EXTERIOR'],
            ['name' => 'MOL. PUERTA', 'category' => 'EXTERIOR'],
            ['name' => 'LLANTAS', 'category' => 'EXTERIOR'],
            ['name' => 'ESPEJOS EXTERIORES', 'category' => 'EXTERIOR'],
            ['name' => 'FARO DELANTERO', 'category' => 'EXTERIOR'],
            ['name' => 'NEBLINEROS', 'category' => 'EXTERIOR'],
            ['name' => 'SEGURO DE VASOS', 'category' => 'EXTERIOR'],
            ['name' => 'ANTENA', 'category' => 'EXTERIOR'],
            ['name' => 'VASO/COPA', 'category' => 'EXTERIOR'],
            // MOTOR
            ['name' => 'BATERIA', 'category' => 'MOTOR'],
            ['name' => 'PURIFICADOR', 'category' => 'MOTOR'],
            ['name' => 'TAPA LIQUI. EMBRAGUE', 'category' => 'MOTOR'],
            ['name' => 'TAPA DE RADIADOR', 'category' => 'MOTOR'],
            ['name' => 'SOPORTE DE BATERIA', 'category' => 'MOTOR'],
            ['name' => 'TAPA LIQUI. FRENO', 'category' => 'MOTOR'],
            ['name' => 'TAPA LIQUI. DIRECCION', 'category' => 'MOTOR'],
            ['name' => 'TAPA DE ACEITE', 'category' => 'MOTOR'],
            ['name' => 'GNV-GLP', 'category' => 'MOTOR'],
            ['name' => 'VARILLA ATM', 'category' => 'MOTOR'],
            ['name' => 'VARILLA DE ACEITE', 'category' => 'MOTOR'],
            ['name' => 'GATA', 'category' => 'MOTOR'],
            // INTERIOR
            ['name' => 'TABLERO', 'category' => 'INTERIOR'],
            ['name' => 'CENICERO', 'category' => 'INTERIOR'],
            ['name' => 'CABEZAL DE ASIENTO', 'category' => 'INTERIOR'],
            ['name' => 'ABRE PUERTAS', 'category' => 'INTERIOR'],
            ['name' => 'PISOS SOBRE ALFOMBRAS', 'category' => 'INTERIOR'],
            ['name' => 'TAPIZ DE ASIENTOS', 'category' => 'INTERIOR'],
            ['name' => 'ENCENDEDOR', 'category' => 'INTERIOR'],
            ['name' => 'RADIO', 'category' => 'INTERIOR'],
            ['name' => 'ALZA LUNAS', 'category' => 'INTERIOR'],
            ['name' => 'TAPASOL', 'category' => 'INTERIOR'],
            ['name' => 'TAPIZ DE PUERTA', 'category' => 'INTERIOR'],
            ['name' => 'ESPEJOS INTERIORES', 'category' => 'INTERIOR'],
            ['name' => 'RELOJ', 'category' => 'INTERIOR'],
            ['name' => 'CLAXON', 'category' => 'INTERIOR'],
            ['name' => 'CODERAS', 'category' => 'INTERIOR'],
            ['name' => 'ALARMA', 'category' => 'INTERIOR'],
            // HERRAMIENTAS/EMERGENCIA
            ['name' => 'ALFOMBRA EN MALETERA', 'category' => 'HERRAMIENTAS/EMERGENCIA'],
            ['name' => 'PALANCA DE GATA', 'category' => 'HERRAMIENTAS/EMERGENCIA'],
            ['name' => 'HERRAMIENTAS', 'category' => 'HERRAMIENTAS/EMERGENCIA'],
            ['name' => 'LLAVE DE RUEDAS', 'category' => 'HERRAMIENTAS/EMERGENCIA'],
            ['name' => 'EXTINGUIDOR', 'category' => 'HERRAMIENTAS/EMERGENCIA'],
            ['name' => 'TRIANGULO', 'category' => 'HERRAMIENTAS/EMERGENCIA'],
            ['name' => 'COCODRILOS', 'category' => 'HERRAMIENTAS/EMERGENCIA'],
            ['name' => 'LLANTA DE REPUESTO', 'category' => 'HERRAMIENTAS/EMERGENCIA'],
        ];
    }

    public function run(): void
    {
        // Insertar de forma idempotente
        foreach ($this->manualItems() as $index => $item) {
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