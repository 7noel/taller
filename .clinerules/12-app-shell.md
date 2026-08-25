# App shell y patrón de listados (regla obligatoria)

Cumplimiento obligatorio para toda vista autenticada nueva o modificada del Taller Mecánico. Define el **app shell** (layout + navegación) y el **patrón de listado base** (Contactos/Vehículos son las vistas de referencia).

## App shell (layout + navegación) — `layouts/app.blade.php` + `layouts/navigation.blade.php`

- **Sidebar izquierda** fija en desktop (`lg+`): `w-64` expandida / `w-20` colapsada (solo iconos con `title`). Enlaces con **icono SVG + etiqueta**, estado activo `bg-blue-50 text-blue-700` + barra lateral azul de 3px, hover `bg-gray-50`, transición 150ms. Móvil/tablet: drawer con overlay + botón hamburguesa en una topbar delgada.
- **Colapso (desktop)**: botón chevrons en la cabecera de la sidebar; aplica la clase `app-sidebar-collapsed` en `<html>`, persiste en `localStorage` (`sidebar-collapsed`) y se restaura **antes del paint** (script inline en `<head>`). Los offsets (`padding-left`) de `.app-nav-wrap` y `main` cambian 16rem ↔ 5rem con transición 200ms.
- **Usuario + dropdown en el footer de la sidebar** (`border-t`): avatar + nombre (se oculta colapsado) + chevron; dropdown abre **hacia arriba** (`bottom-full`, sombra, z-60) con **Mi Perfil** y **Cerrar sesión** (`form` con `@csrf`, cubierto por `form-guard`).
- **Topbar**: solo móvil/tablet (`lg:hidden`): hamburguesa + título de la página.
- **Header de página** (`<x-slot name="header">` de cada vista): banda blanca `border-b border-gray-200`, `px-4 py-4 sm:px-6 lg:px-8`, renderizada por la navegación (no duplicar un `<header>` en el layout).
- Fondo de página `bg-gray-50`; contenido dentro de `<main>` (offset `lg:pl-64` gestionado por CSS del app shell).
- Se conservan obligatoriamente: `form-guard`, `confirm-modal`, `@stack('styles')` / `@stack('scripts')`, CDN (Tailwind, Tabulator, Tom Select, ApexCharts).

## Patrón de contenedor de vista (obligatorio)

Toda vista autenticada usa este esquema de espaciado compacto (sustituye al antiguo `py-12`):

```html
<div class="py-6">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        {{-- flash + contenido --}}
    </div>
</div>
```

- `py-6` (máx. 24px verticales) en lugar de `py-12` (48px). No usar `py-10`, `py-8` ni padding vertical mayor salvo que el contenido lo justifique.
- Contenedor `max-w-7xl` con `px-4 sm:px-6 lg:px-8`.
- Cards/paneles: `.card` con `rounded-lg border border-gray-200 shadow-sm`; padding interior `p-4 sm:p-5` (listados) o `p-6` (formularios extensos).

## Patrón de listado de referencia — base "Contactos/Vehículos"

Las vistas `parties/index` y `vehicles/index` son las **plantillas de referencia** para cualquier listado nuevo (practicamente idénticas). Estructura obligatoria:

1. `<x-slot name="header">` con `<h2>` + botón de acción `.btn-primary` (icono SVG + etiqueta).
2. Contenedor `py-6 / max-w-7xl px-4 sm:px-6 lg:px-8`.
3. Flash de éxito: `mb-4 px-4 py-3 bg-green-50 border border-green-200 text-green-700 rounded-lg text-sm`.
4. `card overflow-hidden` con `p-4 sm:p-5`.
5. Buscador `.search-input` dentro de `relative max-w-md` con icono de lupa SVG absoluto a la izquierda, `mb-3`.
6. Tabulator con: `layout: 'fitColumns'`, `responsiveLayout: 'collapse'`, `height: 'auto'`, `placeholder` que comunica el vacío, y columna **Acciones** con `formatter` en JS que construye botones `.btn-icon` (ver/editar/eliminar con `title`; eliminar con `data-confirm`). Nunca enviar HTML de acciones en el JSON de la API.
7. `@push('scripts')` para el JS específico (Tabulator, Tom Select, listeners).

## Reglas transversales que se mantienen

- Campos obligatorios con asterisco rojo (`<span class="text-red-500">*</span>`), sync con Form Requests (`.clinerules/03`).
- Formularios: `@csrf`, `data-confirm` para destructivos, anti-doble envío y renovación CSRF globales (`.clinerules/08`).
- Estados/acciones en badges, menú de acciones compacto, sin timestamps en listados, `responsiveLayout: 'collapse'` (`.clinerules/11`).
- Sistema de diseño Restrained: botones `.btn*`, cards, búsqueda, tema Tabulator, tipografía y colores (`.clinerules/07`).
- Documentos emitidos: identidad con `document_sn` y componente `x-document-badge` (`.clinerules/10`).