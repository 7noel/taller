{{-- ============ SECCIÓN 6: FOTOS ============ --}}
@php
    $photoHelper = $isEdit
        ? 'Toma o elige una foto y se sube automáticamente apenas la capturas. Puedes seguir tomando mientras se suben las anteriores.'
        : 'Toma o elige las fotos del vehículo. Se comprimen solas y se guardarán al crear el inventario.';
@endphp
<div class="pb-6 mb-6">
    <h3 class="text-lg font-semibold text-gray-800 mb-1">Fotos del vehículo</h3>
    <p class="text-sm text-gray-500 mb-4">{{ $photoHelper }}</p>

    <div class="flex flex-wrap items-center gap-3">
        <button type="button" id="btn-photo-capture" class="btn btn-primary" title="Abrir la cámara para tomar una foto">
            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"/><path stroke-linecap="round" stroke-linejoin="round" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
            Tomar foto
        </button>
        <button type="button" id="btn-photo-upload" class="btn btn-secondary" title="Seleccionar fotos de la galería o del equipo">
            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><rect x="3" y="3" width="18" height="18" rx="2"/><circle cx="8.5" cy="8.5" r="1.5"/><path stroke-linecap="round" stroke-linejoin="round" d="M21 15l-5-5L5 21"/></svg>
            Subir fotos
        </button>
        <span id="photo-count" class="hidden px-2.5 py-1 rounded-full bg-blue-50 text-blue-700 text-xs font-semibold">0 fotos</span>
    </div>

    <div class="mt-3 flex flex-wrap items-center gap-2" id="photo-quality-bar">
        <span class="text-xs font-medium text-gray-700">Calidad de cámara:</span>
        <button type="button" class="quality-btn inline-flex items-center px-3 py-1.5 rounded-full text-xs font-semibold transition-colors bg-gray-100 text-gray-600 hover:bg-gray-200" data-quality="estandar" title="1600 px · JPEG 0.85 (rápida)">Estándar</button>
        <button type="button" class="quality-btn inline-flex items-center px-3 py-1.5 rounded-full text-xs font-semibold transition-colors bg-gray-100 text-gray-600 hover:bg-gray-200" data-quality="alta" title="2048 px · JPEG 0.90">Alta</button>
        <button type="button" class="quality-btn inline-flex items-center px-3 py-1.5 rounded-full text-xs font-semibold transition-colors bg-gray-100 text-gray-600 hover:bg-gray-200" data-quality="maxima" title="2560 px · JPEG 0.92">Máxima</button>
    </div>

    <div id="photo-upload-status" class="mt-3 hidden items-center gap-2 text-sm text-gray-600">
        <svg class="animate-spin h-4 w-4 text-blue-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
        <span id="photo-upload-status-text">Subiendo foto…</span>
    </div>

    <div id="photo-form-error" class="mt-3 hidden px-4 py-3 bg-red-50 border border-red-200 text-red-700 rounded-lg text-sm"></div>

    <input type="file" id="photo-capture-input" accept="image/*" capture="environment" class="hidden" tabindex="-1" aria-hidden="true">
    <input type="file" id="photo-input" name="photos[]" accept="image/*" multiple class="hidden" tabindex="-1" aria-hidden="true">

    <div id="photo-preview" class="mt-4 grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 gap-4">
        @if ($isEdit)
            @foreach ($checkIn->photos as $photo)
                <div class="photo-item relative group" data-id="{{ $photo->id }}">
                    <img src="{{ $photo->url }}" class="photo-img w-full h-32 object-cover rounded-lg border border-gray-200" alt="Foto del vehículo">
                    <button type="button" class="photo-delete absolute top-1 right-1 flex items-center justify-center bg-red-600 text-white rounded-full w-6 h-6 shadow hover:bg-red-700" title="Eliminar foto">
                        <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                </div>
            @endforeach
        @endif
    </div>

    <p class="mt-3 text-xs text-gray-400">En el celular, «Tomar foto» abre la cámara trasera. Si el navegador lo permite (HTTPS) se usa la cámara integrada con el lente principal, vista previa 3:4, zoom y selector de proporción. La calidad elegida aplica a las fotos de cámara; cada una se sube en cuanto la tomas.</p>

    {{-- Overlay de cámara integrada (solo se usa en contexto seguro / getUserMedia) --}}
    <div id="photo-camera-modal" class="fixed inset-0 z-50 hidden bg-black">
        {{-- Vista previa en canvas: muestra exactamente el encuadre final --}}
        <div id="photo-camera-stage" class="absolute inset-0 flex items-center justify-center p-4 bg-black">
            <canvas id="photo-camera-canvas" class="block max-w-full max-h-full w-auto h-auto"></canvas>
        </div>
        <video id="photo-camera-video" autoplay playsinline muted class="absolute opacity-0 pointer-events-none" style="width:1px;height:1px;left:-10px;top:-10px" aria-hidden="true"></video>

        <div class="absolute inset-x-0 top-0 flex items-center justify-between p-4 bg-gradient-to-b from-black/70 to-transparent">
            <span class="text-white text-sm font-semibold flex items-center gap-2">
                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"/><path stroke-linecap="round" stroke-linejoin="round" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                Cámara trasera
            </span>
            <div class="flex items-center gap-2">
                <button type="button" id="btn-camera-switch" class="hidden inline-flex h-9 w-9 items-center justify-center rounded-lg bg-white/15 text-white hover:bg-white/25" title="Cambiar de lente (principal / gran angular)">
                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M8 7h11m0 0l-4-4m4 4l-4 4M16 17H5m0 0l4 4m-4-4l4-4"/></svg>
                </button>
                <button type="button" id="btn-camera-rotate" class="inline-flex h-9 w-9 items-center justify-center rounded-lg bg-white/15 text-white hover:bg-white/25" title="Rotar 90°">
                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
                </button>
                <button type="button" id="btn-camera-close" class="inline-flex items-center gap-1 rounded-lg bg-white/15 text-white px-3 py-1.5 text-sm font-semibold hover:bg-white/25" title="Cerrar cámara">
                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                    Cerrar
                </button>
            </div>
        </div>

        <div class="absolute inset-x-0 bottom-0 pb-7 pt-20 bg-gradient-to-t from-black/80 to-transparent text-center">
            <div class="flex flex-wrap justify-center gap-2 mb-4 px-4">
                @foreach (['3:4', '1:1', '4:3', '16:9'] as $ratio)
                    <button type="button" class="cam-ratio-btn rounded-full px-3 py-1.5 text-xs font-semibold text-white transition-colors bg-white/15 hover:bg-white/25" data-ratio="{{ $ratio }}">{{ $ratio }}</button>
                @endforeach
            </div>
            <div class="flex items-center justify-center gap-4 mb-3 text-white">
                <button type="button" id="btn-camera-zoom-out" class="inline-flex h-9 w-9 items-center justify-center rounded-full bg-white/15 hover:bg-white/25" title="Alejar (zoom −)">
                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M5 12h14"/></svg>
                </button>
                <span id="camera-zoom-label" class="inline-block w-14 text-sm font-semibold">1×</span>
                <button type="button" id="btn-camera-zoom-in" class="inline-flex h-9 w-9 items-center justify-center rounded-full bg-white/15 hover:bg-white/25" title="Acercar (zoom +)">
                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 5v14M5 12h14"/></svg>
                </button>
            </div>
            <p id="photo-camera-status" class="text-white text-sm mb-4 px-4">Toma las fotos: cada disparo se encola y se sube solo.</p>
            <button type="button" id="btn-camera-shutter" class="mx-auto h-16 w-16 rounded-full border-4 border-white flex items-center justify-center focus:outline-none active:scale-95 transition-transform" title="Tomar foto">
                <span class="block h-12 w-12 rounded-full bg-white"></span>
            </button>
        </div>
    </div>
</div>