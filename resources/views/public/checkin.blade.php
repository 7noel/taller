<x-public-layout>
    <div class="max-w-6xl mx-auto px-4 py-6 sm:px-6 lg:px-8">

        {{-- Barra superior --}}
        <div class="flex items-center justify-between gap-3 mb-5">
            <a href="{{ route('public.portal', $vehicle->access_token) }}" class="inline-flex items-center gap-1.5 text-sm font-medium text-blue-600 hover:text-blue-700 transition-colors duration-150">
                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                Mis servicios
            </a>
            <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold {{ $checkIn->status === 'pending_approval' ? 'bg-amber-50 text-amber-700' : 'bg-blue-50 text-blue-700' }}">
                {{ $checkIn->status_label }}
            </span>
        </div>

        @if (session('success'))
            <div class="mb-4 px-4 py-3 bg-green-50 border border-green-200 text-green-700 rounded-lg text-sm">{{ session('success') }}</div>
        @endif
        @if ($errors->any())
            <div class="mb-4 px-4 py-3 bg-red-50 border border-red-200 text-red-700 rounded-lg text-sm">{{ $errors->first() }}</div>
        @endif

        {{-- Stepper del flujo --}}
        <div class="mb-5">
            @include('partials.flow-stepper', ['activeIndex' => 0])
        </div>

        <div class="lg:grid lg:grid-cols-3 lg:gap-6">
            <div class="lg:col-span-2 min-w-0">

        {{-- Hero del vehículo --}}
        <div class="card mb-4 overflow-hidden">
            <div class="p-5 border-b border-gray-100 flex items-center justify-between gap-3">
                <div>
                    <div class="text-2xl font-bold text-gray-900 tracking-wider">{{ $vehicle->plate }}</div>
                    <div class="text-sm text-gray-500 mt-0.5">
                        {{ $vehicle->vehicleModel?->brand?->name ?? '—' }} {{ $vehicle->vehicleModel?->name ?? '' }}
                        @if ($vehicle->year) · {{ $vehicle->year }} @endif
                        @if ($vehicle->color) · {{ $vehicle->color }} @endif
                    </div>
                </div>
                @if ($checkIn->document_sn)
                    <x-document-badge :sn="$checkIn->document_sn" />
                @endif
            </div>
            <dl class="grid grid-cols-2 sm:grid-cols-3 gap-x-4 gap-y-4 p-5 text-sm">
                <div>
                    <dt class="text-xs text-gray-500">VIN</dt>
                    <dd class="font-medium text-gray-900 mt-0.5 break-all">{{ $vehicle->vin ?? '—' }}</dd>
                </div>
                <div>
                    <dt class="text-xs text-gray-500">Carrocería</dt>
                    <dd class="font-medium text-gray-900 mt-0.5">{{ $vehicle->body_type ? ucfirst($vehicle->body_type) : '—' }}</dd>
                </div>
                <div>
                    <dt class="text-xs text-gray-500">Propietario</dt>
                    <dd class="font-medium text-gray-900 mt-0.5">{{ $checkIn->client?->display_name ?? '—' }}</dd>
                </div>
                <div class="sm:col-span-2">
                    <dt class="text-xs text-gray-500">Documento del propietario</dt>
                    <dd class="font-medium text-gray-900 mt-0.5">{{ $checkIn->client?->document_type_label ?? '' }} {{ $checkIn->client?->document_number ?? '—' }}</dd>
                </div>
            </dl>
        </div>

        @if ($checkIn->status === 'pending_approval')
            <div class="mb-4 px-4 py-3 bg-blue-50 border border-blue-200 text-blue-800 rounded-lg text-sm">
                Tu vehículo está en <strong>ingreso</strong>. Revisa los datos y confirma si estás de acuerdo para continuar con el presupuesto.
            </div>
        @endif

        {{-- Datos del servicio --}}
        <div class="card mb-4">
            <div class="p-5">
                <h2 class="text-sm font-semibold text-gray-700 uppercase tracking-wider mb-4">Datos del servicio</h2>
                <dl class="grid grid-cols-2 sm:grid-cols-3 gap-x-4 gap-y-4 text-sm">
                    <div>
                        <dt class="text-xs text-gray-500">Tipo de servicio</dt>
                        <dd class="font-medium text-gray-900 mt-0.5">{{ $checkIn->service_type_label }}</dd>
                    </div>
                    <div>
                        <dt class="text-xs text-gray-500">Nº siniestro</dt>
                        <dd class="font-medium text-gray-900 mt-0.5">{{ $checkIn->claim_number ?? '—' }}</dd>
                    </div>
                    <div class="col-span-2 sm:col-span-3">
                        <dt class="text-xs text-gray-500">Aseguradora</dt>
                        <dd class="font-medium text-gray-900 mt-0.5">{{ $checkIn->insuranceCompany?->display_name ?? '—' }}</dd>
                    </div>
                    <div>
                        <dt class="text-xs text-gray-500">Kilometraje</dt>
                        <dd class="font-medium text-gray-900 mt-0.5">{{ $checkIn->mileage ? number_format($checkIn->mileage) . ' km' : '—' }}</dd>
                    </div>
                    <div>
                        <dt class="text-xs text-gray-500">Combustible</dt>
                        <dd class="font-medium text-gray-900 mt-0.5">{{ $checkIn->fuel_level_label }}</dd>
                    </div>
                    <div>
                        <dt class="text-xs text-gray-500">Tarjeta de propiedad</dt>
                        <dd class="font-medium text-gray-900 mt-0.5">{{ $checkIn->property_card_label }}</dd>
                    </div>
                    <div>
                        <dt class="text-xs text-gray-500">Llaves</dt>
                        <dd class="font-medium text-gray-900 mt-0.5">{{ $checkIn->keys_count }}</dd>
                    </div>
                    <div>
                        <dt class="text-xs text-gray-500">Control remoto</dt>
                        <dd class="font-medium text-gray-900 mt-0.5">{{ $checkIn->has_remote_control ? 'Sí' : 'No' }}</dd>
                    </div>
                    <div>
                        <dt class="text-xs text-gray-500">Vencimiento SOAT</dt>
                        <dd class="font-medium text-gray-900 mt-0.5">{{ $checkIn->soat_expiration?->format('d/m/Y') ?? '—' }}</dd>
                    </div>
                    <div>
                        <dt class="text-xs text-gray-500">Revisión técnica</dt>
                        <dd class="font-medium text-gray-900 mt-0.5">{{ $checkIn->technical_review_expiration?->format('d/m/Y') ?? '—' }}</dd>
                    </div>
                </dl>
            </div>
        </div>

        @if ($checkIn->client_request || $checkIn->observations)
            <div class="card mb-4">
                <div class="p-5 space-y-4 text-sm">
                    @if ($checkIn->client_request)
                        <div>
                            <p class="text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1">Tu solicitud</p>
                            <p class="text-gray-800 whitespace-pre-line">{{ $checkIn->client_request }}</p>
                        </div>
                    @endif
                    @if ($checkIn->observations)
                        <div>
                            <p class="text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1">Observaciones</p>
                            <p class="text-gray-800 whitespace-pre-line">{{ $checkIn->observations }}</p>
                        </div>
                    @endif
                </div>
            </div>
        @endif


        {{-- Checklist del estado al ingreso --}}
        @if ($checkIn->checklistResults->isNotEmpty())
            <div class="card mb-4">
                <div class="p-5">
                    <h2 class="text-sm font-semibold text-gray-700 uppercase tracking-wider mb-4">Estado del vehículo al ingreso</h2>
                    <ul class="divide-y divide-gray-100">
                        @foreach ($checkIn->checklistResults->sortBy('checklistItem.order') as $result)
                            <li class="py-2.5 first:pt-0 last:pb-0 flex items-start gap-3">
                                <span class="mt-1.5 h-2.5 w-2.5 rounded-full shrink-0
                                    {{ $result->status === 'good' ? 'bg-green-500' : ($result->status === 'regular' ? 'bg-amber-500' : ($result->status === 'bad' ? 'bg-red-500' : 'bg-gray-400')) }}"></span>
                                <div class="flex-1 min-w-0">
                                    <div class="flex items-center justify-between gap-2">
                                        <p class="font-medium text-gray-800 text-sm">{{ $result->checklistItem?->name }}</p>
                                        <span class="inline-flex items-center px-2 py-0.5 rounded-md text-xs font-medium shrink-0
                                            {{ $result->status === 'good' ? 'bg-green-50 text-green-700' : ($result->status === 'regular' ? 'bg-amber-50 text-amber-700' : ($result->status === 'bad' ? 'bg-red-50 text-red-700' : 'bg-gray-100 text-gray-600')) }}">
                                            {{ $result->status_label }}
                                        </span>
                                    </div>
                                    @if ($result->observations)
                                        <p class="mt-0.5 text-xs text-gray-500">{{ $result->observations }}</p>
                                    @endif
                                </div>
                            </li>
                        @endforeach
                    </ul>
                </div>
            </div>
        @endif


        {{-- Daños --}}
        @if ($checkIn->damages->isNotEmpty())
            <div class="card mb-4">
                <div class="p-5">
                    <h2 class="text-sm font-semibold text-gray-700 uppercase tracking-wider mb-1">Daños registrados</h2>
                    <p class="text-xs text-gray-500 mb-4">Marcados sobre el vehículo al momento del ingreso.</p>

                    <div class="bg-gray-50 border border-gray-200 rounded-lg p-4 flex justify-center">
                        @include('partials.damage-mockup', [
                            'damagesWithCoords' => $damagesWithCoords,
                            'bodyType' => $vehicle->body_type ?? '',
                        ])
                    </div>

                    <ul class="mt-4 grid gap-2 sm:grid-cols-2">
                        @foreach ($checkIn->damages as $damage)
                            <li class="flex items-start gap-2.5 bg-gray-50 border border-gray-200 rounded-lg p-3">
                                <span class="mt-0.5 flex h-2.5 w-2.5 rounded-full shrink-0
                                    {{ $damage->damage_type === 'scratch' ? 'bg-green-500' : ($damage->damage_type === 'dent' ? 'bg-red-500' : 'bg-blue-500') }}"></span>
                                <div class="min-w-0">
                                    <p class="font-medium text-gray-800 text-sm">{{ $damage->damage_type_label }} <span class="text-gray-400">·</span> <span class="text-gray-500 font-normal">{{ $damage->side_label }}</span></p>
                                    @if ($damage->notes)
                                        <p class="text-xs text-gray-500 mt-0.5">{{ $damage->notes }}</p>
                                    @endif
                                </div>
                            </li>
                        @endforeach
                    </ul>
                </div>
            </div>
        @endif


        {{-- Fotos --}}
        @if ($checkIn->photos->isNotEmpty())
            <div class="card mb-4">
                <div class="p-5">
                    <h2 class="text-sm font-semibold text-gray-700 uppercase tracking-wider mb-4">Fotos</h2>
                    <div class="grid grid-cols-2 sm:grid-cols-3 gap-3">
                        @foreach ($checkIn->photos as $photo)
                            <button type="button" data-lightbox="{{ $photo->url }}" class="block text-left group focus:outline-none focus-visible:ring-2 focus-visible:ring-blue-500 rounded-lg">
                                <img src="{{ $photo->url }}" alt="Foto del inventario" class="w-full h-32 object-cover rounded-lg border border-gray-200 transition-transform duration-150 group-hover:scale-[1.02] group-active:scale-[0.99]">
                                @if ($photo->description)
                                    <p class="mt-1 text-xs text-gray-500">{{ $photo->description }}</p>
                                @endif
                            </button>
                        @endforeach
                    </div>
                </div>
            </div>

            {{-- Lightbox --}}
            <div id="lightbox" class="hidden fixed inset-0 z-50 items-center justify-center p-4 bg-black/80" aria-hidden="true">
                <button type="button" data-lightbox-close class="absolute top-4 right-4 rounded-full bg-white/10 p-2 text-white hover:bg-white/20 transition-colors duration-150" aria-label="Cerrar imagen">
                    <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
                <img id="lightbox-img" src="" alt="Foto ampliada" class="max-h-[85vh] max-w-full rounded-lg shadow-2xl">
            </div>
        @endif


            </div>

            <aside class="mt-6 lg:mt-0">
                <div class="lg:sticky lg:top-6 space-y-4">

        {{-- Aprobar / Rechazar --}}
        @if ($checkIn->status === 'pending_approval')
            <div class="card border-2 border-blue-200">
                <div class="p-5">
                    <h2 class="text-base font-semibold text-gray-800 mb-1">¿Estás de acuerdo con el inventario?</h2>
                    <p class="text-sm text-gray-500 mb-4">Si hay algo que corregir, indícalo y lo revisaremos antes de continuar.</p>

                    <form id="form-approve" method="POST" action="{{ route('public.portal.check-in.approve', [$vehicle->access_token, $checkIn]) }}">@csrf</form>
                    <form id="form-reject" method="POST" action="{{ route('public.portal.check-in.reject', [$vehicle->access_token, $checkIn]) }}">@csrf</form>

                    <textarea id="reject-reason" name="reason" form="form-reject" rows="2" required placeholder="Si no estás de acuerdo, indica qué corregir..." class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"></textarea>
                    <p id="reject-reason-error" class="hidden mt-1 text-xs text-red-600">Indica el motivo del rechazo para poder continuar.</p>

                    <div class="mt-3 grid gap-3 sm:grid-cols-2">
                        <button type="button" data-approve-open class="btn btn-primary w-full justify-center py-2.5">
                            <svg class="h-4 w-4 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                            Sí, estoy de acuerdo
                        </button>
                        <button type="button" data-reject-open class="btn btn-danger w-full justify-center py-2.5">No estoy de acuerdo</button>
                    </div>
                </div>
            </div>
        @elseif ($checkIn->rejection_reason)
            <div class="card">
                <div class="p-5">
                    <div class="rounded-lg bg-red-50 border border-red-200 text-red-700 p-4">
                        <p class="font-medium text-sm">Motivo del rechazo</p>
                        <p class="text-sm mt-1">{{ $checkIn->rejection_reason }}</p>
                        <p class="text-xs mt-2 text-red-600/80">El taller está revisando tu observación.</p>
                    </div>
                </div>
            </div>
        @endif

        {{-- Datos rápidos --}}
        <div class="card">
            <div class="p-5 text-sm">
                <h3 class="text-xs font-semibold text-gray-500 uppercase tracking-wider mb-3">Datos del servicio</h3>
                <dl class="space-y-2">
                    <div class="flex justify-between gap-3"><dt class="text-gray-500">Fecha de ingreso</dt><dd class="font-medium text-gray-900">{{ $checkIn->created_at?->format('d/m/Y') }}</dd></div>
                    <div class="flex justify-between gap-3"><dt class="text-gray-500">Documento</dt><dd class="font-medium text-gray-900">{{ $checkIn->document_sn ?? '—' }}</dd></div>
                    <div class="flex justify-between gap-3"><dt class="text-gray-500">Establecimiento</dt><dd class="font-medium text-gray-900 text-right">{{ $checkIn->establishment?->name ?? '—' }}</dd></div>
                </dl>
            </div>
        </div>

                </div>
            </aside>
        </div>

        @include('partials.approval-confirm-modal', ['entityName' => 'inventario'])

        <p class="mt-8 text-center text-xs text-gray-400">¿Dudas? Contáctanos por WhatsApp o en nuestras instalaciones.</p>
    </div>

    @push('scripts')
    <script>
    // Lightbox de fotos (vanilla JS)
    (function () {
        const lightbox = document.getElementById('lightbox');
        if (!lightbox) return;
        const lightboxImg = document.getElementById('lightbox-img');

        document.querySelectorAll('[data-lightbox]').forEach(function (btn) {
            btn.addEventListener('click', function () {
                lightboxImg.src = this.dataset.lightbox;
                lightbox.classList.remove('hidden');
                lightbox.classList.add('flex');
                lightbox.setAttribute('aria-hidden', 'false');
                document.body.style.overflow = 'hidden';
            });
        });

        function closeLightbox() {
            lightbox.classList.add('hidden');
            lightbox.classList.remove('flex');
            lightbox.setAttribute('aria-hidden', 'true');
            document.body.style.overflow = '';
        }

        document.querySelectorAll('[data-lightbox-close]').forEach(function (el) {
            el.addEventListener('click', closeLightbox);
        });
        lightbox.addEventListener('click', function (e) {
            if (e.target === lightbox) closeLightbox();
        });
        document.addEventListener('keydown', function (e) {
            if (e.key === 'Escape') closeLightbox();
        });
    })();
    </script>
    @endpush
</x-public-layout>

