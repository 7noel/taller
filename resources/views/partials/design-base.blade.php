{{-- ===== Sistema de diseño base (compartido por app y guest) ===== --}}
<style type="text/tailwindcss">
    @layer base {
        input[type='text'],
        input[type='number'],
        input[type='date'],
        input[type='email'],
        input[type='tel'],
        input[type='password'],
        select,
        textarea {
            @apply block w-full rounded-md border-gray-300 bg-white px-3 py-2 text-sm text-gray-900 shadow-sm
                   focus:border-blue-500 focus:ring-blue-500 disabled:bg-gray-50 disabled:text-gray-500;
        }
        select {
            background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' fill='none' viewBox='0 0 20 20'%3E%3Cpath stroke='%239ca3af' stroke-linecap='round' stroke-linejoin='round' stroke-width='1.5' d='m6 8 4 4 4-4'/%3E%3C/svg%3E");
            background-position: right 0.5rem center;
            background-repeat: no-repeat;
            background-size: 1.5em 1.5em;
            padding-right: 2.5rem;
            -webkit-print-color-adjust: exact;
            print-color-adjust: exact;
            appearance: none;
        }
        select[multiple] {
            background-image: none;
            padding-right: 0.75rem;
        }
        input[type='checkbox'],
        input[type='radio'] {
            @apply h-4 w-4 rounded border-gray-300 bg-white text-blue-600 focus:ring-blue-500;
        }
        input[type='checkbox']:checked,
        input[type='radio']:checked {
            @apply border-transparent bg-current;
        }
    }
</style>
<style type="text/tailwindcss">
    /* Superficies del navegador con la paleta (craft-floor) */
    ::selection { background: #bfdbfe; color: #1e3a8a; }
    :focus-visible { outline: none; }
    :focus:not(:focus-visible) { outline: none; }
    a:focus-visible,
    button:focus-visible,
    input:focus-visible,
    select:focus-visible,
    textarea:focus-visible,
    [tabindex]:focus-visible {
        outline: 2px solid #2563eb;
        outline-offset: 2px;
    }
    @media (prefers-reduced-motion: reduce) {
        *, *::before, *::after {
            transition-duration: 0.01ms !important;
            animation-duration: 0.01ms !important;
            animation-iteration-count: 1 !important;
        }
    }
</style>