<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">{{ isset($user) ? __('Editar Usuario') : __('Crear Usuario') }}</h2>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="bg-white shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <form action="{{ isset($user) ? route('users.update', $user) : route('users.store') }}" method="POST">
                        @csrf
                        @if(isset($user)) @method('PUT') @endif

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Nombre <span class="text-red-500">*</span></label>
                                <input type="text" name="name" value="{{ old('name', $user->name ?? '') }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500" required>
                                @error('name') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700">Email <span class="text-red-500">*</span></label>
                                <input type="email" name="email" value="{{ old('email', $user->email ?? '') }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500" required>
                                @error('email') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700">Teléfono</label>
                                <input type="text" name="phone" value="{{ old('phone', $user->phone ?? '') }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                @error('phone') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700">Establecimiento</label>
                                <select name="establishment_id" id="establishment-select" class="mt-1 block w-full">
                                    <option value="">Sin asignar</option>
                                    @foreach($establishments as $id => $name)
                                        <option value="{{ $id }}" {{ old('establishment_id', $user->establishment_id ?? '') == $id ? 'selected' : '' }}>{{ $name }}</option>
                                    @endforeach
                                </select>
                                @error('establishment_id') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700">{{ isset($user) ? 'Nueva Contraseña' : 'Contraseña' }} <span class="text-red-500">{{ isset($user) ? '' : '*' }}</span></label>
                                <div class="relative mt-1">
                                    <input type="password" name="password" id="password-field" class="block w-full rounded-md border-gray-300 pr-10 shadow-sm focus:border-blue-500 focus:ring-blue-500" {{ isset($user) ? '' : 'required' }}>
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
                                @error('password') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                            </div>

                            <div class="md:col-span-2">
                                <label class="block text-sm font-medium text-gray-700">Roles</label>
                                <select name="roles[]" id="roles-select" multiple class="mt-1 block w-full">
                                    @foreach($roles as $role)
                                        <option value="{{ $role }}" {{ (old('roles') ? in_array($role, old('roles')) : (isset($user) && $user->roles->contains('name', $role))) ? 'selected' : '' }}>{{ $role }}</option>
                                    @endforeach
                                </select>
                                @error('roles.*') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                            </div>
                        </div>

                        <div class="mt-6 flex justify-end space-x-3">
                            <a href="{{ route('users.index') }}" class="bg-gray-300 hover:bg-gray-400 text-gray-800 font-bold py-2 px-4 rounded">Cancelar</a>
                            <button type="submit" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                                {{ isset($user) ? 'Actualizar' : 'Crear' }}
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const establishmentSelect = new TomSelect('#establishment-select', {
                maxItems: 1,
                closeAfterSelect: true,
                create: false,
                placeholder: 'Buscar establecimiento...',
                copyClassesToDropdown: false,
                dropdownParent: 'body',
                onItemAdd: function() {
                    this.blur();
                    this.close();
                },
                onDropdownOpen: function() {
                    if (this.items.length) {
                        this.setTextValue('');
                        this.control_input.setSelectionRange(0, 0);
                    }
                }
            });

            new TomSelect('#roles-select', {
                maxItems: null,
                closeAfterSelect: false,
                create: false,
                placeholder: 'Seleccionar roles...',
                copyClassesToDropdown: false,
                dropdownParent: 'body',
            });

            const passwordInput = document.getElementById('password-field');
            const toggleBtn = document.getElementById('password-toggle');
            const eyeIcon = document.getElementById('password-eye');
            const eyeOffIcon = document.getElementById('password-eye-off');

            toggleBtn.addEventListener('click', function() {
                const show = passwordInput.type === 'password';
                passwordInput.type = show ? 'text' : 'password';
                eyeIcon.classList.toggle('hidden', !show);
                eyeOffIcon.classList.toggle('hidden', show);
                toggleBtn.setAttribute('aria-label', show ? 'Ocultar contraseña' : 'Mostrar contraseña');
            });
        });
    </script>
    @endpush
</x-app-layout>