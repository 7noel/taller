<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Vehículo ') . $vehicle->plate }}
            </h2>
            <div class="flex gap-2">
                <a href="{{ route('vehicles.edit', $vehicle) }}"
                   class="inline-flex items-center px-4 py-2 bg-yellow-500 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-yellow-600 focus:outline-none focus:ring-2 focus:ring-yellow-500 focus:ring-offset-2 transition ease-in-out duration-150">
                    Editar
                </a>
                <form method="POST" action="{{ route('vehicles.destroy', $vehicle) }}" data-confirm="¿Eliminar este vehículo?">
                    @csrf
                    @method('DELETE')
                    <button type="submit"
                            class="inline-flex items-center px-4 py-2 bg-red-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2 transition ease-in-out duration-150">
                        Eliminar
                    </button>
                </form>
            </div>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <h3 class="text-lg font-semibold mb-4">Datos del vehículo</h3>
                            <dl class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                <div>
                                    <dt class="text-sm font-medium text-gray-500">Placa</dt>
                                    <dd class="mt-1 text-sm text-gray-900">{{ $vehicle->plate }}</dd>
                                </div>
                                <div>
                                    <dt class="text-sm font-medium text-gray-500">Marca</dt>
                                    <dd class="mt-1 text-sm text-gray-900">{{ $vehicle->vehicleModel?->brand?->name ?? '—' }}</dd>
                                </div>
                                <div>
                                    <dt class="text-sm font-medium text-gray-500">Modelo</dt>
                                    <dd class="mt-1 text-sm text-gray-900">{{ $vehicle->vehicleModel?->name ?? '—' }}</dd>
                                </div>
                                <div>
                                    <dt class="text-sm font-medium text-gray-500">Tipo de Carrocería</dt>
                                    <dd class="mt-1 text-sm text-gray-900">{{ $vehicle->body_type ?? '—' }}</dd>
                                </div>
                                <div>
                                    <dt class="text-sm font-medium text-gray-500">Color</dt>
                                    <dd class="mt-1 text-sm text-gray-900">{{ $vehicle->color ?? '—' }}</dd>
                                </div>
                                <div>
                                    <dt class="text-sm font-medium text-gray-500">Año</dt>
                                    <dd class="mt-1 text-sm text-gray-900">{{ $vehicle->year ?? '—' }}</dd>
                                </div>
                                <div>
                                    <dt class="text-sm font-medium text-gray-500">VIN</dt>
                                    <dd class="mt-1 text-sm text-gray-900">{{ $vehicle->vin ?? '—' }}</dd>
                                </div>
                                <div>
                                    <dt class="text-sm font-medium text-gray-500">Número de Motor</dt>
                                    <dd class="mt-1 text-sm text-gray-900">{{ $vehicle->engine_number ?? '—' }}</dd>
                                </div>
                                <div>
                                    <dt class="text-sm font-medium text-gray-500">Próxima Revisión Técnica</dt>
                                    <dd class="mt-1 text-sm text-gray-900">{{ $vehicle->technical_review_date?->format('d/m/Y') ?? '—' }}</dd>
                                </div>
                                <div>
                                    <dt class="text-sm font-medium text-gray-500">Días Aviso Revisión</dt>
                                    <dd class="mt-1 text-sm text-gray-900">{{ $vehicle->review_reminder_days }}</dd>
                                </div>
                            </dl>
                        </div>

                        <div>
                            <h3 class="text-lg font-semibold mb-4">Contactos relacionados</h3>
                            @if($vehicle->relationships->isEmpty())
                                <p class="text-sm text-gray-500">Sin contactos asociados.</p>
                            @else
                                <div class="space-y-3">
                                    @foreach($vehicle->relationships as $relationship)
                                        <div class="p-4 border border-gray-200 rounded-lg">
                                            <div class="flex justify-between items-start">
                                                <div>
                                                    <a href="{{ route('parties.show', $relationship->party) }}" class="font-medium text-gray-900 hover:text-blue-600">
                                                        {{ $relationship->party?->display_name }}
                                                    </a>
                                                    <div class="text-sm text-gray-500">
                                                        {{ $relationship->party?->document_type }} {{ $relationship->party?->document_number }}
                                                    </div>
                                                    <div class="mt-1">
                                                        <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-blue-100 text-blue-800">
                                                            {{ ucfirst(str_replace('_', ' ', $relationship->role)) }}
                                                        </span>
                        @if($relationship->is_primary_commercial)
                                                            <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-green-100 text-green-800">
                                                                <svg class="h-3 w-3 me-1 inline-block" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
                                                                Contacto comercial principal
                                                            </span>
                                                        @endif
                                                    </div>
                                                    @if($relationship->notes)
                                                        <p class="mt-1 text-xs text-gray-500">{{ $relationship->notes }}</p>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
            {{-- Enlace público del portal del cliente --}}
            <div class="card mt-4">
                <div class="p-6">
                    <div class="flex flex-wrap items-center justify-between gap-3">
                        <div>
                            <h3 class="text-lg font-semibold text-gray-800">Enlace público del cliente</h3>
                            <p class="text-sm text-gray-500 mt-1">El cliente abre este enlace para aprobar inventarios y presupuestos, ver avances e histórico.</p>
                        </div>
                        @if ($vehicle->access_token)
                            <div class="flex flex-wrap gap-2">
                                <button type="button" data-copy-link="{{ $vehicle->public_link }}" title="Copiar enlace del portal del cliente" class="btn btn-secondary">
                                    <svg class="h-4 w-4 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M13.828 10.172a4 4 0 010 5.656l-3 3a4 4 0 01-5.656-5.656l1.5-1.5m9.314-9.314a4 4 0 00-5.656 0l-1.5 1.5"/></svg>
                                    Copiar enlace
                                </button>
                                <form method="POST" action="{{ route('vehicles.token.regenerate', $vehicle) }}" data-confirm="¿Regenerar el enlace público? El enlace anterior quedará invalidado al instante.">
                                    @csrf
                                    <button type="submit" class="btn btn-secondary">Regenerar</button>
                                </form>
                                <form method="POST" action="{{ route('vehicles.token.revoke', $vehicle) }}" data-confirm="¿Revocar el enlace público? El cliente ya no podrá acceder con ese enlace.">
                                    @csrf
                                    <button type="submit" class="btn btn-danger">Revocar</button>
                                </form>
                            </div>
                        @else
                            <form method="POST" action="{{ route('vehicles.token.regenerate', $vehicle) }}">
                                @csrf
                                <button type="submit" class="btn btn-primary">Generar enlace</button>
                            </form>
                        @endif
                    </div>
                    @if ($vehicle->access_token)
                        <div class="mt-4 rounded-lg bg-gray-50 border border-gray-200 px-4 py-3 text-sm break-all">
                            <span class="text-gray-500">Enlace: </span>
                            <a href="{{ $vehicle->public_link }}" target="_blank" class="text-blue-600 hover:underline">{{ $vehicle->public_link }}</a>
                        </div>
                    @endif
                </div>
            </div>

        </div>
    </div>
    @push('scripts')
    @include('partials.whatsapp-modal-scripts')
    @endpush
</x-app-layout>
