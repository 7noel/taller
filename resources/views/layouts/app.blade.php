<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ $title ?? config('app.name', 'Taller Mecánico') }}</title>

    {{-- Aplicar el estado colapsado de la sidebar ANTES del paint (evita parpadeo) --}}
    <script>
    (function () {
        try {
            if (localStorage.getItem('sidebar-collapsed') === '1') {
                document.documentElement.classList.add('app-sidebar-collapsed');
            }
        } catch (e) {}
    })();
    </script>

    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    @include('partials.design-base')
    <style type="text/tailwindcss">
        /* ===== Tom Select: estilos consistentes con Tailwind ===== */
        .ts-wrapper .ts-control,
        .ts-wrapper.single .ts-control,
        .ts-wrapper.multi .ts-control {
            @apply flex w-full rounded-md border-gray-300 bg-white px-3 py-2 text-sm text-gray-900 shadow-sm
                   focus:border-blue-500 focus:ring-blue-500 disabled:bg-gray-50 disabled:text-gray-500;
            min-height: 2.5rem;
            align-items: center;
        }
        .ts-wrapper .ts-control > input {
            font-size: 0.875rem;
            padding: 0;
            border: 0;
            box-shadow: none;
        }
        .ts-wrapper .ts-control,
        .ts-wrapper.focus .ts-control,
        .ts-control:hover {
            border-color: #d1d5db;
            box-shadow: 0 1px 2px 0 rgb(0 0 0 / 0.05);
        }
        .ts-wrapper.focus .ts-control {
            border-color: #3b82f6;
            box-shadow: 0 0 0 3px rgb(59 130 246 / 0.5);
        }
        .ts-wrapper .ts-dropdown {
            @apply rounded-md border-gray-300 bg-white text-sm text-gray-900 shadow-sm;
        }
        .ts-wrapper .ts-dropdown .ts-dropdown-content .option {
            @apply cursor-pointer px-3 py-2 text-gray-700;
            border-left: 3px solid transparent;
            transition: background-color 0.2s ease, color 0.2s ease, border-left-color 0.2s ease;
        }
        .ts-wrapper .ts-dropdown .ts-dropdown-content .option:hover {
            background-color: #f1f3f5;
            color: #212529;
        }
        .ts-wrapper .ts-dropdown .ts-dropdown-content .option.active,
        .ts-wrapper .ts-dropdown .ts-dropdown-content .option.selected {
            background-color: #e7f1ff;
            border-left-color: #0d6efd;
            color: #212529;
        }
        .ts-wrapper .ts-dropdown .ts-dropdown-content .option.active .suboption,
        .ts-wrapper .ts-dropdown .ts-dropdown-content .option.selected .suboption {
            color: #495057;
        }
        .ts-wrapper .ts-dropdown .ts-dropdown-content .option .suboption {
            @apply block text-xs;
            color: #6c757d;
        }
        .ts-wrapper .ts-dropdown .ts-dropdown-content .no-results {
            @apply px-3 py-2 text-gray-500;
        }
        .ts-wrapper .ts-item {
            @apply bg-blue-100 text-blue-800 px-2 py-0.5 rounded-md text-sm;
        }
        /* ===== Single mode estricto: 1 línea, sin cursor parpadeante ===== */
        .ts-wrapper.single .ts-control {
            height: 2.5rem;
            min-height: 2.5rem;
            max-height: 2.5rem;
            overflow: hidden;
            flex-wrap: nowrap;
            white-space: nowrap;
        }
        .ts-wrapper.single .ts-control .item {
            @apply text-sm text-gray-900;
            background: transparent;
            padding: 0;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
            flex: 0 1 auto;
            min-width: 0;
            max-width: 60%;
        }
        /* Oculta el input interno (cursor) sin sacarlo del flujo: se mantiene 1 línea */
        .ts-wrapper.single.has-items .ts-control > input {
            visibility: hidden;
            width: 0;
            padding: 0;
            flex: 0 0 0;
        }
        /* Al abrir el dropdown: el input reaparece en la MISMA línea, listo para escribir */
        .ts-wrapper.single.has-items.dropdown-active .ts-control > input {
            visibility: visible;
            width: auto;
            flex: 1 1 0%;
            min-width: 40px;
        }
    </style>
    <style type="text/tailwindcss">
        /* ===== Sistema de diseño: componentes reutilizables (modo Operate) ===== */
        @layer components {
            .btn {
                @apply inline-flex items-center gap-2 rounded-lg px-4 py-2 text-sm font-semibold transition-all duration-150 ease-out focus:outline-none focus-visible:ring-2 focus-visible:ring-offset-2 disabled:opacity-50 disabled:pointer-events-none;
            }
            .btn-primary {
                @apply bg-blue-600 text-white shadow-sm hover:bg-blue-700 hover:shadow focus-visible:ring-blue-500 active:bg-blue-800;
            }
            .btn-secondary {
                @apply bg-white text-gray-700 border border-gray-300 shadow-sm hover:bg-gray-50 focus-visible:ring-gray-400 active:bg-gray-100;
            }
            .btn-danger {
                @apply bg-red-600 text-white shadow-sm hover:bg-red-700 focus-visible:ring-red-500 active:bg-red-800;
            }
            .btn-icon {
                @apply inline-flex h-8 w-8 items-center justify-center rounded-md transition-all duration-150 ease-out focus:outline-none focus-visible:ring-2 focus-visible:ring-offset-1 disabled:opacity-50 disabled:pointer-events-none;
            }
            .btn-icon-blue  { @apply bg-blue-50 text-blue-600 hover:bg-blue-100 hover:text-blue-700 focus-visible:ring-blue-300; }
            .btn-icon-amber { @apply bg-amber-50 text-amber-600 hover:bg-amber-100 hover:text-amber-700 focus-visible:ring-amber-300; }
            .btn-icon-red   { @apply bg-red-50 text-red-600 hover:bg-red-100 hover:text-red-700 focus-visible:ring-red-300; }

            .card {
                @apply bg-white rounded-lg border border-gray-200 shadow-sm;
            }
            input.search-input {
                @apply w-full sm:w-96 rounded-lg border-gray-300 py-2 pl-9 pr-3 text-sm shadow-sm transition-all duration-150 ease-out focus:border-blue-500 focus:ring-blue-500;
            }
            .data-table {
                @apply text-sm text-gray-700;
            }
        }

        /* ===== Tabulator: tema claro profesional ===== */
        .tabulator {
            border: 0 !important;
            font-family: inherit !important;
            font-size: 0.875rem !important;
        }
        .tabulator .tabulator-header {
            border-bottom: 1px solid #e2e8f0 !important;
            background: #f8fafc !important;
            color: #475569 !important;
            font-weight: 600 !important;
            font-size: 0.75rem !important;
            letter-spacing: 0.05em;
            text-transform: uppercase;
        }
        .tabulator .tabulator-header .tabulator-col {
            background: #f8fafc !important;
            border-right: none !important;
        }
        .tabulator .tabulator-row {
            border-bottom: 1px solid #f1f5f9 !important;
            transition: background-color 150ms ease-out !important;
        }
        .tabulator .tabulator-row.tabulator-row-even {
            background-color: #ffffff !important;
        }
        .tabulator .tabulator-row.tabulator-row-odd {
            background-color: #ffffff !important;
        }
        .tabulator .tabulator-row:hover {
            background-color: #eff6ff !important;
        }
        .tabulator .tabulator-cell {
            padding: 0.625rem 0.75rem !important;
            border-right: none !important;
            vertical-align: middle;
        }
        .tabulator .tabulator-footer {
            border-top: 1px solid #e2e8f0 !important;
            background: #ffffff !important;
            color: #475569 !important;
        }
        .tabulator .tabulator-page.active {
            background: #2563eb !important;
            border-color: #2563eb !important;
            color: #fff !important;
        }
        .tabulator .tabulator-placeholder {
            color: #94a3b8 !important;
        }

    </style>

    {{-- App shell: sidebar colapsable (desktop) --}}
    <style>
        .tabulator .tabulator-header { font-family: inherit; }

        /* Dropdown de usuario en el footer de la sidebar (abre hacia arriba) */
        .user-dropdown {
            position: absolute;
            left: 0.75rem;
            bottom: calc(100% + 0.5rem);
            z-index: 60;
            min-width: 12rem;
            padding: 0.25rem;
            border: 1px solid #e5e7eb;
            border-radius: 0.5rem;
            background: #ffffff;
            box-shadow: 0 10px 15px -3px rgb(0 0 0 / 0.1), 0 4px 6px -4px rgb(0 0 0 / 0.1);
        }
        .app-sidebar-collapsed .user-dropdown { left: 0.75rem; }

        /* El colapso del sidebar cambia de ancho sin animar propiedades de layout
           (padding/width) para evitar jank; el drawer móvil anima solo translate-x. */
        @media (min-width: 1024px) {
            .app-nav-wrap { padding-left: 16rem; }
            main { padding-left: 16rem; }
            .app-sidebar-collapsed .app-nav-wrap { padding-left: 5rem; }
            .app-sidebar-collapsed main { padding-left: 5rem; }
            .app-sidebar-collapsed #sidebar { width: 5rem; }
            .app-sidebar-collapsed #sidebar .flex.h-14 { padding-left: 0.75rem; padding-right: 0.75rem; }
            .app-sidebar-collapsed #sidebar nav { padding-left: 0.75rem; padding-right: 0.75rem; }
            .app-sidebar-collapsed .nav-label { display: none; }
            .app-sidebar-collapsed .nav-item { justify-content: center; gap: 0; padding-left: 0; padding-right: 0; }
            .app-sidebar-collapsed #sidebarCollapse { margin-left: auto; }
            .app-sidebar-collapsed .user-sidebar-wrap { padding-left: 0.5rem; padding-right: 0.5rem; }
            .app-sidebar-collapsed .user-sidebar-wrap button { justify-content: center; }
        }
    </style>

    <!-- Tabulator CSS -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/tabulator/6.2.0/css/tabulator.min.css" rel="stylesheet">
    <!-- Tom Select CSS -->
    <link href="https://cdn.jsdelivr.net/npm/tom-select@2.3.1/dist/css/tom-select.css" rel="stylesheet">
    @stack('styles')
