@php
    $navItems = [
        ['route' => 'dashboard', 'active' => 'dashboard', 'label' => 'Dashboard', 'icon' => 'M3 12l9-9 9 9M5 10v10h5v-6h4v6h5V10'],
        ['route' => 'parties.index', 'active' => 'parties.*', 'label' => 'Contactos', 'icon' => 'M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z'],
        ['route' => 'vehicles.index', 'active' => 'vehicles.*', 'label' => 'Vehículos', 'icon' => 'M9 17a2 2 0 11-4 0 2 2 0 014 0zM19 17a2 2 0 11-4 0 2 2 0 014 0zM5 15H3v-4h4v4H5zM17 15h-2v-4h4v4h-2zM3 11l3-4h9l3 4'],
        ['route' => 'check-ins.index', 'active' => 'check-ins.*', 'label' => 'Inventario', 'icon' => 'M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2'],
        ['route' => 'repair-services.index', 'active' => 'repair-services.*', 'label' => 'Servicios', 'icon' => 'M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065zM15 12a3 3 0 11-6 0 3 3 0 016 0z', 'can' => 'ver servicios'],
        ['route' => 'parts.index', 'active' => 'parts.*', 'label' => 'Repuestos', 'icon' => 'M9 3v2m6-2v2M9 19v2m6-2v2M5 9H3m2 6H3m18-6h-2m2 6h-2M7 19h10a2 2 0 002-2V7a2 2 0 00-2-2H7a2 2 0 00-2 2v10a2 2 0 002 2zM9 9h6v6H9z', 'can' => 'ver repuestos'],
        ['route' => 'warehouses.index', 'active' => 'warehouses.*', 'label' => 'Almacenes', 'icon' => 'M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4', 'can' => 'ver almacenes'],
        ['route' => 'stock.index', 'active' => 'stock.*', 'label' => 'Stock', 'icon' => 'M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4', 'can' => 'ver stock'],
        ['route' => 'part-brands.index', 'active' => 'part-brands.*', 'label' => 'Marcas de Repuesto', 'icon' => 'M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z', 'can' => 'ver marcas de repuesto'],
        ['route' => 'part-categories.index', 'active' => 'part-categories.*', 'label' => 'Categorías de Repuesto', 'icon' => 'M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2', 'can' => 'ver categorías de repuesto'],
        ['route' => 'service-categories.index', 'active' => 'service-categories.*', 'label' => 'Categorías de Servicio', 'icon' => 'M4 6h16M4 10h16M4 14h16M4 18h16', 'can' => 'ver categorías de servicio'],
        ['route' => 'users.index', 'active' => 'users.*', 'label' => 'Usuarios', 'icon' => 'M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z', 'can' => 'ver usuarios'],
        ['route' => 'establishments.index', 'active' => 'establishments.*', 'label' => 'Establecimientos', 'icon' => 'M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4', 'can' => 'ver establecimientos'],
        ['route' => 'company-settings.edit', 'active' => 'company-settings.*', 'label' => 'Configuración', 'icon' => 'M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065zM15 12a3 3 0 11-6 0 3 3 0 016 0z', 'can' => 'ver configuración'],
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

        {{-- Botón colapsar (solo desktop) --}}
        <button id="sidebarCollapse" type="button" aria-label="Colapsar menú" title="Colapsar menú"
                class="ml-auto hidden h-8 w-8 shrink-0 items-center justify-center rounded-md text-gray-400 hover:bg-gray-100 hover:text-gray-700 focus-visible:ring-2 focus-visible:ring-blue-500 lg:inline-flex">
            {{-- "<" visible expandido; ">" visible colapsado --}}
            <svg class="icon-collapse h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7"/></svg>
            <svg class="icon-expand hidden h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/></svg>
        </button>

        {{-- Botón cerrar drawer (solo móvil) --}}
        <button id="sidebarClose" type="button" aria-label="Cerrar menú" class="ml-auto inline-flex h-8 w-8 shrink-0 items-center justify-center rounded-md text-gray-500 hover:bg-gray-100 hover:text-gray-700 lg:hidden">
            <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
        </button>
    </div>

    {{-- Navegación --}}
    <nav class="flex-1 space-y-1 overflow-y-auto px-3 py-4">
        @foreach ($navItems as $item)
            @if (isset($item['can']) && !auth()->user()->can($item['can']))
                @continue
            @endif
            @php
                $active = request()->routeIs($item['active']);
                $linkClasses = $active
                    ? 'bg-blue-50 text-blue-700'
                    : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900';
            @endphp
            <a href="{{ route($item['route']) }}" title="{{ $item['label'] }}"
               class="nav-item relative flex items-center gap-3 rounded-md px-3 py-2 text-sm font-medium transition-colors duration-150 ease-out {{ $linkClasses }}">
                @if ($active)
                    <span class="absolute left-0 top-1/2 h-5 w-0.5 -translate-y-1/2 rounded-full bg-blue-600"></span>
                @endif
                <svg class="h-5 w-5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="{{ $item['icon'] }}"/>
                </svg>
                <span class="nav-label">{{ $item['label'] }}</span>
            </a>
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