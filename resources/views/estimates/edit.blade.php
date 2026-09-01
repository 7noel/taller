<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center gap-3 flex-wrap">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">{{ __('Editar Presupuesto') }}</h2>
            @if ($estimate->document_sn)
                <x-document-badge :sn="$estimate->document_sn" />
            @endif
            @if ($estimate->vehicle?->plate)
                <span class="text-sm text-gray-500">{{ $estimate->vehicle->plate }}</span>
            @endif
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <form method="POST" action="{{ route('estimates.update', $estimate) }}">
                @csrf
                @method('PUT')
                @include('estimates._form', ['estimate' => $estimate, 'checkIn' => null, 'parentEstimate' => null, 'advisors' => $advisors, 'technicians' => $technicians, 'establishment' => $establishment])

                <div class="mt-6 flex gap-2">
                    <button type="submit" class="btn btn-primary">Guardar Cambios</button>
                    <a href="{{ route('estimates.show', $estimate) }}" class="btn btn-secondary">Cancelar</a>
                </div>
            </form>
        </div>
    </div>

    @include('partials.contact-modal')
    @include('estimates._currency-modal')
    @include('estimates._form-scripts', ['estimate' => $estimate, 'checkIn' => null, 'parentEstimate' => null, 'technicians' => $technicians, 'establishment' => $establishment])
</x-app-layout>