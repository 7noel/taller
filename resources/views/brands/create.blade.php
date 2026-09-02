<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Nueva Marca de Vehículo</h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8">
            @if (session('success'))
                <div class="mb-4 px-4 py-3 bg-green-50 border border-green-200 text-green-700 rounded-lg text-sm">{{ session('success') }}</div>
            @endif

            <div class="card overflow-hidden">
                <form method="POST" action="{{ route('brands.store') }}" class="p-6">
                    @csrf
                    @include('brands._form', ['brand' => null, 'models' => collect()])

                    <div class="mt-6 flex gap-2">
                        <button type="submit" class="btn btn-primary">Guardar marca</button>
                        <a href="{{ route('brands.index') }}" class="btn btn-secondary">Cancelar</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
