# Tablas y listados compactos (regla obligatoria)

Cumplimiento obligatorio para todos los listados y tablas de administración del Taller Mecánico.

## Regla principal

Todos los listados y tablas deben ser **compactos y escaneables**:

1. **Badges para estados:** los estados (activo/inactivo, borrador/aprobado, LOCAL/API, etc.) se muestran como píldoras ("pill"/"badge") pequeñas con color semántico (verde éxito, rojo error, ámbar advertencia, azul info, gris neutro), nunca como columnas de texto crudo redundantes.
2. **Menú de acciones agrupado:** las acciones por fila (ver/editar/eliminar) se agrupan en un único menú desplegable (tres puntos verticales `⋮`) o, si son pocas, en iconos compactos `.btn-icon` con `title`. Prohibido renderizar formularios de edición inline dentro de la fila.
3. **Prohibido mostrar timestamps en tablas de administración:** `created_at`, `updated_at` (y otras columnas tipo "fecha de creación/actualización") no deben exponerse en los listados de backoffice. Se reservan para auditoría (activity log), no para la UI.
4. **Agrupar información relacionada:** en lugar de columnas separadas que fragmentan un mismo concepto (ej. "Prefijo" + "Número actual"), mostrarlas en una sola columna legible (ej. "Serie Actual" → `IV01-00015`).
5. **Sin columnas saturadas:** eliminar columnas irrelevantes para el usuario final (timestamps, códigos internos redundantes, estado duplicado).

## Patrón de menú de acciones (tres puntos)

```js
// Columna Tabulator
{
  title: '', field: 'id', width: 56, hozAlign: 'center', headerSort: false,
  formatter: cell => `<button type="button" class="btn-icon ..." data-id="${cell.getData().id}">⋮</button>`
}
```

- Delegación de eventos en `document` para abrir el menú.
- Cerrar con clic fuera, `Escape` o scroll.
- Opciones del menú: Editar / Eliminar, con sus acciones y confirmación.

## Responsividad

- Tabulator: `responsiveLayout: 'collapse'` (transforma a tarjetas/acordeón en móvil).
- `layout: 'fitColumns'`, `height: 'auto'`, `placeholder` personalizado que comunica el estado vacío ("No hay ... registrados").

## Verificación

- [ ] Estados en badges, no texto crudo.
- [ ] Acciones agrupadas en menú `⋮` o iconos `.btn-icon` con `title`.
- [ ] Sin columnas `created_at`/`updated_at` en el listado.
- [ ] Información relacionada agrupada en una sola columna.
- [ ] `responsiveLayout: 'collapse'` habilitado.