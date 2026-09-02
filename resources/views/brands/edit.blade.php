<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">Editar Marca — {{ $brand->name }}</h2>
            <a href="{{ route('brands.index') }}" class="btn btn-secondary">Volver al catálogo</a>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8">
            @if (session('success'))
                <div class="mb-4 px-4 py-3 bg-green-50 border border-green-200 text-green-700 rounded-lg text-sm">{{ session('success') }}</div>
            @endif
            @if (session('warning'))
                <div class="mb-4 px-4 py-3 bg-amber-50 border border-amber-200 text-amber-700 rounded-lg text-sm">
                    @foreach ((array) session('warning') as $warning)
                        <p>{{ $warning }}</p>
                    @endforeach
                </div>
            @endif
            @if (session('error'))
                <div class="mb-4 px-4 py-3 bg-red-50 border border-red-200 text-red-700 rounded-lg text-sm">{{ session('error') }}</div>
            @endif

            <div class="card overflow-hidden">
                <form method="POST" action="{{ route('brands.update', $brand) }}" class="p-6">
                    @csrf
                    @method('PUT')
                    @include('brands._form', ['brand' => $brand, 'models' => $models])

                    <div class="mt-6 flex gap-2">
                        <button type="submit" class="btn btn-primary">Guardar cambios</button>
                        <a href="{{ route('brands.index') }}" class="btn btn-secondary">Cancelar</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
