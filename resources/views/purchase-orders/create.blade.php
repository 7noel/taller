<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">{{ __('Nueva Orden de Compra') }}</h2>
            <a href="{{ route('purchase-orders.index') }}" class="btn btn-secondary">Volver</a>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            @if ($errors->any())
                <div class="mb-4 px-4 py-3 bg-red-50 border border-red-200 text-red-700 rounded-lg text-sm">
                    <ul class="list-disc list-inside">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form method="POST" action="{{ route('purchase-orders.store') }}" class="card p-6">
                @csrf
                @include('purchase-orders._form', ['po' => null])

                <div class="mt-6 flex justify-end">
                    <button type="submit" class="btn btn-primary">Guardar OC</button>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
