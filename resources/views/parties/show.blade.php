<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">{{ $party->display_name }}</h2>
            <div class="flex gap-2">
                <a href="{{ route('parties.edit', $party) }}" class="inline-flex items-center px-4 py-2 bg-yellow-500 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-yellow-600">Editar</a>
                <form method="POST" action="{{ route('parties.destroy', $party) }}" onsubmit="return confirm('¿Eliminar esta party?')">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="inline-flex items-center px-4 py-2 bg-red-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-red-700">Eliminar</button>
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
                                <div class="overflow-x-auto">
                                    <table class="min-w-full bg-white rounded-lg shadow">
                                        <thead class="bg-gray-100">
                                            <tr>
                                                <th class="px-4 py-2 text-left text-xs font-medium text-gray-600 uppercase">Placa</th>
                                                <th class="px-4 py-2 text-left text-xs font-medium text-gray-600 uppercase">Marca</th>
                                                <th class="px-4 py-2 text-left text-xs font-medium text-gray-600 uppercase">Modelo</th>
                                                <th class="px-4 py-2 text-left text-xs font-medium text-gray-600 uppercase">Año</th>
                                                <th class="px-4 py-2 text-left text-xs font-medium text-gray-600 uppercase">Color</th>
                                                <th class="px-4 py-2 text-left text-xs font-medium text-gray-600 uppercase">Rol</th>
                                                <th class="px-4 py-2 text-left text-xs font-medium text-gray-600 uppercase">Principal</th>
                                            </tr>
                                        </thead>
                                        <tbody class="divide-y divide-gray-200">
                                            @foreach($party->vehicles as $vehicle)
                                                @php
                                                    $role = $vehicle->pivot->role ?? 'Sin rol';
                                                    $isPrimary = $vehicle->pivot->is_primary_commercial ?? false;
                                                @endphp
                                                <tr>
                                                    <td class="px-4 py-2"><a href="{{ route('vehicles.show', $vehicle) }}" class="text-blue-600 hover:underline">{{ $vehicle->plate }}</a></td>
                                                    <td class="px-4 py-2">{{ $vehicle->vehicleModel?->brand?->name ?? 'No especificado' }}</td>
                                                    <td class="px-4 py-2">{{ $vehicle->vehicleModel?->name ?? 'No especificado' }}</td>
                                                    <td class="px-4 py-2">{{ $vehicle->year ?? '-' }}</td>
                                                    <td class="px-4 py-2">{{ $vehicle->color ?? '-' }}</td>
                                                    <td class="px-4 py-2"><span class="px-2 py-1 text-xs font-medium rounded-full
                                                        @switch($role)
                                                            @case('owner') bg-blue-100 text-blue-700 @break
                                                            @case('driver') bg-green-100 text-green-700 @break
                                                            @case('approver') bg-yellow-100 text-yellow-700 @break
                                                            @case('operator') bg-purple-100 text-purple-700 @break
                                                            @default bg-gray-100 text-gray-700
                                                        @endswitch">{{ ucfirst($role) }}</span></td>
                                                    <td class="px-4 py-2 text-center">@if($isPrimary)<span class="text-yellow-500" title="Contacto comercial principal">⭐</span>@else<span class="text-gray-300">-</span>@endif</td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>