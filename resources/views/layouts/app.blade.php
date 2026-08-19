<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', config('app.name', 'Taller Mecánico'))</title>

    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    <!-- Tabulator CSS -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/tabulator/6.2.0/css/tabulator.min.css" rel="stylesheet">
    <!-- Tom Select CSS -->
    <link href="https://cdn.jsdelivr.net/npm/tom-select@2.3.1/dist/css/tom-select.css" rel="stylesheet">
    @stack('styles')
</head>
<body class="font-sans antialiased">
    <div class="min-h-screen bg-gray-100">
        @include('layouts.navigation')

        @isset($header)
            <header class="bg-white shadow">
                <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
                    {{ $header }}
                </div>
            </header>
        @endisset

        <main>
            @yield('content')
            {{ $slot }}
        </main>
    </div>

    <!-- JavaScript CDN -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/tabulator/6.2.0/js/tabulator.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
    <script src="https://cdn.jsdelivr.net/npm/tom-select@2.3.1/dist/js/tom-select.complete.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/tesseract.js@5/dist/tesseract.min.js"></script>
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
    @stack('scripts')
</body>
</html>