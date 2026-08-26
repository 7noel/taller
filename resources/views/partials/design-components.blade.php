{{-- Sistema de diseño: componentes reutilizables (modo Operate).
     Compartido por layouts/app.blade.php y layouts/public.blade.php --}}
<style type="text/tailwindcss">
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
</style>
