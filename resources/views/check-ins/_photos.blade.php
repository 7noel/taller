{{-- ============ SECCIÓN 6: FOTOS ============ --}}
<div class="pb-6 mb-6">
    <h3 class="text-lg font-semibold text-gray-800 mb-1">Fotos del vehículo</h3>
    <p class="text-sm text-gray-500 mb-4">Adjunta fotos del ingreso. En modo edición se suben de inmediato vía AJAX; en creación se previsualizan y se guardan junto con el inventario.</p>

    <div class="flex flex-wrap items-center gap-3">
        <label class="inline-flex items-center gap-2 px-4 py-2 bg-white border border-gray-300 rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest hover:bg-gray-50 cursor-pointer">
            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg>
            Agregar foto
            <input type="file" id="photo-input" name="photos[]" accept="image/*" multiple class="hidden">
        </label>
        <span id="photo-progress" class="text-xs text-gray-500 hidden">Subiendo...</span>
    </div>

    <div id="photo-preview" class="mt-4 grid grid-cols-2 md:grid-cols-4 gap-4">
        @if ($isEdit)
            @foreach ($checkIn->photos as $photo)
                <div class="photo-item relative group" data-id="{{ $photo->id }}">
                    <img src="{{ $photo->url }}" class="w-full h-32 object-cover rounded-lg border border-gray-200" alt="Foto del vehículo">
                    <button type="button" class="photo-delete absolute top-1 right-1 bg-red-600 text-white rounded-full w-6 h-6 hidden group-hover:flex items-center justify-center" title="Eliminar">
                        <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                </div>
            @endforeach
        @endif
    </div>
</div>