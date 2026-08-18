# Instrucciones frontend (Tailwind + Tabulator + ApexCharts + Tom Select)

## Estilos y componentes
- Usar **Tailwind CSS** para todos los estilos (clases utilitarias).
- **Tabulator** para tablas dinámicas: búsqueda, paginación, filtros, ordenamiento.
- **ApexCharts** para gráficos (KPIs, reportes).
- **Tom Select** para autocompletado y selección de datos (clientes, vehículos, repuestos, servicios).

## Vistas
- Todas las vistas deben heredar del layout base (`layouts/app.blade.php`) que ya contiene las CDN.
- Usar secciones `@section('content')` para el contenido principal, y `@push('scripts')` para JavaScript específico de cada vista.

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

- Para un gráfico:
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

## Responsive
- Diseño mobile-first. Usar clases de Tailwind para diferentes tamaños (sm:, md:, lg:).
- Tablas de Tabulator se adaptan automáticamente con `responsiveLayout: 'collapse'`.
- Formularios en columnas que se apilan en móvil (grid-cols-1 md:grid-cols-2).