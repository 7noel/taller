{{-- =============================================================
     MODO OSCURO — capa global de overrides (taller mecánico)
     - Activado por la clase `dark` en <html> (persistida en
       localStorage('theme') + prefers-color-scheme).
     - Todo el CSS vive dentro de @media screen para que la
       impresión de documentos SIEMPRE salga en claro.
     - Paleta oscura: superficies slate (900 fondo / 800 tarjetas /
       700 bordes), texto slate-100..500, estados semánticos
       tintados (bg *-900/30 + texto *-300).
     - Botones de toggle: [data-theme-toggle] con .icon-sun/.icon-moon.
     ============================================================= --}}

{{-- Aplicar el tema ANTES del paint (evita parpadeo), mismo patrón
     que sidebar-collapsed en layouts/app.blade.php --}}
<script>
(function () {
    function initialTheme() {
        try {
            var t = localStorage.getItem('theme');
            if (t === 'dark' || t === 'light') return t;
        } catch (e) {}
        return (window.matchMedia && window.matchMedia('(prefers-color-scheme: dark)').matches) ? 'dark' : 'light';
    }
    var dark = initialTheme() === 'dark';
    document.documentElement.classList.toggle('dark', dark);
    document.documentElement.style.colorScheme = dark ? 'dark' : 'light';
})();
</script>

