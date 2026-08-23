<x-app-layout>
    <x-slot:title>Mi Perfil</x-slot:title>

    <div class="py-12">
        <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white p-6 rounded-lg shadow-md">
                <h1 class="text-2xl font-bold mb-6">Mi Perfil</h1>

                @if(session('status') === 'profile-updated' || session('success'))
                    <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
                        {{ session('success') ?? 'Perfil actualizado correctamente.' }}
                    </div>
                @endif

                <form method="POST" action="{{ route('profile.update') }}">
                    @csrf
                    @method('PATCH')

                    <div class="mb-4">
                        <label for="name" class="block text-sm font-medium text-gray-700">Nombre completo</label>
                        <input type="text" id="name" name="name" value="{{ old('name', $user->name) }}" required
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 py-2.5">
                        @error('name') <span class="text-red-600 text-sm">{{ $message }}</span> @enderror
                    </div>

                    <div class="mb-4">
                        <label for="email" class="block text-sm font-medium text-gray-700">Correo electrónico</label>
                        <input type="email" id="email" name="email" value="{{ old('email', $user->email) }}" required
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 py-2.5">
                        @error('email') <span class="text-red-600 text-sm">{{ $message }}</span> @enderror
                    </div>

                    <div class="mb-4">
                        <label for="phone" class="block text-sm font-medium text-gray-700">Teléfono</label>
                        <input type="text" id="phone" name="phone" value="{{ old('phone', $user->phone) }}"
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 py-2.5">
                        @error('phone') <span class="text-red-600 text-sm">{{ $message }}</span> @enderror
                    </div>

                    <div class="border-t border-gray-200 my-6 pt-4">
                        <h2 class="text-lg font-semibold mb-4">Cambiar contraseña</h2>

                        <div class="mb-4">
                            <label for="password" class="block text-sm font-medium text-gray-700">Nueva contraseña</label>
                            <div class="relative mt-1">
                                <input type="password" id="password" name="password"
                                    class="block w-full rounded-md border-gray-300 pr-10 shadow-sm focus:border-blue-500 focus:ring-blue-500 py-2.5">
                                <button type="button" id="password-toggle"
                                    class="absolute inset-y-0 right-0 flex items-center pr-3 text-gray-400 hover:text-gray-600"
                                    aria-label="Mostrar contraseña">
                                    <svg id="password-eye" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                    </svg>
                                    <svg id="password-eye-off" class="h-5 w-5 hidden" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.542-7a9.97 9.97 0 011.563-2.029m5.858-5.196A9.94 9.94 0 0112 5c4.478 0 8.268 2.943 9.542 7a9.97 9.97 0 01-4.025 5.248"/>
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M3 3l18 18"/>
                                    </svg>
                                </button>
                            </div>
                            @error('password') <span class="text-red-600 text-sm">{{ $message }}</span> @enderror
                        </div>

                        <div class="mb-4">
                            <label for="password_confirmation" class="block text-sm font-medium text-gray-700">Confirmar nueva contraseña</label>
                            <div class="relative mt-1">
                                <input type="password" id="password_confirmation" name="password_confirmation"
                                    class="block w-full rounded-md border-gray-300 pr-10 shadow-sm focus:border-blue-500 focus:ring-blue-500 py-2.5">
                                <button type="button" id="password-confirmation-toggle"
                                    class="absolute inset-y-0 right-0 flex items-center pr-3 text-gray-400 hover:text-gray-600"
                                    aria-label="Mostrar contraseña">
                                    <svg id="password-confirmation-eye" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                    </svg>
                                    <svg id="password-confirmation-eye-off" class="h-5 w-5 hidden" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.542-7a9.97 9.97 0 011.563-2.029m5.858-5.196A9.94 9.94 0 0112 5c4.478 0 8.268 2.943 9.542 7a9.97 9.97 0 01-4.025 5.248"/>
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M3 3l18 18"/>
                                    </svg>
                                </button>
                            </div>
                        </div>
                    </div>

                    <div class="flex justify-end">
                        <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition">
                            Guardar cambios
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            function setupPasswordToggle(inputId, toggleId, eyeId, eyeOffId) {
                const input = document.getElementById(inputId);
                const toggle = document.getElementById(toggleId);
                const eye = document.getElementById(eyeId);
                const eyeOff = document.getElementById(eyeOffId);
                if (input && toggle) {
                    toggle.addEventListener('click', function () {
                        const show = input.type === 'password';
                        input.type = show ? 'text' : 'password';
                        eye.classList.toggle('hidden', !show);
                        eyeOff.classList.toggle('hidden', show);
                        toggle.setAttribute('aria-label', show ? 'Ocultar contraseña' : 'Mostrar contraseña');
                    });
                }
            }

            setupPasswordToggle('password', 'password-toggle', 'password-eye', 'password-eye-off');
            setupPasswordToggle('password_confirmation', 'password-confirmation-toggle', 'password-confirmation-eye', 'password-confirmation-eye-off');
        });
    </script>
    @endpush
</x-app-layout>
