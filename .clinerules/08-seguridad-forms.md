# Seguridad y UX en formularios (reglas obligatorias)

Cumplimiento obligatorio para toda vista o formulario nuevo del Taller Mecánico (páginas independientes, modales y acciones de estado).

## Regla CSRF: renovar el token justo antes del envío

Todo formulario que requiera token CSRF debe obtener y actualizar el token justo antes de su envío (o renovarlo en segundo plano), para evitar errores de expiración.

### Implementación estándar (no duplicar)

- Partial global: `resources/views/partials/form-guard.blade.php`
  - Incluido en `layouts/app.blade.php` y `layouts/guest.blade.php`.
  - Intercepta el submit de cualquier formulario (delegación de eventos).
  - Llama a `GET /api/csrf-token` (ruta pública en `routes/web.php`) y actualiza el meta `csrf-token` y los inputs ocultos `_token` antes de enviar.
- No añadir lógica CSRF adicional por vista: el partial cubre formularios estáticos y los generados por Tabulator.

### Obligaciones al crear un formulario nuevo

1. Usar `@csrf` (el guard renueva el token).
2. Los fetch AJAX con POST/PUT/DELETE deben leer el token del meta `csrf-token` en el momento del envío (no guardarlo en constante al cargar).

## Regla de Confirmación de Eliminación (modal global, prohibido `confirm()` del navegador)

Toda acción destructiva (eliminar un registro, copiar datos, regenerar series, etc.) debe confirmarse con el **modal global de confirmación** del proyecto, **nunca** con `onsubmit="return confirm(...)"` del navegador.

### Implementación estándar (no duplicar)

- Partial global: `resources/views/partials/confirm-modal.blade.php`
  - Incluido en `layouts/app.blade.php` (antes de `form-guard`).
  - Expone `window.ConfirmModal.open(form, { message?, confirmLabel? })`.
- El `form-guard.blade.php` ya intercepta cualquier `<form data-confirm="mensaje...">`: abre el modal y detiene el envío hasta que el usuario confirme (`data-confirmed="1"`). Si cancela, NO se ejecuta nada.
- Al confirmar, se llama `form.requestSubmit()`, que vuelve a pasar por form-guard (refresh CSRF + anti-doble envío) y recién envía.

### Obligaciones al crear un formulario destructivo

1. Agregar `data-confirm="¿Estás seguro de eliminar ...?"` al `<form method="POST">`.
2. Mantener `@csrf` y `@method('DELETE')` (el guard renueva el token).
3. **PROHIBIDO** usar `onsubmit="return confirm('...')"` o `onsubmit` con `prompt()` para acciones destructivas.
4. Si hace falta un mensaje específico, pasar parámetros por `data-confirm` (mensaje) y opcional `data-confirm-label` (default "Eliminar").

## Regla Anti-Duplicados: flag booleano + botón deshabilitado

Todos los formularios de creación o edición deben deshabilitar su botón de envío inmediatamente después del primer clic y usar una bandera booleana para evitar el doble clic y la duplicidad de datos.

### Implementación estándar (formularios tradicionales)

El partial `form-guard.blade.php` ya:

- Marca el formulario con `data-submitting="1"` (flag) antes del refresh CSRF, bloqueando doble clic ráfaga.
- Deshabilita el botón submit y muestra spinner + "Guardando..." (personalizable con `data-loading-text` en el botón o el form).
- Si un `onsubmit` inline cancela (confirm/prompt rechazado), no marca el formulario y restaura todo.
- En error de servidor el formulario se re-renderiza con validación y el botón se re-habilita.
- En éxito el formulario navega y el flag no se restaura (correcto).

### Implementación estándar (modales AJAX)

En cualquier modal que guarde por fetch (ej.: `partials/contact-modal.blade.php`, `check-ins/_vehicle_modal.blade.php`):

- Variable `saving = false` como flag.
- En el click: si `saving` es true, retornar (bloquear reentrada); poner `saving = true`; llamar al guardado; en `finally` poner `saving = false`.
- Dentro del guardado: deshabilitar el botón, cambiar texto a "Guardando...", y en `finally` re-habilitar botón y restaurar texto.
- En éxito cerrar el modal; en error mostrar mensaje para reintentar.

### Obligaciones al crear un formulario o modal nuevo

1. Formulario tradicional: el partial ya lo cubre, no se requiere nada extra.
2. Modal AJAX: obligatorio flag booleano + deshabilitar botón + restaurar en `finally` + cerrar modal en éxito.
3. Prohibido el doble envío por doble clic o Enter accidental.
