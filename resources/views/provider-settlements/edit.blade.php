<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">Editar Liquidación <span class="text-sm font-mono text-gray-500">{{ $settlement->document_sn }}</span></h2>
            <a href="{{ route('provider-settlements.index') }}" class="btn btn-secondary">Volver</a>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            @include('provider-settlements._form', [
                'action' => route('provider-settlements.update', $settlement),
                'method' => 'PUT',
                'submitLabel' => 'Guardar Cambios',
                'settlement' => $settlement,
                'igvRate' => $igvRate,
                'detractionRate' => $detractionRate,
            ])
        </div>
    </div>
</x-app-layout>
