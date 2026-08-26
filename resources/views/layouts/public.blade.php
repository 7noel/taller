<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ $title ?? config('app.name', 'Taller Mecánico') }}</title>

    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    @include('partials.design-base')
    @include('partials.design-components')
    @stack('styles')
</head>
<body class="font-sans text-gray-900 antialiased bg-gray-50">
    {{ $slot }}

    {{-- Modal global de confirmación + guard CSRF/anti-doble envío --}}
    @include('partials.confirm-modal')
    @include('partials.form-guard')

    @stack('scripts')
</body>
</html>
