<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">{{ __('Nuevo Vehículo') }}</h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <form method="POST" action="{{ route('vehicles.store') }}" id="vehicle-form">
                        @csrf

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Placa *</label>
                                <input type="text" name="plate" value="{{ old('plate') }}" required maxlength="7" placeholder="ABC123"
                                       class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 uppercase">
                                @error('plate') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700">Marca *</label>
                                <select id="brand_id" name="brand_id" class="mt-1 block w-full rounded-md border-gray-300">
                                    <option value="">Seleccionar marca...</option>
                                    @foreach ($brands as $brand)
                                        <option value="{{ $brand->id }}">{{ $brand->name }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700">Modelo *</label>
                                <select id="model_id" name="model_id" class="mt-1 block w-full rounded-md border-gray-300" disabled>
                                    <option value="">Seleccione primero la marca...</option>
                                </select>
                                @error('model_id') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700">Color</label>
                                <input type="text" name="color" value="{{ old('color') }}" class="mt-1 block w-full rounded-md border-gray-300">
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700">VIN</label>
                                <input type="text" name="vin" value="{{ old('vin') }}" class="mt-1 block w-full rounded-md border-gray-300 uppercase">
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700">N. Motor</label>
                                <input type="text" name="engine_number" value="{{ old('engine_number') }}" class="mt-1 block w-full rounded-md border-gray-300 uppercase">
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700">Año</label>
                                <input type="number" name="year" value="{{ old('year') }}" min="1900" max="{{ date('Y') + 1 }}" class="mt-1 block w-full rounded-md border-gray-300">
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700">Carrocería</label>
                                <select name="body_type" class="mt-1 block w-full rounded-md border-gray-300">
                                    <option value="">Seleccionar...</option>
                                    <option value="sedan">Sedán</option>
                                    <option value="suv">SUV</option>
                                    <option value="pickup">Pickup</option>
                                    <option value="camioneta">Camioneta</option>
                                    <option value="camion">Camión</option>
                                    <option value="moto">Moto</option>
                                </select>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700">Próxima Revisión Técnica</label>
                                <input type="date" name="technical_review_date" value="{{ old('technical_review_date') }}" class="mt-1 block w-full rounded-md border-gray-300">
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700">Días Aviso Revisión</label>
                                <input type="number" name="review_reminder_days" value="{{ old('review_reminder_days', 15) }}" min="1" max="90" class="mt-1 block w-full rounded-md border-gray-300">
                            </div>
                        </div>

                        <div class="flex gap-2 items-center mt-6">
                            <button type="button" id="btnSunarp" class="bg-blue-600 font-semibold text-xs text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition">
                                📸 OBTENER DATOS DE SUNAT
                            </button>
                        </div>

                        @include('vehicles._relationships')

                        <div class="mt-6 flex gap-2">
                            <button type="submit" class="inline-flex items-center px-4 py-2 bg-blue-600 rounded-md font-semibold text-xs text-white uppercase hover:bg-blue-700">Guardar</button>
                            <a href="{{ route('vehicles.index') }}" class="inline-flex items-center px-4 py-2 bg-gray-200 rounded-md font-semibold text-xs text-gray-700 uppercase hover:bg-gray-300">Cancelar</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    @include('vehicles._sunarp_modal')

    @push('scripts')
    <script>
    // Cargar modelos según marca
    const brandSelect = document.getElementById('brand_id');
    const modelSelect = document.getElementById('model_id');
    const modelsByBrand = @json($brands->mapWithKeys(fn ($b) => [$b->id => $b->models->map(fn ($m) => ['id' => $m->id, 'name' => $m->name])]));

    brandSelect.addEventListener('change', function() {
        modelSelect.innerHTML = '<option value="">Seleccionar modelo...</option>';
        modelSelect.disabled = !this.value;
        const models = modelsByBrand[this.value] || [];
        models.forEach(m => {
            const opt = document.createElement('option');
            opt.value = m.id;
            opt.textContent = m.name;
            modelSelect.appendChild(opt);
        });
    });
    </script>
    @endpush
</x-app-layout>