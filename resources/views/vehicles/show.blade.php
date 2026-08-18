<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Detalle de Vehículo') }}
            </h2>
            <div class="flex gap-2">
                <a href="{{ route('vehicles.edit', $vehicle) }}"
                   class="inline-flex items-center px-4 py-2 bg-yellow-500 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-yellow-600 focus:outline-none focus:ring-2 focus:ring-yellow-500 focus:ring-offset-2 transition ease-in-out duration-150">
                    Editar
                </a>
                <form method="POST" action="{{ route('vehicles.destroy', $vehicle) }}" onsubmit="return confirm('¿Eliminar este vehículo?')">
                    @csrf
                    @method('DELETE')
                    <button type="submit"
                            class="inline-flex items-center px-4 py-2 bg-red-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2 transition ease-in-out duration-150">
                        Eliminar
                    </button>
                </form>
                <a href="{{ route('vehicles.index') }}"
                   class="inline-flex items-center px-4 py-2 bg-gray-200 border border-transparent rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest hover:bg-gray-300 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 transition ease-in-out duration-150">
                    Volver
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                {{-- Información del vehículo --}}
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4">Información del Vehículo</h3>
                        <dl class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Placa</dt>
                                <dd class="mt-1 text-sm text-gray-900 font-semibold">{{ $vehicle->plate }}</dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Tipo de Carrocería</dt>
                                <dd class="mt-1 text-sm text-gray-900">{{ ucfirst($vehicle->body_type) }}</dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Marca</dt>
                                <dd class="mt-1 text-sm text-gray-900">{{ $vehicle->brand }}</dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Modelo</dt>
                                <dd class="mt-1 text-sm text-gray-900">{{ $vehicle->model }}</dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Año</dt>
                                <dd class="mt-1 text-sm text-gray-900">{{ $vehicle->year ?? '—' }}</dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Color</dt>
                                <dd class="mt-1 text-sm text-gray-900">{{ $vehicle->color ?? '—' }}</dd>
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
                                <dt class="text-sm font-medium text-gray-500">Establecimiento</dt>
                                <dd class="mt-1 text-sm text-gray-900">{{ $vehicle->establishment?->name }}</dd>
                            </div>
                        </dl>
                    </div>
                </div>

                {{-- Cliente --}}
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <div class="flex justify-between items-center mb-4">
                            <h3 class="text-lg font-semibold text-gray-900">Cliente</h3>
                            @if($vehicle->client)
                                <a href="{{ route('clients.show', $vehicle->client) }}" class="text-sm text-blue-600 hover:text-blue-800">Ver cliente</a>
                            @endif
                        </div>

                        @if($vehicle->client)
                            <dl class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                <div class="col-span-2">
                                    <dt class="text-sm font-medium text-gray-500">Razón Social / Nombre</dt>
                                    <dd class="mt-1 text-sm text-gray-900">{{ $vehicle->client->business_name }}</dd>
                                </div>
                                <div>
                                    <dt class="text-sm font-medium text-gray-500">Documento</dt>
                                    <dd class="mt-1 text-sm text-gray-900">{{ $vehicle->client->document_type }}: {{ $vehicle->client->document_number }}</dd>
                                </div>
                                <div>
                                    <dt class="text-sm font-medium text-gray-500">Celular</dt>
                                    <dd class="mt-1 text-sm text-gray-900">{{ $vehicle->client->mobile ?? '—' }}</dd>
                                </div>
                            </dl>
                        @else
                            <p class="text-sm text-gray-500">Sin cliente asociado.</p>
                        @endif
                    </div>
                </div>

                {{-- Contactos --}}
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg md:col-span-2">
                    <div class="p-6">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4">Contactos del Vehículo</h3>

                        @if($vehicle->contacts->count())
                            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
                                @foreach($vehicle->contacts as $contact)
                                    <div class="p-4 border border-gray-200 rounded-lg">
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800 mb-2">
                                            {{ match($contact->type) { 'approver' => 'Aprobador', 'driver' => 'Chofer', 'operator' => 'Operador', default => $contact->type } }}
                                        </span>
                                        <p class="text-sm font-semibold text-gray-900">{{ $contact->name }}</p>
                                        @if($contact->phone)
                                            <p class="text-sm text-gray-600">📞 {{ $contact->phone }}</p>
                                        @endif
                                        @if($contact->email)
                                            <p class="text-sm text-gray-600">✉️ {{ $contact->email }}</p>
                                        @endif
                                        @if($contact->company_name)
                                            <p class="text-sm text-gray-600">🏢 {{ $contact->company_name }}</p>
                                        @endif
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <p class="text-sm text-gray-500">Este vehículo no tiene contactos registrados.</p>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>