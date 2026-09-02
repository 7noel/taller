@php
    $navItems = [
        ['route' => 'dashboard', 'active' => 'dashboard', 'label' => 'Dashboard', 'icon' => 'M3 12l9-9 9 9M5 10v10h5v-6h4v6h5V10'],
        ['route' => 'kanban.index', 'active' => 'kanban.*', 'label' => 'Tablero Kanban', 'icon' => 'M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h2a2 2 0 002-2V7a2 2 0 00-2-2zm10 0h-2a2 2 0 00-2 2v8a2 2 0 002 2h2a2 2 0 002-2V7a2 2 0 00-2-2z', 'can' => 'ver tablero'],
    ];

    // Grupos con submenús colapsables (el grupo de la ruta activa se abre automáticamente)
    $navGroups = [
        'clients' => [
            'label' => 'Clientes',
            'icon' => 'M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z',
            'items' => [
                ['route' => 'parties.index', 'active' => 'parties.*', 'label' => 'Contactos'],
                ['route' => 'vehicles.index', 'active' => 'vehicles.*', 'label' => 'Vehículos'],
            ],
        ],
        'workshop' => [
            'label' => 'Taller',
            'icon' => 'M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4',
            'items' => [
                ['route' => 'check-ins.index', 'active' => 'check-ins.*', 'label' => 'Inventario'],
                ['route' => 'estimates.index', 'active' => 'estimates.*', 'label' => 'Presupuestos', 'can' => 'ver presupuestos'],
                ['route' => 'work-orders.index', 'active' => 'work-orders.*', 'label' => 'Órdenes de Trabajo', 'can' => 'ver órdenes de trabajo'],
            ],
        ],
        'outsourced' => [
            'label' => 'Tercerizados',
            'icon' => 'M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z',
            'items' => [
                ['route' => 'service-vouchers.index', 'active' => 'service-vouchers.*', 'label' => 'Vales de Servicio', 'can' => 'ver vales de servicio'],
                ['route' => 'provider-settlements.index', 'active' => 'provider-settlements.*', 'label' => 'Liquidaciones', 'can' => 'ver liquidaciones de servicios'],
            ],
        ],
        'warehouse' => [
            'label' => 'Almacén',
            'icon' => 'M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4',
            'items' => [
                ['route' => 'parts.index', 'active' => 'parts.*', 'label' => 'Repuestos', 'can' => 'ver repuestos'],
                ['route' => 'warehouses.index', 'active' => 'warehouses.*', 'label' => 'Almacenes', 'can' => 'ver almacenes'],
                ['route' => 'stock.index', 'active' => 'stock.index', 'label' => 'Stock', 'can' => 'ver stock'],
                ['route' => 'stock.movements', 'active' => 'stock.movements', 'label' => 'Kardex', 'can' => 'ver stock'],
                ['route' => 'inventory-guides.index', 'active' => 'inventory-guides.*', 'label' => 'Guías de Inventario', 'can' => 'ver guías de inventario'],
                ['route' => 'purchase-orders.index', 'active' => 'purchase-orders.*', 'label' => 'Compras', 'can' => 'ver órdenes de compra'],
                ['route' => 'part-orders.index', 'active' => 'part-orders.*', 'label' => 'Pedidos de Repuestos', 'can' => 'ver pedidos de repuestos'],
            ],
        ],
        'catalogs' => [
            'label' => 'Catálogos',
            'icon' => 'M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z',
            'items' => [
                ['route' => 'brands.index', 'active' => 'brands.*', 'label' => 'Marcas de Vehículos', 'can' => 'ver marcas'],
                ['route' => 'repair-services.index', 'active' => 'repair-services.*', 'label' => 'Servicios', 'can' => 'ver servicios'],
                ['route' => 'part-brands.index', 'active' => 'part-brands.*', 'label' => 'Marcas de Repuesto', 'can' => 'ver marcas de repuesto'],
                ['route' => 'part-categories.index', 'active' => 'part-categories.*', 'label' => 'Categorías de Repuesto', 'can' => 'ver categorías de repuesto'],
                ['route' => 'service-categories.index', 'active' => 'service-categories.*', 'label' => 'Categorías de Servicio', 'can' => 'ver categorías de servicio'],
                ['route' => 'form-templates.index', 'active' => 'form-templates.*', 'label' => 'Plantillas', 'can' => 'ver plantillas de formulario'],
                ['route' => 'checklist-items.index', 'active' => 'checklist-items.*', 'label' => 'Checklist', 'can' => 'ver checklist'],
            ],
        ],
        'appointments' => [
            'label' => 'Citas',
            'icon' => 'M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z',
            'items' => [
                ['route' => 'appointments.index', 'active' => 'appointments.*', 'label' => 'Citas', 'can' => 'ver citas'],
                ['route' => 'follow-ups.index', 'active' => 'follow-ups.*', 'label' => 'Seguimiento', 'can' => 'ver seguimientos'],
                ['route' => 'reminders.index', 'active' => 'reminders.*', 'label' => 'Recordatorios', 'can' => 'ver seguimientos'],
            ],
        ],
        'billing' => [
            'label' => 'Facturación',
            'icon' => 'M9 14l6-6m-5.5.5h.01M14.5 14.5h.01M4 21v-7m0-4V3h16v14H7l-3 4zm3-7h10M8 7h.01M16 10h.01M14 14h.01',
            'items' => [
                ['route' => 'invoices.index', 'active' => 'invoices.*', 'label' => 'Comprobantes', 'can' => 'ver facturas'],
                ['route' => 'dispatches.index', 'active' => 'dispatches.*', 'label' => 'Guías de Remisión', 'can' => 'ver guías de remisión'],
                ['route' => 'cash.index', 'active' => 'cash.*', 'label' => 'Caja', 'can' => 'ver caja'],
                ['route' => 'cash.payment-methods', 'active' => 'cash.payment-methods*', 'label' => 'Métodos de Pago', 'can' => 'ver métodos de pago'],
                ['route' => 'cash.banks', 'active' => 'cash.banks*', 'label' => 'Bancos', 'can' => 'ver bancos'],
            ],
        ],
        'reports' => [
            'label' => 'Reportes',
            'icon' => 'M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z',
            'items' => [
                ['route' => 'reports.index', 'active' => 'reports.index', 'label' => 'Centro de Reportes', 'can' => 'ver reportes'],
                ['route' => 'reports.vehicles', 'active' => 'reports.vehicles', 'label' => 'Frecuencia de Vehículos', 'can' => 'ver reportes'],
                ['route' => 'reports.advisors', 'active' => 'reports.advisors', 'label' => 'Rentabilidad de Asesores', 'can' => 'ver reportes'],
                ['route' => 'reports.profitability', 'active' => 'reports.profitability', 'label' => 'Costos y Utilidad', 'can' => 'ver reportes'],
                ['route' => 'reports.followups', 'active' => 'reports.followups', 'label' => 'Seguimientos', 'can' => 'ver reportes'],
                ['route' => 'reports.revenue', 'active' => 'reports.revenue', 'label' => 'Ingresos y Cobranza', 'can' => 'ver reportes'],
                ['route' => 'reports.parts', 'active' => 'reports.parts', 'label' => 'Repuestos Utilizados', 'can' => 'ver reportes'],
            ],
        ],

        'admin' => [
            'label' => 'Administración',
            'icon' => 'M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065zM15 12a3 3 0 11-6 0 3 3 0 016 0z',
            'items' => [
                ['route' => 'users.index', 'active' => 'users.*', 'label' => 'Usuarios', 'can' => 'ver usuarios'],
                ['route' => 'establishments.index', 'active' => 'establishments.*', 'label' => 'Establecimientos', 'can' => 'ver establecimientos'],
                ['route' => 'company-settings.edit', 'active' => 'company-settings.*', 'label' => 'Configuración', 'can' => 'ver configuración'],
                ['route' => 'exchange-rates.index', 'active' => 'exchange-rates.*', 'label' => 'Tipos de Cambio', 'can' => 'ver configuración'],
            ],
        ],
    ];
