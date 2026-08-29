<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">{{ __('Editar Orden de Compra') }}</h2>
            <div class="flex gap-2">
                <a href="{{ route('purchase-orders.show', $purchaseOrder) }}" class="btn btn-secondary">Ver</a>
                <a href="{{ route('purchase-orders.index') }}" class="btn btn-secondary">Volver</a>
            </div>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            @if ($errors->any())
                <div class="mb-4 px-4 py-3 bg-red-50 border border-red-200 text-red-700 rounded-lg text-sm">{{ $errors->first() }}</div>
            @endif

            <form method="POST" action="{{ route('purchase-orders.update', $purchaseOrder) }}" class="card p-6">
                @csrf
                @method('PUT')
                @include('purchase-orders._form', ['po' => $purchaseOrder])

                <div class="mt-6 flex justify-end">
                    <button type="submit" class="btn btn-primary">Actualizar OC</button>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
