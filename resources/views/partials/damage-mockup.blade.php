{{-- Silueta del vehículo con marcadores de daños (compartido).
     Requiere: $damagesWithCoords (array de {damage_type, pos_x, pos_y}), $bodyType (string). --}}
@php
    $damagesWithCoords = $damagesWithCoords ?? [];
    $bodyType = $bodyType ?? '';
    $hasCoords = count($damagesWithCoords) > 0;
@endphp

<div id="damage-mockup-wrap" class="relative inline-block max-w-full {{ $hasCoords ? '' : 'hidden' }}">
    <img id="damage-mockup-img" src="data:image/gif;base64,R0lGODlhAQABAAAAACH5BAEKAAEALAAAAAABAAEAAAICTAEAOw==" alt="Silueta del vehículo con daños" class="max-w-full h-auto rounded-lg border border-gray-200">
    <div id="damage-mockup-markers" class="absolute inset-0 pointer-events-none"></div>
</div>
<p id="damage-mockup-no-image" class="text-sm text-gray-500 {{ $hasCoords ? 'hidden' : '' }}">
    @if ($bodyType)
        Los daños registrados no tienen posición en el gráfico; se listan debajo.
    @else
        No hay imagen de mockup para este tipo de vehículo.
    @endif
</p>

<script>
(function () {
    const damages = @json($damagesWithCoords);
    const bodyType = @json($bodyType);
    const wrap = document.getElementById('damage-mockup-wrap');
    const img = document.getElementById('damage-mockup-img');
    const markers = document.getElementById('damage-mockup-markers');
    const noImage = document.getElementById('damage-mockup-no-image');

    if (!wrap || !bodyType || damages.length === 0) {
        if (noImage && !bodyType) noImage.textContent = 'No hay imagen de mockup para este tipo de vehículo.';
        return;
    }

    const colors = { 'scratch': '#008000', 'dent': '#ff0000', 'crack': '#0000ff' };
    const icons = {
        'scratch': `<svg class='h-5 w-5' fill='none' viewBox='0 0 14 14' stroke='currentColor' stroke-width='2'><polygon points='7,1 13,13 1,13'/></svg>`,
        'dent': `<svg class='h-5 w-5' fill='none' viewBox='0 0 14 14' stroke='currentColor' stroke-width='2'><circle cx='7' cy='7' r='5'/></svg>`,
        'crack': `<svg class='h-5 w-5' fill='none' viewBox='0 0 14 14' stroke='currentColor' stroke-width='2'><line x1='2' y1='2' x2='12' y2='12'/><line x1='12' y1='2' x2='2' y2='12'/></svg>`
    };
    const mockupPath = "{{ asset('images/mockups') }}";
    const exts = ['jpg', 'jpeg', 'png', 'svg'];
    let idx = 0;

    const tryNext = () => {
        if (idx >= exts.length) {
            if (noImage) {
                noImage.textContent = 'No hay imagen de mockup para este tipo de vehículo.';
                noImage.classList.remove('hidden');
            }
            return;
        }
        const ext = exts[idx++];
        const probe = new Image();
        probe.onload = () => {
            img.src = `${mockupPath}/${bodyType}.${ext}`;
            wrap.classList.remove('hidden');
            if (noImage) noImage.classList.add('hidden');
            damages.forEach(d => {
                const marker = document.createElement('div');
                marker.className = 'absolute flex items-center justify-center';
                marker.style.left = d.pos_x + '%';
                marker.style.top = d.pos_y + '%';
                marker.style.transform = 'translate(-50%, -50%)';
                marker.style.color = colors[d.damage_type] || '#6b7280';
                marker.style.filter = 'drop-shadow(0 0 2px #ffffff) drop-shadow(0 0 4px rgba(255, 255, 255, 0.7))';
                marker.innerHTML = icons[d.damage_type] || '';
                markers.appendChild(marker);
            });
        };
        probe.onerror = tryNext;
        probe.src = `${mockupPath}/${bodyType}.${ext}?t=${Date.now()}`;
    };
    tryNext();
})();
</script>
