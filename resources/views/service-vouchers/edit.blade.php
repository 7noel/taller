<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">{{ __('Editar Comprobante') }} <span class="text-sm font-mono text-gray-500">{{ $voucher->document_sn }}</span></h2>
            <a href="{{ route('service-vouchers.index') }}" class="btn btn-secondary">Volver</a>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            @include('service-vouchers._form', [
                'action' => route('service-vouchers.update', $voucher),
                'method' => 'PUT',
                'submitLabel' => 'Guardar Cambios',
                'voucher' => $voucher,
                'igvRate' => $igvRate,
                'detractionRate' => $detractionRate,
            ])
        </div>
    </div>
</x-app-layout>