@endphp

{{-- Overlay móvil para el drawer --}}
<div id="sidebarOverlay" class="fixed inset-0 z-40 hidden bg-gray-900/50 lg:hidden"></div>

{{-- Sidebar: fija en desktop (colapsable), drawer en móvil --}}
<aside id="sidebar"
       class="fixed inset-y-0 left-0 z-50 flex w-64 flex-col border-r border-gray-200 bg-white -translate-x-full transition-[width,transform] duration-200 ease-out lg:translate-x-0">
    {{-- Cabecera: logo + botón colapsar (desktop) + cerrar (móvil) --}}
    <div class="flex h-14 shrink-0 items-center gap-2 px-4">
        <a href="{{ route('dashboard') }}" class="flex min-w-0 items-center gap-2 font-bold text-gray-900" title="Taller Mecánico">
            <svg class="h-6 w-6 shrink-0 text-blue-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/>
                <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
            </svg>
            <span class="nav-label truncate">Taller Mecánico</span>
        </a>

        <div class="ml-auto flex items-center gap-1">
            {{-- Toggle modo oscuro/claro --}}
            <button type="button" data-theme-toggle aria-label="Cambiar a modo oscuro" title="Cambiar a modo oscuro"
                    class="inline-flex h-8 w-8 shrink-0 items-center justify-center rounded-md text-gray-400 transition-colors duration-150 ease-out hover:bg-gray-100 hover:text-gray-700 focus-visible:ring-2 focus-visible:ring-blue-500">
                <svg class="icon-sun h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M12 3v2m0 14v2m9-9h-2M5 12H3m15.36-6.36l-1.42 1.42M7.06 16.94l-1.42 1.42m12.72 0l-1.42-1.42M7.06 7.06L5.64 5.64M12 8a4 4 0 100 8 4 4 0 000-8z"/></svg>
                <svg class="icon-moon hidden h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M21 12.79A9 9 0 1111.21 3 7 7 0 0021 12.79z"/></svg>
            </button>

            {{-- Botón colapsar (solo desktop) --}}
            <button id="sidebarCollapse" type="button" aria-label="Colapsar menú" title="Colapsar menú"
                    class="hidden h-8 w-8 shrink-0 items-center justify-center rounded-md text-gray-400 hover:bg-gray-100 hover:text-gray-700 focus-visible:ring-2 focus-visible:ring-blue-500 lg:inline-flex">
                {{-- "<" visible expandido; ">" visible colapsado --}}
                <svg class="icon-collapse h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7"/></svg>
                <svg class="icon-expand hidden h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/></svg>
            </button>

            {{-- Botón cerrar drawer (solo móvil) --}}
            <button id="sidebarClose" type="button" aria-label="Cerrar menú" class="inline-flex h-8 w-8 shrink-0 items-center justify-center rounded-md text-gray-500 hover:bg-gray-100 hover:text-gray-700 lg:hidden">
                <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>
    </div>

    {{-- Navegación: ítems de primer nivel + grupos con submenú --}}
    <nav class="flex-1 space-y-1 overflow-y-auto px-3 py-4">
        @foreach ($navItems as $item)
            @if (isset($item['can']) && !auth()->user()->can($item['can']))
                @continue
            @endif
            @include('partials.nav-link', ['item' => $item, 'icon' => $item['icon']])
        @endforeach

        @foreach ($navGroups as $key => $group)
            @php
                $visibleItems = array_values(array_filter($group['items'], fn ($i) => empty($i['can']) || auth()->user()->can($i['can'])));
                if (count($visibleItems) === 0) { continue; }
                $groupActive = collect($visibleItems)->contains(fn ($i) => request()->routeIs($i['active']));
                $groupId = 'nav-group-' . $key;
            @endphp
            <div class="pt-3">
                <button type="button" id="btn-{{ $groupId }}" data-nav-group="{{ $groupId }}"
                        aria-expanded="{{ $groupActive ? 'true' : 'false' }}" aria-controls="{{ $groupId }}"
                        title="{{ $group['label'] }}"
                        class="nav-item group-toggle flex w-full items-center gap-3 rounded-md px-3 py-2 text-sm font-medium transition-colors duration-150 ease-out hover:bg-gray-50 hover:text-gray-900 {{ $groupActive ? 'text-gray-800' : 'text-gray-600' }}">
                    <svg class="h-5 w-5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="{{ $group['icon'] }}"/>
                    </svg>
                    <span class="nav-label min-w-0 flex-1 truncate text-left">{{ $group['label'] }}</span>
                    <svg class="group-chevron nav-label h-4 w-4 shrink-0 text-gray-400 transition-transform duration-150 ease-out" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/>
                    </svg>
                </button>
                <div id="{{ $groupId }}" class="nav-submenu {{ $groupActive ? '' : 'hidden' }}">
                    <div class="mt-1 space-y-1 pb-1">
                        @foreach ($visibleItems as $item)
                            @include('partials.nav-link', ['item' => $item, 'submenu' => true, 'icon' => null])
                        @endforeach
                    </div>
                </div>
            </div>
        @endforeach
    </nav>

    {{-- Footer: usuario + dropdown (Perfil / Cerrar sesión) --}}
    @auth
    <div class="user-sidebar-wrap relative shrink-0 border-t border-gray-200 px-3 py-3">
        <button id="userMenuSidebar" type="button" aria-haspopup="true" aria-expanded="false"
                class="nav-item flex w-full items-center gap-3 rounded-md px-2 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50 hover:text-gray-900">
            <span class="flex h-8 w-8 shrink-0 items-center justify-center rounded-full bg-blue-50 text-blue-700">
                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
            </span>
            <span class="nav-label min-w-0 flex-1 truncate text-left">{{ Auth::user()->name }}</span>
            {{-- El dropdown abre hacia ARRIBA (footer de la sidebar): chevron apunta hacia arriba por defecto y rota al abrir --}}
            <svg class="user-chevron nav-label h-4 w-4 shrink-0 text-gray-400 transition-transform duration-150 ease-out" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M5 15l7-7 7 7"/></svg>
        </button>

        <div id="userDropdownSidebar" class="user-dropdown hidden">
            <a href="{{ route('profile.edit') }}" class="flex items-center gap-2 px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                <svg class="h-4 w-4 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                Mi Perfil
            </a>
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="flex w-full items-center gap-2 px-4 py-2 text-sm text-red-600 hover:bg-gray-100">
                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>
                    Cerrar sesión
                </button>
            </form>
        </div>
    </div>
    @endauth