</head>
<body class="font-sans antialiased">
    <div class="min-h-screen bg-gray-50">
        @include('layouts.navigation')

        <main>
            {{ $slot }}
        </main>
    </div>

    <!-- JavaScript CDN -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/tabulator/6.2.0/js/tabulator.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
    <script src="https://cdn.jsdelivr.net/npm/tom-select@2.3.1/dist/js/tom-select.complete.min.js"></script>
    <script>
    // Interceptor para errores 419 (CSRF expirado): recarga la página para renovar token
    document.addEventListener('DOMContentLoaded', function () {
        const originalFetch = window.fetch;
        window.fetch = function (...args) {
            return originalFetch.apply(this, args).then(response => {
                if (response.status === 419) {
                    location.reload();
                    return Promise.reject('Session expired');
                }
                return response;
            });
        };
    });

    // Renovar sesión cada 5 minutos mientras la página esté abierta
    setInterval(function () {
        fetch('/api/keep-alive', {
            method: 'GET',
            headers: { 'X-Requested-With': 'XMLHttpRequest' }
        }).catch(() => {});
    }, 300000);
    </script>

    {{-- Modal global de confirmación de acciones destructivas --}}
    @include('partials.confirm-modal')

    {{-- Modal pequeño para crear marca/categoría desde formularios (patrón "nueva placa") --}}
    @include('partials.catalog-quick-create')

    {{-- Guard global: refresh CSRF antes de envío + anti-doble envío --}}
    @include('partials.form-guard')

    @stack('scripts')
</body>
</html>