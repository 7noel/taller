<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">{{ __('Nuevo Repuesto') }}</h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="card overflow-hidden">
                <form method="POST" action="{{ route('parts.store') }}" class="p-6">
                    @csrf
                    @include('parts._form', ['part' => null])

                    <div class="mt-6 border-t border-gray-100 pt-6">
                        <h3 class="text-sm font-semibold text-gray-700 mb-3">Inventario inicial (opcional)</h3>
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Cantidad inicial</label>
                                <input type="number" step="0.01" min="0" name="initial_quantity" value="{{ old('initial_quantity') }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Almacén</label>
                                <select name="initial_warehouse_id" id="initial_warehouse_id" class="mt-1 w-full">
                                    @foreach (\App\Models\Warehouse::orderBy('name')->get() as $wh)
                                        <option value="{{ $wh->id }}" @selected(old('initial_warehouse_id') == $wh->id)>{{ $wh->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="flex items-end pb-2">
                                <p class="text-xs text-gray-500">Se genera un movimiento de entrada al guardar.</p>
                            </div>
                        </div>
                    </div>

                    <div class="mt-6 flex gap-2">
                        <button type="submit" class="btn btn-primary">Guardar</button>
                        <a href="{{ route('parts.index') }}" class="btn btn-secondary">Cancelar</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>