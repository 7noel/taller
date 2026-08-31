<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Cita {{ $appointment->scheduled_at_display }}
            </h2>
            <div class="flex gap-2">
                <a href="{{ route('appointments.edit', $appointment) }}" class="btn btn-secondary">Editar</a>
                <a href="{{ route('appointments.index') }}" class="btn btn-primary">Volver</a>
            </div>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            @if (session('success'))
                <div class="mb-4 px-4 py-3 bg-green-50 border border-green-200 text-green-700 rounded-lg text-sm">
                    {{ session('success') }}
                </div>
            @endif

            <div class="card overflow-hidden mb-6">
                <div class="p-4 sm:p-5">
                    <div class="flex flex-wrap items-center gap-3 mb-4">
                        @php
                            $badge = \App\Models\Appointment::STATUS_BADGES[$appointment->status] ?? 'bg-gray-100 text-gray-600';
                        @endphp
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $badge }}">{{ $appointment->status_label }}</span>
                        @if ($appointment->checkIn)
                            <a href="{{ route('check-ins.show', $appointment->checkIn) }}" class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-50 text-blue-700 hover:bg-blue-100">
                                Asociada al ingreso {{ $appointment->checkIn->document_sn }}
                            </a>
                        @endif
                        @if ($appointment->status === 'scheduled')
                            <form method="POST" action="{{ route('appointments.confirm', $appointment) }}">
                                @csrf
                                <button type="submit" class="btn btn-primary">Confirmar cita</button>
                            </form>
                        @endif
                        @if (in_array($appointment->status, ['scheduled', 'confirmed'], true))
                            <form method="POST" action="{{ route('appointments.cancel', $appointment) }}" data-confirm="¿Cancelar esta cita?">
                                @csrf
                                <button type="submit" class="btn btn-danger">Cancelar</button>
                            </form>
                        @endif
                        @if ($appointment->status === 'completed' && $appointment->check_in_id)
                            <form method="POST" action="{{ route('appointments.unlink', $appointment) }}" data-confirm="¿Desasociar la cita del ingreso? La cita volverá a confirmada.">
                                @csrf
                                <button type="submit" class="btn btn-secondary">Desasociar del ingreso</button>
                            </form>
                        @endif
                    </div>
                    <dl class="grid grid-cols-1 md:grid-cols-2 gap-x-6 gap-y-4 text-sm">
                        <div>
                            <dt class="font-medium text-gray-500">Vehículo</dt>
                            <dd class="text-gray-800">
                                @if ($appointment->vehicle)
                                    <span class="font-semibold">{{ $appointment->vehicle->plate }}</span>
                                    <span class="text-gray-500">· {{ $appointment->vehicle->vehicleModel?->brand?->name }} {{ $appointment->vehicle->vehicleModel?->name }} ({{ $appointment->vehicle->year ?? '—' }})</span>
                                @else
                                    <span class="text-gray-400">No especificado</span>
                                @endif
                            </dd>
                        </div>
                        <div>
                            <dt class="font-medium text-gray-500">Contacto</dt>
                            <dd class="text-gray-800">{{ $appointment->contact_name ?: ($appointment->party?->display_name ?? '—') }}</dd>
                        </div>
                        <div>
                            <dt class="font-medium text-gray-500">Teléfono</dt>
                            <dd class="text-gray-800">{{ $appointment->contact_phone ?: '—' }}</dd>
                        </div>
                        <div>
                            <dt class="font-medium text-gray-500">Email</dt>
                            <dd class="text-gray-800">{{ $appointment->contact_email ?: '—' }}</dd>
                        </div>
                        <div>
                            <dt class="font-medium text-gray-500">Fecha / Hora</dt>
                            <dd class="text-gray-800">{{ $appointment->scheduled_at_display }}</dd>
                        </div>
                        <div>
                            <dt class="font-medium text-gray-500">Asesor</dt>
                            <dd class="text-gray-800">{{ $appointment->advisor?->name ?: '—' }}</dd>
                        </div>
                        <div>
                            <dt class="font-medium text-gray-500">Tipo de servicio</dt>
                            <dd class="text-gray-800">{{ $appointment->service_type_label ?: '—' }}</dd>
                        </div>
                        <div>
                            <dt class="font-medium text-gray-500">Establecimiento</dt>
                            <dd class="text-gray-800">{{ $appointment->establishment?->name ?: '—' }}</dd>
                        </div>
                        @if ($appointment->reason)
                            <div class="md:col-span-2">
                                <dt class="font-medium text-gray-500">Motivo</dt>
                                <dd class="text-gray-800 whitespace-pre-line">{{ $appointment->reason }}</dd>
                            </div>
                        @endif
                    </dl>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>

