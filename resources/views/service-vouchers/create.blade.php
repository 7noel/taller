<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">{{ __('Nuevo Comprobante de Servicio Tercerizado') }}</h2>
            <a href="{{ route('service-vouchers.index') }}" class="btn btn-secondary">Volver</a>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            @php
                $newVoucher = new \App\Models\ServiceVoucher();
                if (!empty($preselectedWorkOrder)) {
                    $newVoucher->work_order_id = $preselectedWorkOrder->id;
                }
            @endphp
            @include('service-vouchers._form', [
                'action' => route('service-vouchers.store'),
                'method' => 'POST',
                'submitLabel' => 'Emitir Comprobante',
                'voucher' => $newVoucher,
                'igvRate' => $igvRate,
                'detractionRate' => $detractionRate,
                'preselectedWorkOrder' => $preselectedWorkOrder ?? null,
            ])
        </div>
    </div>
</x-app-layout>
