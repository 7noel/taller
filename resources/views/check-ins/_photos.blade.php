{{-- ============ SECCIÓN 6: FOTOS ============ --}}
<div class="pb-6 mb-6">
    <h3 class="text-lg font-semibold text-gray-800 mb-1">📷 Fotos del vehículo</h3>
    <p class="text-sm text-gray-500 mb-4">Adjunta fotos del ingreso. En modo edición se suben de inmediato vía AJAX; en creación se previsualizan y se guardan junto con el inventario.</p>

    <div class="flex flex-wrap items-center gap-3">
        <label class="inline-flex items-center px-4 py-2 bg-white border border-gray-300 rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest hover:bg-gray-50 cursor-pointer">
            + Agregar foto
            <input type="file" id="photo-input" name="photos[]" accept="image/*" multiple class="hidden">
        </label>
        <span id="photo-progress" class="text-xs text-gray-500 hidden">Subiendo...</span>
    </div>

    <div id="photo-preview" class="mt-4 grid grid-cols-2 md:grid-cols-4 gap-4">
        @if ($isEdit)
            @foreach ($checkIn->photos as $photo)
                <div class="photo-item relative group" data-id="{{ $photo->id }}">
                    <img src="{{ $photo->url }}" class="w-full h-32 object-cover rounded-lg border border-gray-200" alt="Foto del vehículo">
                    <button type="button" class="photo-delete absolute top-1 right-1 bg-red-600 text-white rounded-full w-6 h-6 text-xs hidden group-hover:flex items-center justify-center" title="Eliminar">✕</button>
                </div>
            @endforeach
        @endif
    </div>
</div>