<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Detalle de Cliente') }}
            </h2>
            <div class="flex gap-2">
                <a href="{{ route('clients.edit', $client) }}"
                   class="inline-flex items-center px-4 py-2 bg-yellow-500 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-yellow-600 focus:outline-none focus:ring-2 focus:ring-yellow-500 focus:ring-offset-2 transition ease-in-out duration-150">
                    Editar
                </a>
                <form method="POST" action="{{ route('clients.destroy', $client) }}" onsubmit="return confirm('¿Eliminar este cliente?')">
                    @csrf
                    @method('DELETE')
                    <button type="submit"
                            class="inline-flex items-center px-4 py-2 bg-red-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2 transition ease-in-out duration-150">
                        Eliminar
                    </button>
                </form>
                <a href="{{ route('clients.index') }}"
                   class="inline-flex items-center px-4 py-2 bg-gray-200 border border-transparent rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest hover:bg-gray-300 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 transition ease-in-out duration-150">
                    Volver
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                {{-- Información del cliente --}}
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4">Información General</h3>
                        <dl class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Razón Social / Nombre</dt>
                                <dd class="mt-1 text-sm text-gray-900">{{ $client->business_name }}</dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Tipo de Documento</dt>
                                <dd class="mt-1 text-sm text-gray-900">{{ $client->document_type }}</dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Número de Documento</dt>
                                <dd class="mt-1 text-sm text-gray-900">{{ $client->document_number }}</dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-gray-500">¿Es Aseguradora?</dt>
                                <dd class="mt-1 text-sm text-gray-900">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $client->is_insurance_company ? 'bg-blue-100 text-blue-800' : 'bg-gray-100 text-gray-800' }}">
                                        {{ $client->is_insurance_company ? 'Sí' : 'No' }}
                                    </span>
                                </dd>
                            </div>
                            @if($client->is_insurance_company)
                                <div>
                                    <dt class="text-sm font-medium text-gray-500">Precio Hora Hombre</dt>
                                    <dd class="mt-1 text-sm text-gray-900">S/ {{ number_format($client->insurance_hourly_rate, 2) }}</dd>
                                </div>
                                <div>
                                    <dt class="text-sm font-medium text-gray-500">Precio Paño Pintura</dt>
                                    <dd class="mt-1 text-sm text-gray-900">S/ {{ number_format($client->insurance_panel_rate, 2) }}</dd>
                                </div>
                            @endif
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Teléfono</dt>
                                <dd class="mt-1 text-sm text-gray-900">{{ $client->phone ?? '—' }}</dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Celular</dt>
                                <dd class="mt-1 text-sm text-gray-900">{{ $client->mobile ?? '—' }}</dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Email</dt>
                                <dd class="mt-1 text-sm text-gray-900">{{ $client->email ?? '—' }}</dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Establecimiento</dt>
                                <dd class="mt-1 text-sm text-gray-900">{{ $client->establishment?->name }}</dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Ubigeo</dt>
                                <dd class="mt-1 text-sm text-gray-900">
                                    @if($client->ubigeo)
                                        {{ $client->ubigeo->departamento }} / {{ $client->ubigeo->provincia }} / {{ $client->ubigeo->distrito }}
                                    @else
                                        —
                                    @endif
                                </dd>
                            </div>
                            <div class="col-span-2">
                                <dt class="text-sm font-medium text-gray-500">Dirección</dt>
                                <dd class="mt-1 text-sm text-gray-900">{{ $client->address ?? '—' }}</dd>
                            </div>
                        </dl>
                    </div>
                </div>

                {{-- Vehículos asociados --}}
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <div class="flex justify-between items-center mb-4">
                            <h3 class="text-lg font-semibold text-gray-900">Vehículos Asociados</h3>
                            <a href="{{ route('vehicles.create') }}"
                               class="text-sm text-blue-600 hover:text-blue-800">+ Agregar</a>
                        </div>

                        @if($client->vehicles->count())
                            <div class="space-y-3">
                                @foreach($client->vehicles as $vehicle)
                                    <a href="{{ route('vehicles.show', $vehicle) }}"
                                       class="block p-4 border border-gray-200 rounded-lg hover:bg-gray-50 transition">
                                        <div class="flex justify-between items-center">
                                            <div>
                                                <span class="text-sm font-semibold text-gray-900">{{ $vehicle->plate }}</span>
                                                <span class="ml-2 text-sm text-gray-600">{{ $vehicle->brand }} {{ $vehicle->model }}</span>
                                            </div>
                                            <span class="text-xs text-gray-500">{{ $vehicle->year }}</span>
                                        </div>
                                    </a>
                                @endforeach
                            </div>
                        @else
                            <p class="text-sm text-gray-500">Este cliente no tiene vehículos registrados.</p>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>