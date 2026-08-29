<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">{{ __('Nueva Liquidación de Servicios Tercerizados') }}</h2>
            <a href="{{ route('provider-settlements.index') }}" class="btn btn-secondary">Volver</a>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            @include('provider-settlements._form', [
                'action' => route('provider-settlements.store'),
                'method' => 'POST',
                'submitLabel' => 'Crear Liquidación',
                'settlement' => new \App\Models\ProviderSettlement(),
                'igvRate' => $igvRate,
                'detractionRate' => $detractionRate,
            ])
        </div>
    </div>
</x-app-layout>
