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
    </head>
    <body class="font-sans text-gray-900 antialiased">
        @php $variant = $variant ?? 'centered'; @endphp

        @if ($variant === 'split')
            {{-- ===== Pantalla dividida: panel lateral oscuro + formulario ===== --}}
            <div class="min-h-screen flex bg-gray-50">
                @include('partials.auth-split-side')

                <main class="flex flex-1 items-center justify-center px-4 py-10 sm:px-8">
                    {{ $slot }}
                </main>
            </div>
        @else
            {{-- ===== Variante por defecto: card centrada (starter kit) ===== --}}
            <div class="min-h-screen flex flex-col sm:justify-center items-center pt-6 sm:pt-0 bg-gray-100">
                <div>
                    <a href="/">
                        <x-application-logo class="w-20 h-20 fill-current text-gray-500" />
                    </a>
                </div>

                <div class="w-full sm:max-w-md mt-6 px-6 py-4 bg-white shadow-md overflow-hidden sm:rounded-lg">
                    {{ $slot }}
                </div>
            </div>
        @endif

        @include('partials.form-guard')
    </body>
</html>