<style>
@media screen {
    /* ===== Base ===== */
    html.dark { background-color: #0f172a; }
    html.dark body { background-color: #0f172a; }

    /* ===== Superficies ===== */
    html.dark .bg-white    { background-color: #1e293b !important; }
    html.dark .bg-gray-50  { background-color: #0f172a !important; }
    html.dark .bg-gray-100 { background-color: #0f172a !important; }
    html.dark .bg-gray-200 { background-color: #334155 !important; }
    html.dark .bg-gray-300 { background-color: #475569 !important; }
    html.dark .bg-gray-400 { background-color: #64748b !important; }

    html.dark .hover\:bg-gray-50:hover  { background-color: #1e293b !important; }
    html.dark .hover\:bg-gray-100:hover { background-color: #334155 !important; }
    html.dark .hover\:bg-gray-200:hover { background-color: #475569 !important; }
    html.dark .hover\:bg-gray-300:hover { background-color: #64748b !important; }
    html.dark .hover\:bg-gray-400:hover { background-color: #475569 !important; }
    html.dark .focus\:bg-gray-50:focus  { background-color: #1e293b !important; }
    html.dark .focus\:bg-gray-100:focus { background-color: #334155 !important; }
    html.dark .disabled\:bg-gray-50:disabled { background-color: #1e293b !important; }

    /* ===== Texto ===== */
    html.dark .text-gray-900 { color: #f1f5f9 !important; }
    html.dark .text-gray-800 { color: #e2e8f0 !important; }
    html.dark .text-gray-700 { color: #cbd5e1 !important; }
    html.dark .text-gray-600 { color: #94a3b8 !important; }
    html.dark .text-gray-500 { color: #94a3b8 !important; }
    html.dark .text-gray-400 { color: #64748b !important; }
    html.dark .hover\:text-gray-700:hover { color: #e2e8f0 !important; }
    html.dark .hover\:text-gray-900:hover { color: #f1f5f9 !important; }
    html.dark .focus\:text-gray-700:focus { color: #e2e8f0 !important; }
    html.dark .focus\:text-gray-900:focus { color: #f1f5f9 !important; }
    html.dark .hover\:text-blue-700:hover { color: #93c5fd !important; }
    html.dark .hover\:text-blue-800:hover { color: #bfdbfe !important; }
    html.dark .hover\:text-red-700:hover { color: #fca5a5 !important; }
    html.dark .hover\:text-green-700:hover { color: #6ee7b7 !important; }

    /* ===== Bordes ===== */
    html.dark .border-gray-100 { border-color: #1e293b !important; }
    html.dark .border-gray-200 { border-color: #334155 !important; }
    html.dark .border-gray-300 { border-color: #475569 !important; }
    html.dark .border-gray-500 { border-color: #64748b !important; }
    html.dark .hover\:border-gray-300:hover { border-color: #475569 !important; }
    html.dark .divide-gray-200 > :not([hidden]) ~ :not([hidden]) { border-color: #334155 !important; }
    html.dark .divide-gray-100 > :not([hidden]) ~ :not([hidden]) { border-color: #1e293b !important; }

    /* ===== Estados semánticos (badges, flashes, iconos) ===== */
    html.dark .bg-blue-50    { background-color: rgba(59, 130, 246, 0.16) !important; }
    html.dark .bg-green-50   { background-color: rgba(16, 185, 129, 0.16) !important; }
    html.dark .bg-red-50     { background-color: rgba(239, 68, 68, 0.16) !important; }
    html.dark .bg-amber-50   { background-color: rgba(245, 158, 11, 0.16) !important; }
    html.dark .bg-yellow-50  { background-color: rgba(250, 204, 21, 0.16) !important; }
    html.dark .bg-indigo-50  { background-color: rgba(99, 102, 241, 0.16) !important; }
    html.dark .bg-orange-50  { background-color: rgba(249, 115, 22, 0.16) !important; }
    html.dark .bg-purple-50  { background-color: rgba(168, 85, 247, 0.16) !important; }
    html.dark .bg-teal-50    { background-color: rgba(20, 184, 166, 0.16) !important; }
    html.dark .bg-emerald-50 { background-color: rgba(16, 185, 129, 0.16) !important; }
    html.dark .bg-sky-50     { background-color: rgba(14, 165, 233, 0.16) !important; }
    html.dark .bg-pink-50    { background-color: rgba(236, 72, 153, 0.16) !important; }
    html.dark .bg-violet-50  { background-color: rgba(139, 92, 246, 0.16) !important; }

    html.dark .bg-blue-100   { background-color: #1e3a8a !important; }
    html.dark .bg-green-100  { background-color: #064e3b !important; }
    html.dark .bg-red-100    { background-color: #7f1d1d !important; }
    html.dark .bg-amber-100  { background-color: #78350f !important; }
    html.dark .bg-yellow-100 { background-color: #713f12 !important; }
    html.dark .bg-indigo-100 { background-color: #3730a3 !important; }
    html.dark .bg-orange-100 { background-color: #7c2d12 !important; }
    html.dark .bg-purple-100 { background-color: #581c87 !important; }
    html.dark .bg-teal-100   { background-color: #134e4a !important; }

    html.dark .border-blue-200    { border-color: #1e40af !important; }
    html.dark .border-green-200   { border-color: #065f46 !important; }
    html.dark .border-red-200     { border-color: #7f1d1d !important; }
    html.dark .border-amber-200   { border-color: #78350f !important; }
    html.dark .border-yellow-200  { border-color: #713f12 !important; }
    html.dark .border-indigo-200  { border-color: #3730a3 !important; }
    html.dark .border-orange-200  { border-color: #7c2d12 !important; }
    html.dark .border-purple-200  { border-color: #581c87 !important; }
    html.dark .border-teal-200    { border-color: #134e4a !important; }

    html.dark .text-blue-700   { color: #93c5fd !important; }
    html.dark .text-blue-800   { color: #bfdbfe !important; }
    html.dark .text-blue-600   { color: #60a5fa !important; }
    html.dark .text-green-700  { color: #6ee7b7 !important; }
    html.dark .text-green-800  { color: #a7f3d0 !important; }
    html.dark .text-green-600  { color: #34d399 !important; }
    html.dark .text-red-700    { color: #fca5a5 !important; }
    html.dark .text-red-800    { color: #fecaca !important; }
    html.dark .text-red-600    { color: #f87171 !important; }
    html.dark .text-amber-700  { color: #fcd34d !important; }
    html.dark .text-amber-800  { color: #fde68a !important; }
    html.dark .text-amber-600  { color: #fbbf24 !important; }
    html.dark .text-yellow-700 { color: #fde047 !important; }
    html.dark .text-yellow-800 { color: #fef08a !important; }
    html.dark .text-yellow-600 { color: #facc15 !important; }
    html.dark .text-indigo-700 { color: #a5b4fc !important; }
    html.dark .text-indigo-800 { color: #c7d2fe !important; }
    html.dark .text-orange-700 { color: #fdba74 !important; }
    html.dark .text-orange-800 { color: #fed7aa !important; }
    html.dark .text-purple-700 { color: #d8b4fe !important; }
    html.dark .text-purple-800 { color: #e9d5ff !important; }
    html.dark .text-teal-700   { color: #5eead4 !important; }
    html.dark .text-teal-800   { color: #99f6e4 !important; }

    /* Ring offset de los focus rings: en oscuro usa la superficie (no blanco) */
    html.dark [class*='ring-offset'] { --tw-ring-offset-color: #1e293b !important; }

    /* ===== Componentes del sistema de diseño ===== */
    html.dark .card { background-color: #1e293b; border-color: #334155; }
    html.dark .btn-secondary { background-color: #1e293b !important; border-color: #475569 !important; color: #e2e8f0 !important; }
    html.dark .btn-secondary:hover { background-color: #334155 !important; }
    html.dark .btn-icon-blue  { background-color: rgba(59, 130, 246, 0.16) !important; color: #60a5fa !important; }
    html.dark .btn-icon-blue:hover { background-color: rgba(59, 130, 246, 0.28) !important; color: #93c5fd !important; }
    html.dark .btn-icon-amber { background-color: rgba(245, 158, 11, 0.16) !important; color: #fbbf24 !important; }
    html.dark .btn-icon-amber:hover { background-color: rgba(245, 158, 11, 0.28) !important; color: #fcd34d !important; }
    html.dark .btn-icon-red   { background-color: rgba(239, 68, 68, 0.16) !important; color: #f87171 !important; }
    html.dark .btn-icon-red:hover { background-color: rgba(239, 68, 68, 0.28) !important; color: #fca5a5 !important; }

    /* Inputs base */
    html.dark input[type='text'], html.dark input[type='number'], html.dark input[type='date'],
    html.dark input[type='email'], html.dark input[type='tel'], html.dark input[type='password'],
    html.dark select, html.dark textarea {
        background-color: #111827 !important;
        border-color: #475569 !important;
        color: #f1f5f9 !important;
    }
    html.dark input[type='text']::placeholder, html.dark input[type='number']::placeholder,
    html.dark input[type='date']::placeholder, html.dark input[type='email']::placeholder,
    html.dark input[type='tel']::placeholder, html.dark input[type='password']::placeholder,
    html.dark textarea::placeholder { color: #64748b !important; }
    html.dark input[type='text']:disabled, html.dark input[type='number']:disabled,
    html.dark input[type='date']:disabled, html.dark input[type='email']:disabled,
    html.dark input[type='tel']:disabled, html.dark input[type='password']:disabled,
    html.dark select:disabled, html.dark textarea:disabled {
        background-color: #1e293b !important;
        color: #64748b !important;
    }
    html.dark .search-input { background-color: #111827 !important; border-color: #475569 !important; color: #f1f5f9 !important; }
    html.dark .search-input::placeholder { color: #64748b !important; }

    /* Checkbox / radio (color-scheme dark + acento azul) */
    html.dark input[type='checkbox'], html.dark input[type='radio'] {
        background-color: #111827 !important;
        border-color: #475569 !important;
        accent-color: #3b82f6 !important;
    }
    html.dark input[type='checkbox']:checked, html.dark input[type='radio']:checked {
        background-color: #2563eb !important;
    }

    /* Dropdown de usuario (footer de la sidebar) */
    html.dark .user-dropdown {
        background: #1e293b !important;
        border-color: #334155 !important;
        box-shadow: 0 10px 15px -3px rgb(0 0 0 / 0.5), 0 4px 6px -4px rgb(0 0 0 / 0.5);
    }

    /* ===== Tom Select ===== */
    html.dark .ts-wrapper .ts-control,
    html.dark .ts-wrapper.single .ts-control,
    html.dark .ts-wrapper.multi .ts-control {
        background-color: #111827 !important;
        border-color: #475569 !important;
        color: #f1f5f9 !important;
    }
    html.dark .ts-wrapper .ts-control > input { color: #f1f5f9 !important; }
    html.dark .ts-wrapper .ts-control:hover { border-color: #64748b !important; }
    html.dark .ts-wrapper.focus .ts-control { border-color: #3b82f6 !important; box-shadow: 0 0 0 3px rgb(59 130 246 / 0.35) !important; }
    html.dark .ts-wrapper .ts-dropdown,
    html.dark body > .ts-dropdown {
        background-color: #1e293b !important;
        border-color: #475569 !important;
        color: #f1f5f9 !important;
        box-shadow: 0 10px 15px -3px rgb(0 0 0 / 0.5), 0 4px 6px -4px rgb(0 0 0 / 0.5);
    }
    html.dark .ts-wrapper .ts-dropdown .ts-dropdown-content .option,
    html.dark body > .ts-dropdown .ts-dropdown-content .option { color: #cbd5e1 !important; }
    html.dark .ts-wrapper .ts-dropdown .ts-dropdown-content .option:hover,
    html.dark body > .ts-dropdown .ts-dropdown-content .option:hover { background-color: #334155 !important; color: #f1f5f9 !important; }
    html.dark .ts-wrapper .ts-dropdown .ts-dropdown-content .option.active,
    html.dark .ts-wrapper .ts-dropdown .ts-dropdown-content .option.selected,
    html.dark body > .ts-dropdown .ts-dropdown-content .option.active,
    html.dark body > .ts-dropdown .ts-dropdown-content .option.selected {
        background-color: rgba(37, 99, 235, 0.28) !important;
        border-left-color: #3b82f6 !important;
        color: #f1f5f9 !important;
    }
    html.dark .ts-wrapper .ts-dropdown .ts-dropdown-content .option.active .suboption,
    html.dark .ts-wrapper .ts-dropdown .ts-dropdown-content .option.selected .suboption,
    html.dark body > .ts-dropdown .ts-dropdown-content .option.active .suboption,
    html.dark body > .ts-dropdown .ts-dropdown-content .option.selected .suboption { color: #cbd5e1 !important; }
    html.dark .ts-wrapper .ts-dropdown .ts-dropdown-content .option .suboption,
    html.dark body > .ts-dropdown .ts-dropdown-content .option .suboption { color: #94a3b8 !important; }
    html.dark .ts-wrapper .ts-dropdown .ts-dropdown-content .no-results,
    html.dark body > .ts-dropdown .ts-dropdown-content .no-results { color: #94a3b8 !important; }
    html.dark .ts-wrapper .ts-item { background-color: rgba(37, 99, 235, 0.28) !important; color: #93c5fd !important; }
    html.dark .ts-wrapper.single .ts-control .item { color: #f1f5f9 !important; }

    /* ===== Tabulator ===== */
    html.dark .tabulator { background: transparent !important; }
    html.dark .tabulator .tabulator-header { border-bottom-color: #334155 !important; background: #111827 !important; color: #94a3b8 !important; }
    html.dark .tabulator .tabulator-header .tabulator-col { background: #111827 !important; }
    html.dark .tabulator .tabulator-header .tabulator-col .tabulator-col-content .tabulator-col-title { color: #94a3b8 !important; }
    html.dark .tabulator .tabulator-row { border-bottom-color: #334155 !important; }
    html.dark .tabulator .tabulator-row.tabulator-row-even,
    html.dark .tabulator .tabulator-row.tabulator-row-odd { background-color: #1e293b !important; }
    html.dark .tabulator .tabulator-row:hover { background-color: #334155 !important; }
    html.dark .tabulator .tabulator-cell { color: #cbd5e1; }
    html.dark .tabulator .tabulator-footer { border-top-color: #334155 !important; background: #111827 !important; color: #94a3b8 !important; }
    html.dark .tabulator .tabulator-footer .tabulator-page { background: transparent !important; color: #94a3b8 !important; border-color: #334155 !important; }
    html.dark .tabulator .tabulator-footer .tabulator-page.active { background: #2563eb !important; border-color: #2563eb !important; color: #fff !important; }
    html.dark .tabulator .tabulator-placeholder { color: #64748b !important; }

    /* ===== Iconos del toggle sol/luna (estado inicial sin JS) ===== */
    html.dark .icon-sun { display: none !important; }
    html.dark .icon-moon { display: inline-block !important; }

    /* Con la sidebar colapsada solo caben el logo y el botón colapsar */
    .app-sidebar-collapsed #sidebar [data-theme-toggle] { display: none; }
}
</style>

{{-- Wiring del toggle: sincroniza iconos/title de [data-theme-toggle] y persiste --}}
<script>
document.addEventListener('DOMContentLoaded', function () {
    var root = document.documentElement;
    var toggles = document.querySelectorAll('[data-theme-toggle]');

    function syncToggles() {
        var dark = root.classList.contains('dark');
        toggles.forEach(function (btn) {
            var sun = btn.querySelector('.icon-sun');
            var moon = btn.querySelector('.icon-moon');
            if (sun) sun.classList.toggle('hidden', !dark);
            if (moon) moon.classList.toggle('hidden', dark);
            var label = dark ? 'Cambiar a modo claro' : 'Cambiar a modo oscuro';
            btn.setAttribute('title', label);
            btn.setAttribute('aria-label', label);
        });
    }

    toggles.forEach(function (btn) {
        btn.addEventListener('click', function () {
            var dark = root.classList.toggle('dark');
            root.style.colorScheme = dark ? 'dark' : 'light';
            try { localStorage.setItem('theme', dark ? 'dark' : 'light'); } catch (e) {}
            syncToggles();
        });
    });

    syncToggles();
});
</script>


