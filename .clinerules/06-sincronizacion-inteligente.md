# Sincronización inteligente de colecciones hijo (diff/upsert)

## Regla general

Al sincronizar colecciones hijo (daños, checklist, fotos, asignaciones, ítems de presupuesto, repuestos, vales, etc.) entre el formulario y la base de datos, **está prohibido** el patrón "borrar todo y volver a insertar" (delete-all + reinsertar).

En su lugar, usar **diff/upsert**:

1. Cargar los registros existentes de la colección (p. ej. `keyBy('id')` o por una clave natural estable como `checklist_item_id`).
2. Iterar lo que envía el formulario:
   - Si el registro ya existe (por `id` o clave natural) → `update()`.
   - Si no existe → `create()`.
   - Si la fila viene vacía y había un registro previo → eliminarlo.
3. Al final, eliminar únicamente los registros de BD que **no vienen** en el request.

## Beneficios

- Se conservan los `id`, `created_at` y `updated_at` de los registros existentes.
- Menos escrituras en BD (solo cambia lo que realmente cambió).
- Menor riesgo de pérdida de datos ante errores parciales (la operación sigue siendo transaccional).

## Excepciones

Esta regla **no aplica** cuando:

- El usuario lo pida explícitamente (p. ej. "reemplaza todos los X").
- Una librería o paquete de PHP lo haga directamente por nosotros (p. ej. `Model::sync()`, `Model::updateOrCreate()`, `Model::firstOrCreate()`, etc.).

## Ejemplo (daños / checklist)

Ver `app/Services/CheckInService.php` → `syncDamages()` y `syncChecklist()`, que ya implementan este patrón.