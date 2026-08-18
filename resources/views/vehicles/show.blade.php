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
                <form method="POST" action="{{ route('vehicles.destroy', $vehicle) }}" onsubmit="return confirm('¿Eliminar este vehículo?')">
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

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
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
                                    <dd class="mt-1 text-sm text-gray-900">{{ $vehicle->brand }}</dd>
                                </div>
                                <div>
                                    <dt class="text-sm font-medium text-gray-500">Modelo</dt>
                                    <dd class="mt-1 text-sm text-gray-900">{{ $vehicle->model }}</dd>
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
                                    <dd class="mt-1 text-sm text-gray-900">{{ $vehicle->next_technical_review_date?->format('d/m/Y') ?? '—' }}</dd>
                                </div>
                                <div>
                                    <dt class="text-sm font-medium text-gray-500">Días Aviso Revisión</dt>
                                    <dd class="mt-1 text-sm text-gray-900">{{ $vehicle->technical_review_reminder_days }}</dd>
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
                                                                ⭐ Contacto comercial principal
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
        </div>
    </div>
</x-app-layout>