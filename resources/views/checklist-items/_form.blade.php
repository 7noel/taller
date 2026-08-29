@php
    $item = $checkInChecklistItem ?? null;
@endphp

<div class="grid grid-cols-1 md:grid-cols-3 gap-4">
    <div class="md:col-span-2">
        <label class="block text-sm font-medium text-gray-700">Nombre del ítem <span class="text-red-500">*</span></label>
        <input type="text" name="name" value="{{ old('name', $item->name ?? '') }}" required maxlength="100" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
        @error('name') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
    </div>
    <div>
        <label class="block text-sm font-medium text-gray-700">Categoría <span class="text-red-500">*</span></label>
        <select name="category" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
            <option value="">Seleccionar...</option>
            @foreach (\App\Models\CheckInChecklistItem::CATEGORIES as $value => $label)
                <option value="{{ $value }}" @selected(old('category', $item->category ?? '') === $value)>{{ $label }}</option>
            @endforeach
        </select>
        @error('category') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
    </div>
    <div>
        <label class="block text-sm font-medium text-gray-700">Orden</label>
        <input type="number" name="order" min="0" value="{{ old('order', $item->order ?? 0) }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
    </div>
    <div class="flex items-center gap-2">
        <input type="hidden" name="is_active" value="0">
        <input type="checkbox" id="is_active" name="is_active" value="1" class="rounded border-gray-300 text-blue-600 shadow-sm focus:ring-blue-500" @checked(old('is_active', $item->is_active ?? true))>
        <label for="is_active" class="text-sm font-medium text-gray-700">Activo</label>
    </div>
</div>

<div class="flex justify-end gap-2 border-t border-gray-100 pt-4 mt-4">
    <a href="{{ route('checklist-items.index') }}" class="btn btn-secondary">Cancelar</a>
    <button type="submit" class="btn btn-primary">Guardar</button>
</div>
