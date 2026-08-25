<?php

namespace Database\Seeders;

use App\Models\DocumentType;
use Illuminate\Database\Seeder;

class DocumentTypeSeeder extends Seeder
{
    public function run(): void
    {
        // Códigos SUNAT según cat_document_types del facturador:
        // - '07' Nota de Crédito genera 2 series (FTC1 factura, BLC1 boleta)
        // - '08' Nota de Débito genera 2 series (FTD1 factura, BLD1 boleta)
        $documents = [
            ['code' => '01', 'name' => 'Factura Electrónica', 'is_electronic' => true],
            ['code' => '03', 'name' => 'Boleta de Venta Electrónica', 'is_electronic' => true],
            ['code' => '07', 'name' => 'Nota de Crédito', 'is_electronic' => true],
            ['code' => '08', 'name' => 'Nota de Débito', 'is_electronic' => true],
            ['code' => '09', 'name' => 'Guía de Remisión Remitente', 'is_electronic' => true],
            ['code' => '80', 'name' => 'Nota de Venta', 'is_electronic' => false],
            ['code' => 'U2', 'name' => 'Guía de Ingreso Establecimiento', 'is_electronic' => false],
            ['code' => 'U3', 'name' => 'Guía de Salida Establecimiento', 'is_electronic' => false],
            ['code' => 'U4', 'name' => 'Guía de Transferencia Establecimiento', 'is_electronic' => false],
            ['code' => 'PRE', 'name' => 'Presupuesto', 'is_electronic' => false],
            ['code' => 'OT', 'name' => 'Orden de Trabajo', 'is_electronic' => false],
            ['code' => 'IV', 'name' => 'Inventario Vehicular', 'is_electronic' => false],
            ['code' => 'CST', 'name' => 'Comprobante de Servicio Tercerizado', 'is_electronic' => false],
            ['code' => 'LST', 'name' => 'Liquidación de Servicios Tercerizados', 'is_electronic' => false],
        ];

        foreach ($documents as $document) {
            DocumentType::updateOrCreate(
                ['code' => $document['code']],
                [
                    'name' => $document['name'],
                    'is_electronic' => $document['is_electronic'],
                    'is_active' => true,
                ]
            );
        }
    }
}