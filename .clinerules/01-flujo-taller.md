# Flujo de trabajo del taller (etapas y entidades)

## Etapas del flujo
1. **Inventario** (ingreso del vehículo, checklist, daños, fotos).
2. **Presupuesto** (cotización, servicios, repuestos, órdenes de compra, franquicia).
3. **Aprobación** (seguro y/o cliente).
4. **Reparación** (órdenes de trabajo, subetapas, asignación de técnicos).
5. **Control de calidad** (checklist de calidad).
6. **Entrega** (registro de salida, encuesta de satisfacción).

## Entidades principales y sus estados
- **Inventario**: Borrador, Pendiente aprobación cliente, Aprobado por cliente, Rechazado por cliente, Cerrado.
- **Presupuesto**: Borrador, En aprobación seguro, Aprobado seguro, Rechazado seguro, En aprobación cliente, Aprobado cliente, Rechazado cliente, En reparación, Finalizado.
- **Orden de Trabajo (OT)**: Abierta, En progreso, En espera de repuestos, En control de calidad, Lista para entrega, Entregada, Cerrada.
- **Comprobante de Servicio (Vale)**: Pendiente, Completado, Liquidado.
- **Liquidación (Planilla)**: Borrador, Aprobado, Pagado.
- **Repuesto Pedido (de seguro)**: Pendiente de pedido, Pedido realizado, En camino/Importación, En almacén, Entregado a técnico.
- **Movimiento de Stock**: Entrada, Salida, Ajuste.
- **Cita**: Agendada, Confirmada, Cancelada, Realizada.
- **Factura**: Emitida, Pagada parcial, Pagada total, Anulada.

## Nota
Cada etapa del flujo se refleja en el tablero Kanban con columnas: Inventario, Presupuesto, Aprobación, Reparación, Control de Calidad, Entrega.