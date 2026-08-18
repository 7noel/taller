<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">{{ __('Editar Vehículo') }}</h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <form method="POST" action="{{ route('vehicles.update', $vehicle) }}" id="vehicle-form">
                        @csrf
                        @method('PUT')

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Placa *</label>
                                <input type="text" name="plate" value="{{ old('plate', $vehicle->plate) }}" required maxlength="7" placeholder="ABC123"
                                       class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 uppercase">
                                @error('plate') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700">Marca *</label>
                                <select id="brand_id" name="brand_id" class="mt-1 block w-full rounded-md border-gray-300">
                                    @foreach ($brands as $brand)
                                        <option value="{{ $brand->id }}" @selected($vehicle->vehicleModel?->brand_id == $brand->id)>{{ $brand->name }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="md:col-span-2">
                                <label class="block text-sm font-medium text-gray-700">Modelo *</label>
                                <select id="model_id" name="model_id" required class="mt-1 block w-full rounded-md border-gray-300">
                                    <option value="">Seleccionar modelo...</option>
                                </select>
                                @error('model_id') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700">Color</label>
                                <input type="text" name="color" value="{{ old('color', $vehicle->color) }}" class="mt-1 block w-full rounded-md border-gray-300">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700">VIN</label>
                                <input type="text" name="vin" value="{{ old('vin', $vehicle->vin) }}" class="mt-1 block w-full rounded-md border-gray-300 uppercase">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700">N. Motor</label>
                                <input type="text" name="engine_number" value="{{ old('engine_number', $vehicle->engine_number) }}" class="mt-1 block w-full rounded-md border-gray-300 uppercase">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Año</label>
                                <input type="number" name="year" value="{{ old('year', $vehicle->year) }}" min="1900" max="{{ date('Y') + 1 }}" class="mt-1 block w-full rounded-md border-gray-300">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Carrocería</label>
                                <select name="body_type" class="mt-1 block w-full rounded-md border-gray-300">
                                    <option value="">Seleccionar...</option>
                                    @foreach (['sedan' => 'Sedán', 'suv' => 'SUV', 'pickup' => 'Pickup', 'camioneta' => 'Camioneta', 'camion' => 'Camión', 'moto' => 'Moto'] as $val => $label)
                                        <option value="{{ $val }}" @selected(old('body_type', $vehicle->body_type) == $val)>{{ $label }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Próxima Revisión Técnica</label>
                                <input type="date" name="technical_review_date" value="{{ old('technical_review_date', $vehicle->technical_review_date?->format('Y-m-d')) }}" class="mt-1 block w-full rounded-md border-gray-300">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Días Aviso Revisión</label>
                                <input type="number" name="review_reminder_days" value="{{ old('review_reminder_days', $vehicle->review_reminder_days) }}" min="1" max="90" class="mt-1 block w-full rounded-md border-gray-300">
                            </div>
                        </div>

                        <div class="flex gap-2 items-center mt-6">
                            <button type="button" id="btnSunarp" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition">
                                📸 Obtener datos de Sunarp
                            </button>
                        </div>

                        <div class="mt-6 flex gap-2">
                            <button type="submit" class="inline-flex items-center px-4 py-2 bg-blue-600 rounded-md font-semibold text-xs text-white uppercase hover:bg-blue-700">Actualizar</button>
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
    const brandSelect = document.getElementById('brand_id');
    const modelSelect = document.getElementById('model_id');
    const selectedModelId = @json($vehicle->model_id);
    const selectedBrandId = @json($vehicle->vehicleModel?->brand_id);
    const modelsByBrand = @json($brands->mapWithKeys(fn ($b) => [$b->id => $b->models->map(fn ($m) => ['id' => $m->id, 'name' => $m->name])]));

    function fillModels(brandId) {
        modelSelect.innerHTML = '<option value="">Seleccionar modelo...</option>';
        if (!brandId) return;
        const models = modelsByBrand[brandId] || [];
        models.forEach(m => {
            const opt = document.createElement('option');
            opt.value = m.id;
            opt.textContent = m.name;
            if (m.id == selectedModelId) opt.selected = true;
            modelSelect.appendChild(opt);
        });
    }

    brandSelect.value = selectedBrandId;
    fillModels(selectedBrandId);

    brandSelect.addEventListener('change', function() { fillModels(this.value); });
    </script>
    @endpush
</x-app-layout>