@php
    $w = $warehouse ?? null;
@endphp

<div class="grid grid-cols-1 md:grid-cols-2 gap-4">
    <div>
        <label class="block text-sm font-medium text-gray-700">Establecimiento <span class="text-red-500">*</span></label>
        <select name="establishment_id" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
            <option value="">Seleccionar...</option>
            @foreach ($establishments as $est)
                <option value="{{ $est->id }}" @selected(old('establishment_id', $w->establishment_id ?? '') == $est->id)>{{ $est->name }}</option>
            @endforeach
        </select>
        @error('establishment_id') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
    </div>
    <div>
        <label class="block text-sm font-medium text-gray-700">Nombre <span class="text-red-500">*</span></label>
        <input type="text" name="name" value="{{ old('name', $w->name ?? '') }}" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
        @error('name') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
    </div>
    <div>
        <label class="block text-sm font-medium text-gray-700">Código <span class="text-red-500">*</span></label>
        <input type="text" name="code" value="{{ old('code', $w->code ?? '') }}" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
        @error('code') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
    </div>
    <div>
        <label class="block text-sm font-medium text-gray-700">Ubicación</label>
        <input type="text" name="location" value="{{ old('location', $w->location ?? '') }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
    </div>
    <div class="flex items-center gap-2">
        <input type="hidden" name="is_active" value="0">
        <input type="checkbox" id="is_active" name="is_active" value="1" class="rounded border-gray-300 text-blue-600 shadow-sm focus:ring-blue-500" @checked(old('is_active', $w->is_active ?? true))>
        <label for="is_active" class="text-sm font-medium text-gray-700">Activo</label>
    </div>
</div>