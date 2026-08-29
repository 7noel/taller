# Instrucciones frontend (Tailwind + Tabulator + ApexCharts + Tom Select)

## Estilos y componentes
- Usar **Tailwind CSS** para todos los estilos (clases utilitarias).
- **Tabulator** para tablas dinámicas: busqueda, paginacion, filtros, ordenamiento.
- **ApexCharts** para graficos (KPIs, reportes).
- **Tom Select** para autocompletado y seleccion de datos (clientes, vehiculos, repuestos, servicios). Todos los autocompletados deben ser **seleccion unica estricta** (ver patron estandar abajo).

## Vistas
- **REGLA OBLIGATORIA: patrón de componentes Blade.** Todas las vistas autenticadas deben usar `<x-app-layout>` (componente `layouts/app.blade.php` que ya contiene las CDN). **PROHIBIDO** usar `@extends('layouts.app')` + `@section('content')`: el layout renderiza `{{ $slot }}`, no `@yield`, por lo que mezclar ambos patrones causa el error `Undefined variable $slot`.
- **App shell y espaciado:** aplicar el patrón de `.clinerules/12-app-shell.md` (sidebar colapsable con usuario en el footer, topbar solo móvil, contenedor `py-6` + `px-4 sm:px-6 lg:px-8`, card `.card` con `p-4 sm:p-5` en listados o `p-6` en formularios). Las vistas `parties/index` y `vehicles/index` son las plantillas de referencia de listado.
- Estructura obligatoria:
  - `<x-slot name="header">` para el encabezado de la página (título y botones de acción).
  - Contenido principal directamente dentro de `<x-app-layout>` (slot por defecto).
  - `@push('scripts')` para JavaScript especifico de cada vista (se renderiza al final del layout dentro de `@stack('scripts')`).
- Ejemplo base (listado):
```blade
<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">{{ __('Título') }}</h2>
            <a href="#" class="btn btn-primary">+ Nuevo</a>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            {{-- flash --}}
            <div class="card overflow-hidden">
                <div class="p-4 sm:p-5">
                    {{-- buscador .search-input + tabulador --}}
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>// JS especifico</script>
    @endpush
</x-app-layout>
```
- Mensajes flash: mostrar `session('success')` en un bloque verde (`bg-green-50 border border-green-200 text-green-700 rounded-lg text-sm`) dentro del slot por defecto.
- Enlaces del menú en `layouts/navigation.blade.php`: `$navItems` para ítems de primer nivel y `$navGroups` para grupos con submenú colapsable (`label`, `icon`, `items[]` con `route`/`active`/`label`/`can`); se protegen con `@can('permiso')`, se resaltan con `request()->routeIs('ruta.*')` y el grupo de la ruta activa se abre automáticamente.

## Ejemplos de uso
- Para una tabla de clientes:
```blade
<div id="client-table"></div>
<script>
    new Tabulator('#client-table', {
        ajaxURL: '/api/clients',
        columns: [...],
        pagination: 'remote',
        ...
    });
</script>
```

- Para un grafico:
```blade
<div id="sales-chart"></div>
<script>
    const options = {...};
    new ApexCharts(document.querySelector('#sales-chart'), options).render();
</script>
```

- Para autocompletado:
```blade
<select id="client_id" name="client_id"></select>
<script>
    new TomSelect('#client_id', {
        valueField: 'id',
        labelField: 'business_name',
        searchField: ['business_name', 'document_number'],
        load: function(query, callback) { ... }
    });
</script>
```

## Estandar de autocompletado (Tom Select - seleccion unica estricta)

Configuracion obligatoria: `maxItems: 1`, `closeAfterSelect: true`, `create: false`, `copyClassesToDropdown: false`.

Handlers obligatorios:
- `item_add`: llamar `blur()` y `close()` para quitar el cursor y cerrar el dropdown al seleccionar.
- `dropdown_open`: si ya hay un item seleccionado, llamar `setTextValue('')` y colocar el cursor al inicio con `setSelectionRange(0,0)`, para que al reabrir la busqueda el texto previo no se mezcle ni el cursor salte de linea.

El CSS global en `layouts/app.blade.php` fuerza una sola linea (altura 2.5rem), oculta el cursor interno cuando hay seleccion y lo muestra en la misma linea al abrir el dropdown. No se debe usar `display:none` ni `display:block` en el input interno: usar `visibility` + `flex` para no romper la linea.

## Estandar de campos obligatorios (asterisco rojo en el label)
- En **todos los formularios** (crear/editar/modal): los campos obligatorios se marcan con un asterisco rojo `*` al costado del label (`<span class="text-red-500">*</span>`). Los campos opcionales **no** llevan asterisco.
- No se debe usar texto explicativo ni hints bajo los labels para indicar obligatoriedad: la regla visual es solo el asterisco.
- Mantener sincronizado el asterisco con la validacion backend (Form Requests): si es `required`, tiene asterisco; si es `nullable`, no.

## Responsive
- Diseno mobile-first. Usar clases de Tailwind para diferentes tamanos (sm:, md:, lg:).
- Tablas de Tabulator se adaptan automaticamente con `responsiveLayout: 'collapse'`.

## Regla `@json` en Blade (PROHIBIDO expresiones con comas)

El directivo `@json()` compila la expresion con `explode(',', ...)` (ver `Illuminate\View\Compilers\Concerns\CompilesJson`), por lo que **cualquier coma dentro de `@json(...)` trunca la expresion y genera PHP invalido** (error tipico al renderizar la vista: `ParseError: Unclosed '[' on line X does not match ')'`, HTTP 500).

- **PROHIBIDO**: `@json($model->items->map(fn ($i) => ['a' => $i->a, 'b' => $i->b])->values())` (comas dentro de la expresion).
- **OBLIGATORIO**: precalcular en un bloque `@php` y pasar solo la variable:
```blade
@php
    $itemsData = $model->items->map(fn ($i) => ['a' => $i->a, 'b' => $i->b])->values();
@endphp
const items = @json($itemsData);
```
- `@json()` recibe **solo variables o expresiones sin comas** (`@json($var)`, `@json($x ?? null)`, `@json($x->field)`). Nunca `map()`/`filter()`/`collect()` inline con comas.
- **Nota**: `php artisan view:cache` NO detecta este error (compila sin ejecutar); la falla aparece al renderizar. Tras `view:cache`, verificar con `php -l` sobre `storage/framework/views/*.php`.