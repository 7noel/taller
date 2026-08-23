@php
    $initialDamages = old('damages', $isEdit ? $checkIn->damages->map(fn ($d) => [
        'id' => $d->id,
        'damage_type' => $d->damage_type,
        'side' => $d->side,
        'pos_x' => $d->pos_x,
        'pos_y' => $d->pos_y,
        'notes' => $d->notes,
    ])->values()->all() : []);
@endphp

{{-- ============ SECCIÓN 5: DAÑOS (MOCKUP) ============ --}}
<div class="border-b border-gray-200 pb-6 mb-6">
    <h3 class="text-lg font-semibold text-gray-800 mb-1">🔧 Marcación de daños</h3>
    <p class="text-sm text-gray-500 mb-4">Haz clic sobre la imagen para colocar un marcador. Las coordenadas se guardan en % (independientes del tamaño).</p>

    <div class="flex flex-wrap gap-2 mb-4">
        <button type="button" data-type="scratch" class="damage-tool-btn inline-flex items-center px-3 py-1.5 rounded-md text-xs font-semibold text-white bg-green-600 hover:bg-green-700">🖊️ Rayón</button>
        <button type="button" data-type="dent" class="damage-tool-btn inline-flex items-center px-3 py-1.5 rounded-md text-xs font-semibold text-white bg-red-600 hover:bg-red-700">🔴 Abolladura</button>
        <button type="button" data-type="crack" class="damage-tool-btn inline-flex items-center px-3 py-1.5 rounded-md text-xs font-semibold text-white bg-blue-600 hover:bg-blue-700">🔵 Quiñe</button>
        <button type="button" id="btn-damage-undo" class="inline-flex items-center px-3 py-1.5 rounded-md text-xs font-semibold text-gray-700 bg-gray-200 hover:bg-gray-300">↩️ Deshacer</button>
        <button type="button" id="btn-damage-clear" class="inline-flex items-center px-3 py-1.5 rounded-md text-xs font-semibold text-red-700 bg-red-100 hover:bg-red-200">🗑️ Borrar marcas</button>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-4 items-start">
        <div class="bg-white border border-gray-200 rounded-lg p-4">
            <div id="damage-mockup-image-wrap" class="relative inline-block max-w-full hidden">
                <img id="damage-mockup-image" src="" alt="Mockup del vehículo" class="max-w-full h-auto cursor-crosshair select-none">
                <div id="damage-markers-layer" class="absolute inset-0 pointer-events-none"></div>
            </div>
            <p id="damage-no-image" class="text-sm text-gray-500 hidden">No hay imagen de mockup para este tipo de vehículo. Usa el selector de tipo para registrar daños.</p>
        </div>

        <div class="bg-white border border-gray-200 rounded-lg p-4">
            <h4 class="text-sm font-semibold text-gray-700 mb-2">Daños agregados (<span id="damage-count">{{ count($initialDamages) }}</span>)</h4>
            <div id="damage-list" class="space-y-2 max-h-[420px] overflow-y-auto pr-1">
                @foreach ($initialDamages as $index => $damage)
                    @php $damageKey = $damage['id'] ?? 'new_' . $index; @endphp
                    <div class="damage-row bg-gray-50 border border-gray-200 rounded-lg p-3 flex flex-wrap items-center gap-3" data-key="{{ $damageKey }}">
                        <input type="hidden" name="damages[{{ $index }}][id]" value="{{ $damage['id'] ?? '' }}">
                        <span class="inline-flex items-center px-2 py-0.5 rounded-md text-xs font-medium
                            {{ $damage['damage_type'] === 'scratch' ? 'bg-green-100 text-green-800' : ($damage['damage_type'] === 'dent' ? 'bg-red-100 text-red-800' : 'bg-blue-100 text-blue-800') }}">
                            {{ \App\Models\CheckInDamage::DAMAGE_TYPE_LABELS[$damage['damage_type']] ?? $damage['damage_type'] }}
                        </span>
                        @if (!empty($damage['pos_x']) && !empty($damage['pos_y']))
                            <span class="text-xs text-gray-500">X: {{ $damage['pos_x'] }}% Y: {{ $damage['pos_y'] }}%</span>
                        @endif
                        <input type="hidden" name="damages[{{ $index }}][damage_type]" value="{{ $damage['damage_type'] }}">
                        <input type="hidden" name="damages[{{ $index }}][side]" value="{{ $damage['side'] }}">
                        <input type="hidden" name="damages[{{ $index }}][pos_x]" value="{{ $damage['pos_x'] ?? '' }}">
                        <input type="hidden" name="damages[{{ $index }}][pos_y]" value="{{ $damage['pos_y'] ?? '' }}">
                        <input type="text" name="damages[{{ $index }}][notes]" value="{{ $damage['notes'] ?? '' }}" placeholder="Nota..." class="flex-1 min-w-[120px] rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-xs">
                        <button type="button" class="damage-remove text-red-600 hover:text-red-800 text-xs font-medium">✕ Eliminar</button>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
</div>