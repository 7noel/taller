<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">{{ __('Nueva Plantilla de Formulario') }}</h2>
            <a href="{{ route('form-templates.index') }}" class="btn btn-secondary">Volver</a>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8">
            @if ($errors->any())
                <div class="mb-4 px-4 py-3 bg-red-50 border border-red-200 text-red-700 rounded-lg text-sm">{{ $errors->first() }}</div>
            @endif

            <form method="POST" action="{{ route('form-templates.store') }}" class="card">
                @csrf
                <div class="p-6 grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium text-gray-700">Tipo de formulario <span class="text-red-500">*</span></label>
                        <select name="type" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                            <option value="">Seleccionar...</option>
                            @foreach ($types as $value => $label)
                                <option value="{{ $value }}" @selected(old('type') === $value)>{{ $label }}</option>
                            @endforeach
                        </select>
                        @error('type') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                    </div>

                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium text-gray-700">Nombre de la plantilla <span class="text-red-500">*</span></label>
                        <input type="text" name="name" value="{{ old('name') }}" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500" placeholder="Ej. Control de calidad estándar">
                        @error('name') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700">Taller (establecimiento)</label>
                        <select name="establishment_id" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                            <option value="">Global (todos los talleres)</option>
                            @foreach ($establishments as $est)
                                <option value="{{ $est->id }}" @selected(old('establishment_id') == $est->id)>{{ $est->name }}</option>
                            @endforeach
                        </select>
                        <p class="mt-1 text-xs text-gray-500">Si no seleccionas un taller, la plantilla queda como respaldo global.</p>
                    </div>

                    <div class="flex items-end gap-2 pb-1">
                        <input type="hidden" name="is_active" value="0">
                        <input type="checkbox" id="is_active" name="is_active" value="1" @checked(old('is_active', true)) class="rounded border-gray-300 text-blue-600 shadow-sm focus:ring-blue-500">
                        <label for="is_active" class="text-sm font-medium text-gray-700">Plantilla activa</label>
                    </div>

                    <div class="md:col-span-2 flex justify-end gap-2 border-t border-gray-100 pt-4">
                        <a href="{{ route('form-templates.index') }}" class="btn btn-secondary">Cancelar</a>
                        <button type="submit" class="btn btn-primary">Crear plantilla</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
