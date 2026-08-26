<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Caché de PDFs (inventarios)
    |--------------------------------------------------------------------------
    |
    | Si cache_enabled es true, los PDFs generados se guardan en storage con
    | un nombre versionado por "fingerprint" (datos del inventario + empresa +
    | checklist + plantilla) y se sirven sin regenerar mientras nada cambie.
    |
    | PDF_CACHE=false fuerza la regeneración en cada visita (útil en
    | desarrollo o ante cualquier duda). Para vaciar la caché manualmente:
    | php artisan pdf:clear
    |
    */

    'cache_enabled' => env('PDF_CACHE', true),

    'disk' => env('PDF_CACHE_DISK', 'local'),

    'directory' => 'check-in-pdfs',
];
