# Sistema de diseño Frontend (reglas obligatorias)

Estas reglas son de **cumplimiento obligatorio** para todas las vistas del Taller Mecánico. Se definieron con el skill **impeccable** (modo Operate) y aplican a cualquier vista nueva o existente.

## Principios
- **Modo Operate:** la interfaz acompaña la tarea; familiaridad y consistencia ganan sobre la sorpresa.
- **Restrained:** paleta sobria; el acento azul se reserva para acciones primarias, selección y estados (nunca decoración).
- **Micro-interacciones:** transiciones de **150–250 ms** (usar 150 ms) con `ease-out`; solo para estados (hover/focus/active), no para coreografías de carga.
- **Consistencia:** el mismo vocabulario visual en todas las pantallas. Un botón igual se ve igual en todos lados.

## Paleta de colores
- Primario/acento: `#2563eb` (blue-600); hover `#1d4ed8` (blue-700); anillo focus `rgba(59,130,246,.5)`.
- Neutros: fondo de página `#f3f4f6` (gray-100); superficie `#ffffff`; bordes `#e5e7eb` (gray-200); cabecera de tabla `#f8fafc`; texto primario `#111827`, secundario `#4b5563`, terciario `#6b7280`.
- Estados semánticos:
  - Error → `red-600/red-50`
  - Advertencia → `amber-600/amber-50`
  - Éxito → `green-600/green-50`
  - Info → `blue-600/blue-50`
- Texto sobre superficies de color: tintar del matiz (ej. `text-green-700` sobre `bg-green-50`), nunca gris plano.
- `::selection` = `#bfdbfe` sobre `#1e3a8a`.

## Tipografía
- Familia: sans de sistema (Tailwind `font-sans`); una sola familia. Sin fuentes display en UI.
- Escala fija:
  - Header de página `<h2>`: `text-xl font-semibold text-gray-800`.
  - Labels de filtros: `text-sm font-medium text-gray-700`.
  - Datos de tabla: `text-sm (0.875rem)`.
  - Cabeceras de tabla: `text-xs font-semibold uppercase tracking-wider text-gray-500`.
- Radiaciones de borde: `rounded-lg` en botones/cards/inputs de búsqueda; `rounded-md` en inputs de formulario y chips.

## Sombras
- Sombras sutiles con offset + blur: `shadow-sm` (0 1px 2px rgb(0 0 0 / 0.05)).
- Prohibido: halo de color sin offset; `box-shadow: 4px 4px 0` (neobrutalismo) salvo que la vista lo pida.

## Componentes (definidos en `resources/views/layouts/app.blade.php`)

### Botones
- `.btn` base + modificadores `.btn-primary` (azul sólido, acción principal), `.btn-secondary` (blanco con borde, acción secundaria), `.btn-danger` (rojo, destructivo).
- Todo botón: `inline-flex items-center gap-2 rounded-lg px-4 py-2 text-sm font-semibold`, transición 150 ms, `focus-visible:ring-2 ring-offset-2`, `disabled:opacity-50 pointer-events-none`.
- **Botones de icono en tablas:** `.btn-icon` + `.btn-icon-blue` (ver), `.btn-icon-amber` (editar), `.btn-icon-red` (eliminar). Tamaño `h-8 w-8`, fondo `*-50`, color `*-600`, hover `*-100`/`*-700`, siempre con `title` descriptivo.
- Iconos: SVG inline, trazo `stroke-width="2"`, `h-4 w-4`. Prohibido emoji/unicode como iconos de sistema.

### Cards/contenedores
- `.card`: `bg-white rounded-lg border border-gray-200 shadow-sm` (reemplaza `bg-white shadow-sm sm:rounded-lg`).
- Mensaje flash de éxito: `bg-green-50 border border-green-200 text-green-700 rounded-lg text-sm`.

### Búsqueda
- `.search-input` con icono de lupa SVG absoluto a la izquierda (`absolute left-3 top-1/2 -translate-y-1/2`), contenedor `relative max-w-md`.

### Tablas (Tabulator)
- Tema global en `layouts/app.blade.php`: cabecera `#f8fafc`, texto uppercase `0.75rem`, filas blancas con `hover: #eff6ff`, bordes `#f1f5f9`, celdas `padding: .625rem .75rem`, placeholder `#94a3b8`.
- Configuración obligatoria: `layout: 'fitColumns'`, `responsiveLayout: 'collapse'`, `height: 'auto'`, `placeholder` personalizado.
- Columna Acciones: siempre `formatter` en JavaScript que construye los botones `.btn-icon` (nunca enviar HTML en el JSON de la API).
- Paginación remota activa con color primario en la página actual.

### Formularios e inputs
- Labels con asterisco rojo para obligatorios (ver `03-frontend.md`).
- Autocompletados Tom Select: selección única estricta (`maxItems:1`, `closeAfterSelect:true`, `create:false`, `copyClassesToDropdown:false`), handlers `item_add` (blur+close) y `dropdown_open` (reset texto), y `dropdownParent: 'body'` para dropdowns que salen del contenedor.

## Estados UX
- **Carga:** skeletons o placeholder; evitar spinners centrados.
- **Vacío:** `placeholder` de Tabulator que enseña ("No hay ... registrados").
- **Error en tabla:** el placeholder comunica el problema; errores de formulario bajo el campo con `text-red-600 text-sm`.
- **Hover/focus en filas y botones:** siempre presentes (150 ms).

## Responsividad
- Mobile-first. `grid` `md:`/`lg:` para filtros. `responsiveLayout: 'collapse'` en todas las tablas. `max-w-7xl` en el contenedor de contenido.

## Verificación (antes de dar por terminada una vista)
- [ ] Usa `<x-app-layout>` con `<x-slot name="header">` (nunca `@extends`).
- [ ] Botones con clases `.btn*` / `.btn-icon*` y `title`.
- [ ] Tabla Tabulator con el tema del layout y columna Acciones con formatter.
- [ ] Búsqueda con `.search-input` e icono de lupa.
- [ ] Mensajes flash verde suave (`green-50`).
- [ ] Sin emoji en lugares de iconos SVG; sin gradientes de texto ni sombras duras.