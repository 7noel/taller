<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ $party->display_name }}
            </h2>
            <div class="flex gap-2">
                <a href="{{ route('parties.edit', $party) }}"
                   class="inline-flex items-center px-4 py-2 bg-yellow-500 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-yellow-600 focus:outline-none focus:ring-2 focus:ring-yellow-500 focus:ring-offset-2 transition ease-in-out duration-150">
                    Editar
                </a>
                <form method="POST" action="{{ route('parties.destroy', $party) }}" onsubmit="return confirm('¿Eliminar esta party?')">
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
                            <h3 class="text-lg font-semibold mb-4">Datos generales</h3>
                            <dl class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                <div>
                                    <dt class="text-sm font-medium text-gray-500">Tipo</dt>
                                    <dd class="mt-1 text-sm text-gray-900">{{ $party->type === 'company' ? 'Empresa' : 'Persona' }}</dd>
                                </div>
                                <div>
                                    <dt class="text-sm font-medium text-gray-500">Documento</dt>
                                    <dd class="mt-1 text-sm text-gray-900">{{ $party->document_type }} {{ $party->document_number }}</dd>
                                </div>
                                @if($party->type === 'company')
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
                                <div>
                                    <dt class="text-sm font-medium text-gray-500">Establecimiento</dt>
                                    <dd class="mt-1 text-sm text-gray-900">{{ $party->establishment?->name ?? '—' }}</dd>
                                </div>
                            </dl>
                        </div>

                        <div>
                            <h3 class="text-lg font-semibold mb-4">Vehículos relacionados</h3>
                            @if($party->vehicles->isEmpty())
                                <p class="text-sm text-gray-500">Sin vehículos asociados.</p>
                            @else
                                <div class="space-y-3">
                                    @foreach($party->vehicles as $vehicle)
                                        <a href="{{ route('vehicles.show', $vehicle) }}" class="block p-4 border border-gray-200 rounded-lg hover:bg-gray-50">
                                            <div class="font-medium text-gray-900">{{ $vehicle->plate }} - {{ $vehicle->brand }} {{ $vehicle->model }}</div>
                                            <div class="text-sm text-gray-500">Rol: {{ $vehicle->pivot->role }} · Año: {{ $vehicle->year }}</div>
                                        </a>
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