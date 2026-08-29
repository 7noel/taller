<?php

namespace Database\Seeders;

use App\Models\InventoryMovementReason;
use Illuminate\Database\Seeder;

/**
 * Motivos de ingreso/salida de existencias (hardcodeado, como la tabla
 * inventory_transactions del facturador anterior). No se edita en la UI.
 */
class InventoryMovementReasonSeeder extends Seeder
{
    public function run(): void
    {
        $reasons = [
            // Ingresos (input)
            ['code' => '02', 'name' => 'Compra nacional', 'type' => 'input'],
            ['code' => '03', 'name' => 'Consignación recibida', 'type' => 'input'],
            ['code' => '05', 'name' => 'Devolución recibida', 'type' => 'input'],
            ['code' => '16', 'name' => 'Inventario inicial', 'type' => 'input'],
            ['code' => '18', 'name' => 'Entrada de importación', 'type' => 'input'],
            ['code' => '19', 'name' => 'Ingreso de producción', 'type' => 'input'],
            ['code' => '20', 'name' => 'Entrada por devolución de producción', 'type' => 'input'],
            ['code' => '21', 'name' => 'Entrada por transferencia entre almacenes', 'type' => 'input'],
            ['code' => '22', 'name' => 'Entrada por identificación errónea', 'type' => 'input'],
            ['code' => '24', 'name' => 'Entrada por devolución del cliente', 'type' => 'input'],
            ['code' => '26', 'name' => 'Entrada para servicio de producción', 'type' => 'input'],
            ['code' => '29', 'name' => 'Entrada de bienes en préstamo', 'type' => 'input'],
            ['code' => '31', 'name' => 'Entrada de bienes en custodia', 'type' => 'input'],
            ['code' => '50', 'name' => 'Ingreso temporal', 'type' => 'input'],
            ['code' => '52', 'name' => 'Ingreso por transformación', 'type' => 'input'],
            ['code' => '54', 'name' => 'Ingreso de producción', 'type' => 'input'],
            ['code' => '55', 'name' => 'Entrada de importación', 'type' => 'input'],
            ['code' => '57', 'name' => 'Entrada por conversión de medida', 'type' => 'input'],
            ['code' => '91', 'name' => 'Ingreso por transformación', 'type' => 'input'],
            ['code' => '93', 'name' => 'Ingreso temporal', 'type' => 'input'],
            ['code' => '96', 'name' => 'Entrada por conversión de medida', 'type' => 'input'],
            ['code' => '99', 'name' => 'Otros', 'type' => 'input'],
            ['code' => '100', 'name' => 'Ingreso insumos por molino', 'type' => 'input'],
            ['code' => '102', 'name' => 'Entrada por importación masiva (xlsx)', 'type' => 'input'],
            // Salidas (output)
            ['code' => '01', 'name' => 'Venta nacional', 'type' => 'output'],
            ['code' => '04', 'name' => 'Consignación entregada', 'type' => 'output'],
            ['code' => '06', 'name' => 'Devolución entregada', 'type' => 'output'],
            ['code' => '07', 'name' => 'Bonificación', 'type' => 'output'],
            ['code' => '08', 'name' => 'Premio', 'type' => 'output'],
            ['code' => '09', 'name' => 'Donación', 'type' => 'output'],
            ['code' => '10', 'name' => 'Salida a producción', 'type' => 'output'],
            ['code' => '11', 'name' => 'Salida por transferencia entre almacenes', 'type' => 'output'],
            ['code' => '12', 'name' => 'Retiro', 'type' => 'output'],
            ['code' => '13', 'name' => 'Mermas', 'type' => 'output'],
            ['code' => '14', 'name' => 'Desmedros', 'type' => 'output'],
            ['code' => '15', 'name' => 'Destrucción', 'type' => 'output'],
            ['code' => '17', 'name' => 'Exportación', 'type' => 'output'],
            ['code' => '23', 'name' => 'Salida por identificación errónea', 'type' => 'output'],
            ['code' => '25', 'name' => 'Salida por devolución al proveedor', 'type' => 'output'],
            ['code' => '27', 'name' => 'Salida por servicio de producción', 'type' => 'output'],
            ['code' => '28', 'name' => 'Ajuste por diferencia de inventario', 'type' => 'output'],
            ['code' => '30', 'name' => 'Salida de bienes en préstamo', 'type' => 'output'],
            ['code' => '32', 'name' => 'Salida de bienes en custodia', 'type' => 'output'],
            ['code' => '33', 'name' => 'Muestras médicas', 'type' => 'output'],
            ['code' => '34', 'name' => 'Publicidad', 'type' => 'output'],
            ['code' => '35', 'name' => 'Gastos de representación', 'type' => 'output'],
            ['code' => '36', 'name' => 'Retiro para entrega a trabajadores', 'type' => 'output'],
            ['code' => '37', 'name' => 'Retiro por convenio colectivo', 'type' => 'output'],
            ['code' => '38', 'name' => 'Retiro por sustitución de bien siniestrado', 'type' => 'output'],
            ['code' => '51', 'name' => 'Salida temporal', 'type' => 'output'],
            ['code' => '53', 'name' => 'Salida para servicios terceros', 'type' => 'output'],
            ['code' => '56', 'name' => 'Salida por conversión de medida', 'type' => 'output'],
            ['code' => '101', 'name' => 'Salida por insumo', 'type' => 'output'],
        ];

        foreach ($reasons as $reason) {
            InventoryMovementReason::updateOrCreate(
                ['code' => $reason['code']],
                ['name' => $reason['name'], 'type' => $reason['type']]
            );
        }
    }
}