</aside>

{{-- Área principal (topbar móvil + header de página); offset en desktop por .app-nav-wrap --}}
<div class="app-nav-wrap lg:pl-64">
    {{-- Topbar: solo móvil/tablet --}}
    <header class="sticky top-0 z-30 flex h-14 items-center gap-4 border-b border-gray-200 bg-white px-4 sm:px-6 lg:hidden">
        <button id="sidebarToggle" type="button" aria-controls="sidebar" aria-expanded="false"
                class="inline-flex h-9 w-9 shrink-0 items-center justify-center rounded-md text-gray-500 hover:bg-gray-100 hover:text-gray-700 focus-visible:ring-2 focus-visible:ring-blue-500">
            <span class="sr-only">Abrir menú</span>
            <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16M4 18h16"/>
            </svg>
        </button>
        <span class="truncate text-sm font-semibold text-gray-800">{{ $title ?? config('app.name', 'Taller Mecánico') }}</span>
        {{-- Toggle modo oscuro/claro (móvil/tablet) --}}
        <button type="button" data-theme-toggle aria-label="Cambiar a modo oscuro" title="Cambiar a modo oscuro"
                class="ml-auto inline-flex h-9 w-9 shrink-0 items-center justify-center rounded-md text-gray-500 transition-colors duration-150 ease-out hover:bg-gray-100 hover:text-gray-700 focus-visible:ring-2 focus-visible:ring-blue-500">
            <svg class="icon-sun h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M12 3v2m0 14v2m9-9h-2M5 12H3m15.36-6.36l-1.42 1.42M7.06 16.94l-1.42 1.42m12.72 0l-1.42-1.42M7.06 7.06L5.64 5.64M12 8a4 4 0 100 8 4 4 0 000-8z"/></svg>
            <svg class="icon-moon hidden h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M21 12.79A9 9 0 1111.21 3 7 7 0 0021 12.79z"/></svg>
        </button>
    </header>

    @isset($header)
        <div class="border-b border-gray-200 bg-white px-4 py-4 sm:px-6 lg:px-8">
            {{ $header }}
        </div>
    @endisset
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    // ===== Drawer móvil de la sidebar =====
    const sidebar = document.getElementById('sidebar');
    const overlay = document.getElementById('sidebarOverlay');
    const toggle = document.getElementById('sidebarToggle');
    const closeBtn = document.getElementById('sidebarClose');

    function openSidebar() {
        if (!sidebar || window.innerWidth >= 1024) return;
        sidebar.classList.remove('-translate-x-full');
        if (overlay) overlay.classList.remove('hidden');
        if (toggle) toggle.setAttribute('aria-expanded', 'true');
    }
    function closeSidebar() {
        if (!sidebar || window.innerWidth >= 1024) return;
        sidebar.classList.add('-translate-x-full');
        if (overlay) overlay.classList.add('hidden');
        if (toggle) toggle.setAttribute('aria-expanded', 'false');
    }

    if (toggle) toggle.addEventListener('click', function () {
        sidebar.classList.contains('-translate-x-full') ? openSidebar() : closeSidebar();
    });
    if (closeBtn) closeBtn.addEventListener('click', closeSidebar);
    if (overlay) overlay.addEventListener('click', closeSidebar);
    document.addEventListener('keydown', function (e) { if (e.key === 'Escape') closeSidebar(); });

    // ===== Colapsar sidebar (solo desktop) =====
    const collapseBtn = document.getElementById('sidebarCollapse');
    const collapseIcon = document.querySelector('#sidebarCollapse .icon-collapse');
    const expandIcon = document.querySelector('#sidebarCollapse .icon-expand');

    function updateCollapseIcons(collapsed) {
        if (!collapseIcon || !expandIcon) return;
        collapseIcon.classList.toggle('hidden', collapsed);
        expandIcon.classList.toggle('hidden', !collapsed);
        if (collapseBtn) {
            collapseBtn.setAttribute('aria-label', collapsed ? 'Expandir menú' : 'Colapsar menú');
            collapseBtn.setAttribute('title', collapsed ? 'Expandir menú' : 'Colapsar menú');
        }
    }

    // Sincronizar iconos según el estado persistido (cargado antes del paint en <head>)
    updateCollapseIcons(document.documentElement.classList.contains('app-sidebar-collapsed'));

    if (collapseBtn) {
        collapseBtn.addEventListener('click', function () {
            const collapsed = document.documentElement.classList.toggle('app-sidebar-collapsed');
            try { localStorage.setItem('sidebar-collapsed', collapsed ? '1' : '0'); } catch (e) {}
            updateCollapseIcons(collapsed);
        });
    }

    // ===== Submenús agrupados (acordeón) =====
    document.querySelectorAll('.group-toggle').forEach(function (toggle) {
        const target = document.getElementById(toggle.getAttribute('data-nav-group'));
        if (!target) return;
        const chevron = toggle.querySelector('.group-chevron');
        function setGroupOpen(open) {
            target.classList.toggle('hidden', !open);
            toggle.setAttribute('aria-expanded', open ? 'true' : 'false');
            if (chevron) chevron.classList.toggle('rotate-180', open);
        }
        setGroupOpen(!target.classList.contains('hidden'));
        toggle.addEventListener('click', function () {
            if (document.documentElement.classList.contains('app-sidebar-collapsed')) {
                // Con el sidebar colapsado, abrir un grupo expande la barra lateral
                document.documentElement.classList.remove('app-sidebar-collapsed');
                try { localStorage.setItem('sidebar-collapsed', '0'); } catch (e) {}
                updateCollapseIcons(false);
                setGroupOpen(true);
                return;
            }
            setGroupOpen(target.classList.contains('hidden'));
        });
    });
    // ===== Dropdown de usuario en la sidebar =====
    const userButton = document.getElementById('userMenuSidebar');
    const userDropdown = document.getElementById('userDropdownSidebar');
    if (userButton && userDropdown) {
        const userChevron = userButton.querySelector('.user-chevron');
        function setUserDropdownState(open) {
            userDropdown.classList.toggle('hidden', !open);
            userButton.setAttribute('aria-expanded', open ? 'true' : 'false');
            if (userChevron) userChevron.classList.toggle('rotate-180', open);
        }
        function openUser() { setUserDropdownState(true); }
        function closeUser() { setUserDropdownState(false); }
        userButton.addEventListener('click', function (e) {
            e.stopPropagation();
            userDropdown.classList.contains('hidden') ? openUser() : closeUser();
        });
        document.addEventListener('click', function (e) {
            if (!userButton.contains(e.target) && !userDropdown.contains(e.target)) closeUser();
        });
        document.addEventListener('keydown', function (e) { if (e.key === 'Escape') closeUser(); });
    }
});
</script>
@endpush
