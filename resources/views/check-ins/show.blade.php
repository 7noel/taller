<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-wrap justify-between items-center gap-3">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Inventario #') }}{{ $checkIn->id }} — {{ $checkIn->vehicle?->plate }}
            </h2>
            <div class="flex flex-wrap gap-2">
                @can('editar inventarios')
                    <a href="{{ route('check-ins.edit', $checkIn) }}" class="inline-flex items-center px-3 py-1.5 bg-yellow-500 rounded-md font-semibold text-xs text-white uppercase hover:bg-yellow-600">Editar</a>
                @endcan
                @can('sendToClient', $checkIn)
                    @if (in_array($checkIn->status, ['draft', 'rejected']))
                        <form method="POST" action="{{ route('check-ins.send-to-client', $checkIn) }}">
                            @csrf
                            <button type="submit" class="inline-flex items-center px-3 py-1.5 bg-blue-600 rounded-md font-semibold text-xs text-white uppercase hover:bg-blue-700">Enviar a cliente</button>
                        </form>
                    @endif
                @endcan
                @can('approve', \App\Models\CheckIn::class)
                    @if (in_array($checkIn->status, ['draft', 'pending_approval', 'rejected']))
                        <form method="POST" action="{{ route('check-ins.approve', $checkIn) }}">
                            @csrf
                            <button type="submit" class="inline-flex items-center px-3 py-1.5 bg-green-600 rounded-md font-semibold text-xs text-white uppercase hover:bg-green-700">Aprobar</button>
                        </form>
                    @endif
                @endcan
                @can('reject', \App\Models\CheckIn::class)
                    @if (in_array($checkIn->status, ['draft', 'pending_approval']))
                        <form method="POST" action="{{ route('check-ins.reject', $checkIn) }}" onsubmit="const r = prompt('Motivo de rechazo (opcional):'); if (r !== null) { const i = document.createElement('input'); i.type='hidden'; i.name='reason'; i.value=r; this.appendChild(i); return true; } return false;">
                            @csrf
                            <button type="submit" class="inline-flex items-center px-3 py-1.5 bg-red-600 rounded-md font-semibold text-xs text-white uppercase hover:bg-red-700">Rechazar</button>
                        </form>
                    @endif
                @endcan
            </div>
        </div>
    </x-slot>

    @php
        $statusColors = [
            'draft' => 'bg-gray-100 text-gray-800',
            'pending_approval' => 'bg-yellow-100 text-yellow-800',
            'approved' => 'bg-green-100 text-green-800',
            'rejected' => 'bg-red-100 text-red-800',
            'closed' => 'bg-blue-100 text-blue-800',
        ];
    @endphp

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            @if (session('success'))
                <div class="mb-4 px-4 py-3 bg-green-100 border border-green-400 text-green-700 rounded">{{ session('success') }}</div>
            @endif

            <div class="mb-4">
                <span class="inline-flex items-center px-2.5 py-0.5 rounded-md text-sm font-medium {{ $statusColors[$checkIn->status] ?? 'bg-gray-100 text-gray-800' }}">
                    {{ $checkIn->status_label }}
                </span>
                <span class="ml-2 text-sm text-gray-500">{{ $checkIn->service_type_label }}</span>
            </div>

            {{-- Tabs --}}
            <div class="mb-4 border-b border-gray-200">
                <nav class="flex flex-wrap gap-1">
                    <button class="show-tab-btn px-3 py-2 text-sm font-medium text-blue-600 border-b-2 border-blue-500" data-tab="general">General</button>
                    <button class="show-tab-btn px-3 py-2 text-sm font-medium text-gray-500 border-b-2 border-transparent hover:text-gray-700" data-tab="checklist">Checklist</button>
                    <button class="show-tab-btn px-3 py-2 text-sm font-medium text-gray-500 border-b-2 border-transparent hover:text-gray-700" data-tab="damages">Daños</button>
                    <button class="show-tab-btn px-3 py-2 text-sm font-medium text-gray-500 border-b-2 border-transparent hover:text-gray-700" data-tab="photos">Fotos</button>
                </nav>
            </div>

            {{-- Tab General --}}
            <div id="tab-general" class="show-tab-content">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 text-gray-900">
                        <h3 class="text-lg font-semibold text-gray-800 mb-4">🚗 Vehículo</h3>
                        <div class="grid grid-cols-2 md:grid-cols-4 gap-4 text-sm">
                            <div>
                                <p class="text-xs text-gray-500">Placa</p>
                                <p class="font-medium">{{ $checkIn->vehicle?->plate ?? '-' }}</p>
                            </div>
                            <div>
                                <p class="text-xs text-gray-500">Marca / Modelo</p>
                                <p class="font-medium">{{ $checkIn->vehicle?->vehicleModel?->brand?->name }} {{ $checkIn->vehicle?->vehicleModel?->name }}</p>
                            </div>
                            <div>
                                <p class="text-xs text-gray-500">Año</p>
                                <p class="font-medium">{{ $checkIn->vehicle?->year ?? '-' }}</p>
                            </div>
                            <div>
                                <p class="text-xs text-gray-500">Color</p>
                                <p class="font-medium">{{ $checkIn->vehicle?->color ?? '-' }}</p>
                            </div>
                            <div>
                                <p class="text-xs text-gray-500">VIN</p>
                                <p class="font-medium">{{ $checkIn->vehicle?->vin ?? '-' }}</p>
                            </div>
                            <div>
                                <p class="text-xs text-gray-500">Carrocería</p>
                                <p class="font-medium">{{ $checkIn->vehicle?->body_type ?? '-' }}</p>
                            </div>
                        </div>

                        <h3 class="text-lg font-semibold text-gray-800 mt-6 mb-4">👤 Propietario</h3>
                        <div class="grid grid-cols-2 md:grid-cols-4 gap-4 text-sm">
                            <div>
                                <p class="text-xs text-gray-500">Nombre</p>
                                <p class="font-medium">{{ $checkIn->client?->display_name ?? '-' }}</p>
                            </div>
                            <div>
                                <p class="text-xs text-gray-500">Documento</p>
                                <p class="font-medium">{{ $checkIn->client?->document_type_label }} {{ $checkIn->client?->document_number }}</p>
                            </div>
                            <div>
                                <p class="text-xs text-gray-500">Teléfono</p>
                                <p class="font-medium">{{ $checkIn->client?->phone ?? $checkIn->client?->mobile ?? '-' }}</p>
                            </div>
                            <div>
                                <p class="text-xs text-gray-500">Email</p>
                                <p class="font-medium">{{ $checkIn->client?->email ?? '-' }}</p>
                            </div>
                        </div>

                        <h3 class="text-lg font-semibold text-gray-800 mt-6 mb-4">📋 Datos de ingreso</h3>
                        <div class="grid grid-cols-2 md:grid-cols-4 gap-4 text-sm">
                            <div>
                                <p class="text-xs text-gray-500">Servicio</p>
                                <p class="font-medium">{{ $checkIn->service_type_label }}</p>
                            </div>
                            <div>
                                <p class="text-xs text-gray-500">Nº Siniestro</p>
                                <p class="font-medium">{{ $checkIn->claim_number ?? '-' }}</p>
                            </div>
                            <div>
                                <p class="text-xs text-gray-500">Aseguradora</p>
                                <p class="font-medium">{{ $checkIn->insuranceCompany?->display_name ?? '-' }}</p>
                            </div>
                            <div>
                                <p class="text-xs text-gray-500">Kilometraje</p>
                                <p class="font-medium">{{ number_format((int) $checkIn->mileage) }} km</p>
                            </div>
                            <div>
                                <p class="text-xs text-gray-500">Combustible</p>
                                <p class="font-medium">{{ $checkIn->fuel_level_label }}</p>
                            </div>
                            <div>
                                <p class="text-xs text-gray-500">Tarjeta de Propiedad</p>
                                <p class="font-medium">{{ $checkIn->property_card_label }}</p>
                            </div>
                            <div>
                                <p class="text-xs text-gray-500">Fecha SOAT</p>
                                <p class="font-medium">{{ $checkIn->soat_expiration?->format('d/m/Y') ?? '-' }}</p>
                            </div>
                            <div>
                                <p class="text-xs text-gray-500">Revisión Técnica</p>
                                <p class="font-medium">{{ $checkIn->technical_review_expiration?->format('d/m/Y') ?? '-' }}</p>
                            </div>
                            <div>
                                <p class="text-xs text-gray-500">Llaves</p>
                                <p class="font-medium">{{ $checkIn->keys_count }}</p>
                            </div>
                            <div>
                                <p class="text-xs text-gray-500">Control remoto</p>
                                <p class="font-medium">{{ $checkIn->has_remote_control ? 'Sí' : 'No' }}</p>
                            </div>
                        </div>

                        @if ($checkIn->client_request || $checkIn->observations)
                            <div class="mt-6 grid grid-cols-1 md:grid-cols-2 gap-4">
                                @if ($checkIn->client_request)
                                    <div>
                                        <p class="text-xs text-gray-500">Solicitud del cliente</p>
                                        <p class="mt-1 text-sm whitespace-pre-line">{{ $checkIn->client_request }}</p>
                                    </div>
                                @endif
                                @if ($checkIn->observations)
                                    <div>
                                        <p class="text-xs text-gray-500">Observaciones</p>
                                        <p class="mt-1 text-sm whitespace-pre-line">{{ $checkIn->observations }}</p>
                                    </div>
                                @endif
                            </div>
                        @endif

                        <div class="mt-6 pt-4 border-t border-gray-100 grid grid-cols-1 md:grid-cols-3 gap-2 text-xs text-gray-500">
                            <div>Establecimiento: <span class="font-medium text-gray-700">{{ $checkIn->establishment?->name ?? '-' }}</span></div>
                            <div>Creado por: <span class="font-medium text-gray-700">{{ $checkIn->creator?->name ?? '-' }}</span></div>
                            <div>Fecha: <span class="font-medium text-gray-700">{{ $checkIn->created_at?->format('d/m/Y H:i') }}</span></div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Tab Checklist --}}
            <div id="tab-checklist" class="show-tab-content hidden">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 text-gray-900">
                        <h3 class="text-lg font-semibold text-gray-800 mb-4">✅ Checklist del vehículo</h3>
                        @if ($checkIn->checklistResults->isEmpty())
                            <p class="text-sm text-gray-500">No se registraron resultados de checklist.</p>
                        @else
                            <div class="overflow-x-auto">
                                <table class="min-w-full divide-y divide-gray-200 text-sm">
                                    <thead>
                                        <tr class="bg-gray-50">
                                            <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase">Ítem</th>
                                            <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase">Estado</th>
                                            <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase">Observaciones</th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-gray-200">
                                        @foreach ($checkIn->checklistResults->sortBy('checklistItem.order') as $result)
                                            @php
                                                $statusColors = [
                                                    'good' => 'bg-green-100 text-green-800',
                                                    'regular' => 'bg-yellow-100 text-yellow-800',
                                                    'bad' => 'bg-red-100 text-red-800',
                                                    'not_applicable' => 'bg-gray-100 text-gray-600',
                                                ];
                                            @endphp
                                            <tr>
                                                <td class="px-3 py-2 font-medium">{{ $result->checklistItem?->name }}</td>
                                                <td class="px-3 py-2">
                                                    <span class="inline-flex items-center px-2 py-0.5 rounded-md text-xs font-medium {{ $statusColors[$result->status] ?? 'bg-gray-100' }}">
                                                        {{ $result->status_label }}
                                                    </span>
                                                </td>
                                                <td class="px-3 py-2 text-gray-600">{{ $result->observations ?? '-' }}</td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            {{-- Tab Daños --}}
            <div id="tab-damages" class="show-tab-content hidden">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 text-gray-900">
                        <h3 class="text-lg font-semibold text-gray-800 mb-4">🔧 Daños registrados</h3>

                        @php
                            $hasWithCoords = $checkIn->damages->contains(fn ($d) => $d->pos_x !== null && $d->pos_y !== null);
                        @endphp

                        @if ($checkIn->damages->isEmpty())
                            <p class="text-sm text-gray-500">No se registraron daños.</p>
                        @else
                            {{-- Mockup con marcadores pintados --}}
                            <div class="bg-gray-50 border border-gray-200 rounded-lg p-4 mb-6">
                                <div id="show-damage-mockup-wrap" class="relative inline-block max-w-full hidden">
                                    <img id="show-damage-mockup" src="" alt="Mockup del vehículo" class="max-w-full h-auto rounded-lg border border-gray-200">
                                    <div id="show-damage-markers" class="absolute inset-0 pointer-events-none"></div>
                                </div>
                                <p id="show-damage-no-image" class="text-sm text-gray-500 {{ $hasWithCoords ? 'hidden' : '' }}">
                                    @if ($hasWithCoords)
                                        No hay imagen de mockup para este tipo de vehículo.
                                    @else
                                        Los daños registrados no tienen coordenadas para pintar.
                                    @endif
                                </p>
                            </div>

                            {{-- Lista de daños --}}
                            <div class="flex flex-wrap gap-4">
                                @foreach ($checkIn->damages as $damage)
                                    <div class="bg-gray-50 border border-gray-200 rounded-lg p-4">
                                        <span class="inline-flex items-center px-2 py-0.5 rounded-md text-xs font-medium
                                            {{ $damage->damage_type === 'scratch' ? 'bg-green-100 text-green-800' : ($damage->damage_type === 'dent' ? 'bg-red-100 text-red-800' : 'bg-blue-100 text-blue-800') }}">
                                            {{ $damage->damage_type_label }}
                                        </span>
                                        @if ($damage->pos_x !== null && $damage->pos_y !== null)
                                            <p class="text-xs text-gray-600"><strong>Posición:</strong> X: {{ $damage->pos_x }}% Y: {{ $damage->pos_y }}%</p>
                                        @endif
                                        @if ($damage->notes)
                                            <p class="mt-1 text-xs text-gray-600"><strong>Nota:</strong> {{ $damage->notes }}</p>
                                        @endif
                                    </div>
                                @endforeach
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            {{-- Tab Fotos --}}
            <div id="tab-photos" class="show-tab-content hidden">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 text-gray-900">
                        <h3 class="text-lg font-semibold text-gray-800 mb-4">📷 Fotos del vehículo</h3>
                        @if ($checkIn->photos->isEmpty())
                            <p class="text-sm text-gray-500">No se registraron fotos.</p>
                        @else
                            <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                                @foreach ($checkIn->photos as $photo)
                                    <a href="{{ $photo->url }}" target="_blank" class="block">
                                        <img src="{{ $photo->url }}" class="w-full h-32 object-cover rounded-lg border border-gray-200" alt="Foto del vehículo">
                                        @if ($photo->description)
                                            <p class="mt-1 text-xs text-gray-500">{{ $photo->description }}</p>
                                        @endif
                                    </a>
                                @endforeach
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    @php
        $damagesWithCoords = $checkIn->damages
            ->filter(fn ($d) => $d->pos_x !== null && $d->pos_y !== null)
            ->map(fn ($d) => [
                'damage_type' => $d->damage_type,
                'pos_x' => $d->pos_x,
                'pos_y' => $d->pos_y,
            ])
            ->values();
    @endphp

    @push('scripts')
    <script>
    // ===== Mockup de daños pintado en el detalle =====
    (function () {
        const damages = @json($damagesWithCoords);

        const bodyType = "{{ $checkIn->vehicle?->body_type }}";
        const mockupPath = "{{ asset('images/mockups') }}";
        const wrap = document.getElementById('show-damage-mockup-wrap');
        const img = document.getElementById('show-damage-mockup');
        const markers = document.getElementById('show-damage-markers');
        const noImage = document.getElementById('show-damage-no-image');

        if (!bodyType || damages.length === 0) {
            if (noImage && !bodyType) {
                noImage.textContent = 'No hay imagen de mockup para este tipo de vehículo.';
                noImage.classList.remove('hidden');
            }
            return;
        }

        const colors = { 'scratch': '#10b981', 'dent': '#ef4444', 'crack': '#3b82f6' };
        const icons = { 'scratch': '✕', 'dent': '●', 'crack': '▲' };
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
                    marker.className = 'absolute rounded-full flex items-center justify-center text-white text-xs font-bold';
                    marker.style.left = d.pos_x + '%';
                    marker.style.top = d.pos_y + '%';
                    marker.style.transform = 'translate(-50%, -50%)';
                    marker.style.width = '22px';
                    marker.style.height = '22px';
                    marker.style.background = colors[d.damage_type] || '#6b7280';
                    marker.textContent = icons[d.damage_type] || '•';
                    markers.appendChild(marker);
                });
            };
            probe.onerror = tryNext;
            probe.src = `${mockupPath}/${bodyType}.${ext}?t=${Date.now()}`;
        };
        tryNext();
    })();

    document.querySelectorAll('.show-tab-btn').forEach(btn => {
        btn.addEventListener('click', function () {
            document.querySelectorAll('.show-tab-btn').forEach(b => {
                b.classList.remove('text-blue-600', 'border-blue-500');
                b.classList.add('text-gray-500', 'border-transparent');
            });
            this.classList.add('text-blue-600', 'border-blue-500');
            this.classList.remove('text-gray-500', 'border-transparent');

            document.querySelectorAll('.show-tab-content').forEach(tab => tab.classList.add('hidden'));
            document.getElementById('tab-' + this.dataset.tab).classList.remove('hidden');
        });
    });
    </script>
    @endpush
</x-app-layout>