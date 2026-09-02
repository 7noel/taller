<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-wrap justify-between items-center gap-3">
            <div class="flex flex-wrap items-center gap-3">
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                    {{ __('Inventario') }}
                </h2>
                @if ($checkIn->document_sn)
                    <x-document-badge :sn="$checkIn->document_sn" />
                @endif
                @if ($checkIn->vehicle?->plate)
                    <span class="text-sm text-gray-500">{{ $checkIn->vehicle->plate }}</span>
                @endif
            </div>
            <div class="flex flex-wrap gap-2">
                @can('view', $checkIn)
                    <a href="{{ route('check-ins.pdf', $checkIn) }}" target="_blank" class="inline-flex items-center px-3 py-1.5 bg-white border border-gray-300 rounded-md font-semibold text-xs text-gray-700 uppercase hover:bg-gray-50">
                        <svg class="h-4 w-4 mr-1.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"/></svg>
                        PDF
                    </a>
                @endcan
                @if ($publicLink)
                    <button type="button" data-whatsapp-open class="inline-flex items-center px-3 py-1.5 bg-white border border-gray-300 rounded-md font-semibold text-xs text-gray-700 uppercase hover:bg-gray-50">
                        <svg class="h-4 w-4 mr-1.5" fill="currentColor" viewBox="0 0 24 24"><path d="M12 2a10 10 0 00-8.6 15.1L2 22l5-1.3A10 10 0 1012 2zm5.2 14.2c-.2.6-1.2 1.2-1.7 1.2-.4.1-1 .1-1.6-.1-.4-.1-.9-.3-1.5-.6-2.6-1.1-4.3-3.8-4.4-4-.1-.2-1.1-1.4-1.1-2.7s.7-1.9.9-2.1c.2-.3.5-.3.7-.3h.5c.2 0 .4-.1.6.4.2.6.7 2 .8 2.1.1.1.1.3 0 .5-.1.2-.1.3-.3.5l-.4.5c-.1.1-.3.3-.1.5.2.3.8 1.3 1.7 2.1 1.2 1.1 2.1 1.4 2.4 1.5.3.1.5.1.7-.1l1-1.2c.2-.3.4-.2.7-.1l2 .9c.3.2.5.3.6.4.1.2.1.7-.1 1.3z"/></svg>
                        WhatsApp
                    </button>
                    <button type="button" data-copy-link="{{ $publicLink }}" title="Copiar enlace del portal del cliente" class="inline-flex items-center px-3 py-1.5 bg-white border border-gray-300 rounded-md font-semibold text-xs text-gray-700 uppercase hover:bg-gray-50">
                        <svg class="h-4 w-4 mr-1.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M13.828 10.172a4 4 0 010 5.656l-3 3a4 4 0 01-5.656-5.656l1.5-1.5m9.314-9.314a4 4 0 00-5.656 0l-1.5 1.5"/></svg>
                        Copiar enlace
                    </button>
                @endif
                @can('crear presupuestos')
                    @if ($checkIn->status === 'approved')
                        <a href="{{ route('estimates.create', ['check_in_id' => $checkIn->id]) }}" class="inline-flex items-center px-3 py-1.5 bg-blue-600 rounded-md font-semibold text-xs text-white uppercase hover:bg-blue-700">
                            <svg class="h-4 w-4 mr-1.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
                            Crear presupuesto
                        </a>
                    @endif
                @endcan
                @can('crear órdenes de trabajo')
                    @php
                        $approvedEstimates = $checkIn->estimates
                            ->whereIn('status', ['approved_insurance', 'approved_client'])
                            ->whereNull('work_order_id');
                    @endphp
                    @if ($approvedEstimates->isNotEmpty())
                        <form method="POST" action="{{ route('work-orders.store') }}">
                            @csrf
                            <input type="hidden" name="check_in_id" value="{{ $checkIn->id }}">
                            <button type="submit" class="inline-flex items-center px-3 py-1.5 bg-blue-600 rounded-md font-semibold text-xs text-white uppercase hover:bg-blue-700">
                                <svg class="h-4 w-4 mr-1.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/></svg>
                                Generar OT
                            </button>
                        </form>
                    @endif
                @endcan
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
                        <form id="form-approve" method="POST" action="{{ route('check-ins.approve', $checkIn) }}">
                            @csrf
                            <button type="button" data-checkin-approve class="inline-flex items-center px-3 py-1.5 bg-green-600 rounded-md font-semibold text-xs text-white uppercase hover:bg-green-700">Aprobar</button>
                        </form>
                    @endif
                @endcan
                @can('reject', \App\Models\CheckIn::class)
                    @if (in_array($checkIn->status, ['draft', 'pending_approval']))
                        <form id="form-reject" method="POST" action="{{ route('check-ins.reject', $checkIn) }}">
                            @csrf
                            <input type="hidden" name="reason">
                            <button type="button" data-checkin-reject class="inline-flex items-center px-3 py-1.5 bg-red-600 rounded-md font-semibold text-xs text-white uppercase hover:bg-red-700">Rechazar</button>
                        </form>
                    @endif
                @endcan
                @can('editar inventarios')
                    @if ($checkIn->status === 'approved')
                        <form method="POST" action="{{ route('check-ins.close', $checkIn) }}" data-confirm="¿Cerrar este inventario? Confirmarás que el vehículo salió del taller.">
                            @csrf
                            <button type="submit" class="inline-flex items-center px-3 py-1.5 bg-gray-600 rounded-md font-semibold text-xs text-white uppercase hover:bg-gray-700">Cerrar inventario</button>
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

    <div class="py-6">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            @if (session('success'))
                <div class="mb-4 px-4 py-3 bg-green-50 border border-green-200 text-green-700 rounded-lg text-sm">{{ session('success') }}</div>
            @endif
            @if (session('error'))
                <div class="mb-4 px-4 py-3 bg-red-50 border border-red-200 text-red-700 rounded-lg text-sm">{{ session('error') }}</div>
            @endif
            @if ($errors->any())
                <div class="mb-4 rounded-lg bg-red-50 border border-red-200 px-4 py-3 text-sm text-red-700">
                    <ul class="list-disc list-inside space-y-1">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <div class="mb-4">
                <span class="inline-flex items-center px-2.5 py-0.5 rounded-md text-sm font-medium {{ $statusColors[$checkIn->status] ?? 'bg-gray-100 text-gray-800' }}">
                    {{ $checkIn->status_label }}
                </span>
                <span class="ml-2 text-sm text-gray-500">{{ $checkIn->service_type_label }}</span>
            </div>

            @if ($checkIn->status === 'rejected' && $checkIn->rejection_reason)
                <div class="mb-4 rounded-lg bg-red-50 border border-red-200 p-4">
                    <div class="flex items-start gap-3">
                        <svg class="h-5 w-5 shrink-0 text-red-600 mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z"/></svg>
                        <div>
                            <p class="text-sm font-semibold text-red-700">Inventario rechazado</p>
                            <p class="mt-1 text-sm text-red-700">{{ $checkIn->rejection_reason }}</p>
                            <p class="mt-2 text-xs text-red-600/80">{{ $checkIn->rejected_by_label }}@if ($checkIn->rejected_at) · {{ $checkIn->rejected_at->format('d/m/Y H:i') }}@endif</p>
                        </div>
                    </div>
                </div>
            @endif

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
                        <h3 class="text-lg font-semibold text-gray-800 mb-4">Vehículo</h3>
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

                        <h3 class="text-lg font-semibold text-gray-800 mt-6 mb-4">Propietario</h3>
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

                        <h3 class="text-lg font-semibold text-gray-800 mt-6 mb-4">Datos de ingreso</h3>
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
                        <h3 class="text-lg font-semibold text-gray-800 mb-4">Checklist del vehículo</h3>
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
                        <h3 class="text-lg font-semibold text-gray-800 mb-4">Daños registrados</h3>

                        @php
                            $hasWithCoords = $checkIn->damages->contains(fn ($d) => $d->pos_x !== null && $d->pos_y !== null);
                        @endphp

                        @if ($checkIn->damages->isEmpty())
                            <p class="text-sm text-gray-500">No se registraron daños.</p>
                        @else
                            {{-- Mockup con marcadores pintados --}}
                            <div class="bg-gray-50 border border-gray-200 rounded-lg p-4 mb-6">
                                <div id="show-damage-mockup-wrap" class="relative inline-block max-w-full hidden">
                                    <img id="show-damage-mockup" src="data:image/gif;base64,R0lGODlhAQABAAAAACH5BAEKAAEALAAAAAABAAEAAAICTAEAOw==" alt="Mockup del vehículo" class="max-w-full h-auto rounded-lg border border-gray-200">
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
                        <h3 class="text-lg font-semibold text-gray-800 mb-4">Fotos del vehículo</h3>
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

            {{-- Historial de estados --}}
            <x-status-history :subject="$checkIn" />
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

    @include('partials.checkin-approval-modal')

    @include('partials.whatsapp-modal')

    @push('scripts')
    @include('partials.whatsapp-modal-scripts', [
        'actionUrl' => $actionUrl ?? '',
        'recipientsUrl' => $recipientsUrl ?? '',
        'initialMessage' => $initialMessage ?? '',
        'defaultRecipientPhone' => $recipient['contact_phone'] ?? '',
    ])
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

        const colors = { 'scratch': '#008000', 'dent': '#ff0000', 'crack': '#0000ff' };
        const icons = {
            'scratch': `<svg class='h-5 w-5' fill='none' viewBox='0 0 14 14' stroke='currentColor' stroke-width='2'><polygon points='7,1 13,13 1,13'/></svg>`,
            'dent': `<svg class='h-5 w-5' fill='none' viewBox='0 0 14 14' stroke='currentColor' stroke-width='2'><circle cx='7' cy='7' r='5'/></svg>`,
            'crack': `<svg class='h-5 w-5' fill='none' viewBox='0 0 14 14' stroke='currentColor' stroke-width='2'><line x1='2' y1='2' x2='12' y2='12'/><line x1='12' y1='2' x2='2' y2='12'/></svg>`
        };
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
