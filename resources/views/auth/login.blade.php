<x-guest-layout variant="split">
    {{-- Contenedor del formulario (columna derecha) --}}
    <div class="w-full max-w-md">
        {{-- Logo en móvil (en desktop se ve en el panel izquierdo) --}}
        <div class="mb-8 flex flex-col items-center lg:hidden">
            <div class="flex h-16 w-16 items-center justify-center rounded-2xl bg-slate-900 shadow-lg">
                <svg class="h-8 w-8 text-teal-300" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 2v4m0 12v4m10-10h-4M6 12H2m17.07-7.07l-2.83 2.83M9.17 14.83l-2.83 2.83m14.14 0l-2.83-2.83M9.17 9.17L6.34 6.34"/>
                    <circle cx="12" cy="12" r="4.5"/>
                    <circle cx="12" cy="12" r="1.5" fill="currentColor" stroke="none"/>
                </svg>
            </div>
            <h1 class="mt-4 text-xl font-bold text-gray-900">{{ config('app.name', 'Taller Mecánico') }}</h1>
        </div>

        {{-- Encabezado del formulario --}}
        <div class="mb-8">
            <h2 class="text-2xl font-bold tracking-tight text-gray-900">Iniciar sesión</h2>
            <p class="mt-2 text-sm text-gray-600">Ingresa a la plataforma de gestión del taller para continuar.</p>
        </div>

        <!-- Session Status -->
        <x-auth-session-status class="mb-4" :status="session('status')" />

        <form method="POST" action="{{ route('login') }}" class="space-y-5">
            @csrf

            <!-- Email Address -->
            <div>
                <x-input-label for="email" :value="__('Correo electrónico')" />
                <div class="mt-1">
                    <x-text-input id="email" class="block w-full" type="email" name="email" :value="old('email')" required autofocus autocomplete="username" placeholder="usuario@taller.com" />
                </div>
                <x-input-error :messages="$errors->get('email')" class="mt-2" />
            </div>

            <!-- Password -->
            <div>
                <div class="flex items-center justify-between">
                    <x-input-label for="password" :value="__('Contraseña')" />
                    @if (Route::has('password.request'))
                        <a class="text-sm font-medium text-blue-600 hover:text-blue-800 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 rounded-md" href="{{ route('password.request') }}">
                            {{ __('¿Olvidaste tu contraseña?') }}
                        </a>
                    @endif
                </div>

                <div class="relative mt-1">
                    <x-text-input id="password" class="block w-full pr-10"
                                    type="password"
                                    name="password"
                                    required autocomplete="current-password" placeholder="••••••••" />

                    <button type="button" id="password-toggle"
                        class="absolute inset-y-0 right-0 flex items-center pr-3 text-gray-400 hover:text-gray-600 focus:outline-none"
                        aria-label="Mostrar contraseña">
                        <svg id="password-eye" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                            <path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                        </svg>
                        <svg id="password-eye-off" class="h-5 w-5 hidden" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.542-7a9.97 9.97 0 011.563-2.029m5.858-5.196A9.94 9.94 0 0112 5c4.478 0 8.268 2.943 9.542 7a9.97 9.97 0 01-4.025 5.248"/>
                            <path stroke-linecap="round" stroke-linejoin="round" d="M3 3l18 18"/>
                        </svg>
                    </button>
                </div>

                <x-input-error :messages="$errors->get('password')" class="mt-2" />
            </div>

            <!-- Remember Me -->
            <div class="flex items-center justify-between">
                <label for="remember_me" class="inline-flex items-center select-none">
                    <input id="remember_me" type="checkbox" class="rounded border-gray-300 text-blue-600 shadow-sm focus:ring-blue-500" name="remember">
                    <span class="ms-2 text-sm text-gray-700">{{ __('Recordarme') }}</span>
                </label>
            </div>

            <x-primary-button class="w-full justify-center py-3 transition-all duration-150 ease-out">
                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1"/>
                </svg>
                {{ __('Iniciar sesión') }}
            </x-primary-button>
        </form>

        <p class="mt-8 text-center text-xs text-gray-500">
            &copy; {{ date('Y') }} {{ config('app.name', 'Taller Mecánico') }} · Sistema de gestión del taller
        </p>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const passwordInput = document.getElementById('password');
            const toggleBtn = document.getElementById('password-toggle');
            const eyeIcon = document.getElementById('password-eye');
            const eyeOffIcon = document.getElementById('password-eye-off');

            if (passwordInput && toggleBtn) {
                toggleBtn.addEventListener('click', function () {
                    const show = passwordInput.type === 'password';
                    passwordInput.type = show ? 'text' : 'password';
                    eyeIcon.classList.toggle('hidden', !show);
                    eyeOffIcon.classList.toggle('hidden', show);
                    toggleBtn.setAttribute('aria-label', show ? 'Ocultar contraseña' : 'Mostrar contraseña');
                });
            }
        });
    </script>
</x-guest-layout>