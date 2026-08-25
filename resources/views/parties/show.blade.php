<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">{{ $party->display_name }}</h2>
            <div class="flex gap-2">
                <a href="{{ route('parties.edit', $party) }}" class="inline-flex items-center px-4 py-2 bg-yellow-500 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-yellow-600">Editar</a>
                <form method="POST" action="{{ route('parties.destroy', $party) }}" data-confirm="¿Eliminar esta party?">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="inline-flex items-center px-4 py-2 bg-red-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-red-700">Eliminar</button>
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
                            <h3 class="text-lg font-semibold mb-4">Datos generales</h3>
                            <dl class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                <div>
                                    <dt class="text-sm font-medium text-gray-500">Tipo</dt>
                                    <dd class="mt-1 text-sm text-gray-900">{{ $party->document_type === '6' ? 'Empresa' : 'Persona' }}</dd>
                                </div>
                                <div>
                                    <dt class="text-sm font-medium text-gray-500">Documento</dt>
                                    <dd class="mt-1 text-sm text-gray-900">{{ $party->document_type_label }} {{ $party->document_number }}</dd>
                                </div>
                                @if($party->document_type === '6')
                                <div>
                                    <dt class="text-sm font-medium text-gray-500">Razón Social</dt>
                                    <dd class="mt-1 text-sm text-gray-900">{{ $party->business_name }}</dd>
                                </div>
                                @else
                                <div>
                                    <dt class="text-sm font-medium text-gray-500">Nombre Completo</dt>
                                    <dd class="mt-1 text-sm text-gray-900">{{ $party->display_name }}</dd>
                                </div>
                                @endif
                                <div>
                                    <dt class="text-sm font-medium text-gray-500">Email</dt>
                                    <dd class="mt-1 text-sm text-gray-900">{{ $party->email ?? '—' }}</dd>
                                </div>
                                <div>
                                    <dt class="text-sm font-medium text-gray-500">Teléfono</dt>
                                    <dd class="mt-1 text-sm text-gray-900">{{ $party->phone ?? '—' }}</dd>
                                </div>
                                <div>
                                    <dt class="text-sm font-medium text-gray-500">Celular</dt>
                                    <dd class="mt-1 text-sm text-gray-900">{{ $party->mobile ?? '—' }}</dd>
                                </div>
                                <div>
                                    <dt class="text-sm font-medium text-gray-500">Dirección</dt>
                                    <dd class="mt-1 text-sm text-gray-900">{{ $party->address ?? '—' }}</dd>
                                </div>
                                <div>
                                    <dt class="text-sm font-medium text-gray-500">Distrito</dt>
                                    <dd class="mt-1 text-sm text-gray-900">{{ $party->ubigeo ? $party->ubigeo->distrito . ' - ' . $party->ubigeo->provincia : '—' }}</dd>
                                </div>
                                <div>
                                    <dt class="text-sm font-medium text-gray-500">Compañía de seguros</dt>
                                    <dd class="mt-1 text-sm text-gray-900">{{ $party->is_insurance_company ? 'Sí' : 'No' }}</dd>
                                </div>
                            </dl>
                        </div>

                        <div>
                            <h3 class="text-lg font-semibold mb-4">Vehículos relacionados</h3>
                            @if($party->vehicles->isEmpty())
                                <p class="text-sm text-gray-500">Sin vehículos asociados.</p>
                            @else
                                <div class="grid grid-cols-1 gap-3">
                                    @foreach($party->vehicles as $vehicle)
                                        <div class="border border-gray-200 rounded-lg p-4 hover:bg-gray-50">
                                            <div class="flex items-center justify-between mb-2">
                                                <a href="{{ route('vehicles.show', $vehicle) }}" class="font-semibold text-blue-600 hover:underline">{{ $vehicle->plate }}</a>
                                                @if($vehicle->pivot->is_primary_commercial ?? false)
                                                    <span class="text-yellow-500" title="Contacto comercial principal">
                                                        <svg class="h-4 w-4 inline-block" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
                                                    </span>
                                                @endif
                                            </div>
                                            <div class="text-sm text-gray-600">
                                                {{ $vehicle->vehicleModel?->brand?->name ?? 'No especificado' }} · {{ $vehicle->vehicleModel?->name ?? 'No especificado' }}
                                            </div>
                                            <div class="text-sm text-gray-500">
                                                @if($vehicle->year) Año: {{ $vehicle->year }} · @endif
                                                @if($vehicle->color) Color: {{ $vehicle->color }} @endif
                                            </div>
                                            <span class="inline-block mt-2 px-2 py-1 text-xs font-medium rounded-full
                                                @switch($vehicle->pivot->role)
                                                    @case('owner') bg-blue-100 text-blue-700 @break
                                                    @case('driver') bg-green-100 text-green-700 @break
                                                    @case('approver') bg-yellow-100 text-yellow-700 @break
                                                    @case('operator') bg-purple-100 text-purple-700 @break
                                                    @default bg-gray-100 text-gray-700
                                                @endswitch">
                                                {{ \App\Models\VehicleRelationship::roleLabels()[$vehicle->pivot->role] ?? ucfirst($vehicle->pivot->role ?? 'Sin rol') }}
                                            </span>
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