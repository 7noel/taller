# Instrucciones frontend (Tailwind + Tabulator + ApexCharts + Tom Select)

## Estilos y componentes
- Usar **Tailwind CSS** para todos los estilos (clases utilitarias).
- **Tabulator** para tablas dinámicas: busqueda, paginacion, filtros, ordenamiento.
- **ApexCharts** para graficos (KPIs, reportes).
- **Tom Select** para autocompletado y seleccion de datos (clientes, vehiculos, repuestos, servicios). Todos los autocompletados deben ser **seleccion unica estricta** (ver patron estandar abajo).

## Vistas
- Todas las vistas deben heredar del layout base (`layouts/app.blade.php`) que ya contiene las CDN.
- Usar secciones `@section('content')` para el contenido principal, y `@push('scripts')` para JavaScript especifico de cada vista.

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

## Responsive
- Diseno mobile-first. Usar clases de Tailwind para diferentes tamanos (sm:, md:, lg:).
- Tablas de Tabulator se adaptan automaticamente con `responsiveLayout: 'collapse'`.
- Formularios en columnas que se apilan en movil (grid-cols-1 md:grid-cols-2).