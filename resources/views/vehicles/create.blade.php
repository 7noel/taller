@php
    $modelsByBrandData = $brands->mapWithKeys(fn ($b) => [$b->id => $b->models->map(fn ($m) => ['id' => $m->id, 'name' => $m->name])]);
@endphp

<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">{{ __('Nuevo Vehículo') }}</h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
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
                                <div class="flex items-center justify-between gap-2">
                                    <label for="brand_id" class="block text-sm font-medium text-gray-700">Marca <span class="text-red-500">*</span></label>
                                    <button type="button" data-bmm-open="brand" class="inline-flex items-center gap-1 text-xs font-medium text-blue-600 hover:underline">
                                        <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg>
                                        Nueva marca
                                    </button>
                                </div>
                                <select id="brand_id" name="brand_id" data-bmm-brand class="mt-1 block w-full rounded-md border-gray-300">
                                    <option value="">Seleccionar marca...</option>
                                    @foreach ($brands as $brand)
                                        <option value="{{ $brand->id }}">{{ $brand->name }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div>
                                <div class="flex items-center justify-between gap-2">
                                    <label for="model_id" class="block text-sm font-medium text-gray-700">Modelo <span class="text-red-500">*</span></label>
                                    <button type="button" id="btn-new-model" data-bmm-open="model" disabled class="inline-flex items-center gap-1 text-xs font-medium text-blue-600 hover:underline disabled:opacity-40 disabled:pointer-events-none">
                                        <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg>
                                        Nuevo modelo
                                    </button>
                                </div>
                                <select id="model_id" name="model_id" data-bmm-model class="mt-1 block w-full rounded-md border-gray-300" disabled>
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

                            <div>
                                <label class="block text-sm font-medium text-gray-700">Último Mantenimiento</label>
                                <input type="date" name="last_maintenance_date" value="{{ old('last_maintenance_date') }}" class="mt-1 block w-full rounded-md border-gray-300">
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700">Km Último Mantenimiento</label>
                                <input type="number" name="last_maintenance_mileage" value="{{ old('last_maintenance_mileage') }}" min="0" class="mt-1 block w-full rounded-md border-gray-300">
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700">Próximo Mantenimiento</label>
                                <input type="date" name="next_maintenance_date" value="{{ old('next_maintenance_date') }}" class="mt-1 block w-full rounded-md border-gray-300">
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700">Días Aviso Mantenimiento</label>
                                <input type="number" name="maintenance_reminder_days" value="{{ old('maintenance_reminder_days', 15) }}" min="1" max="90" class="mt-1 block w-full rounded-md border-gray-300">
                            </div>
                        </div>

                        <div class="flex gap-2 items-center mt-6">
                            <button type="button" id="btnSunarp" class="inline-flex items-center gap-2 bg-blue-600 font-semibold text-xs text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition">
                                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"/><path stroke-linecap="round" stroke-linejoin="round" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                                OBTENER DATOS DE SUNARP
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

    {{-- Modal de contacto compartido (requerido por el componente de relaciones) --}}
    @include('partials.contact-modal')

    {{-- Modal "Nueva marca / Nuevo modelo" --}}
    @include('partials.brand-model-modal')

    @push('scripts')
    <script>
    // Cargar modelos según marca
    const brandSelect = document.getElementById('brand_id');
    const modelSelect = document.getElementById('model_id');
    const btnNewModel = document.getElementById('btn-new-model');
    const modelsByBrand = @json($modelsByBrandData);

    function populateModels(brandId, selectedModelId) {
        modelSelect.innerHTML = '<option value="">Seleccionar modelo...</option>';
        modelSelect.disabled = !brandId;
        if (!brandId) return;
        const models = modelsByBrand[brandId] || [];
        models.forEach(m => {
            const opt = document.createElement('option');
            opt.value = m.id;
            opt.textContent = m.name;
            if (selectedModelId && String(m.id) === String(selectedModelId)) opt.selected = true;
            modelSelect.appendChild(opt);
        });
        modelSelect.disabled = false;
    }

    function syncNewModelButton() {
        btnNewModel.disabled = !brandSelect.value;
    }

    function hasOption(select, value) {
        return [...select.options].some(o => String(o.value) === String(value));
    }

    // Integra marca/modelo creados en los modales "Nueva marca / Nuevo modelo"
    document.addEventListener('brand-model-created', function (e) {
        const detail = e.detail || {};

        if (detail.brand) {
            if (!hasOption(brandSelect, detail.brand.id)) {
                const opt = document.createElement('option');
                opt.value = detail.brand.id;
                opt.textContent = detail.brand.name;
                brandSelect.add(opt);
            }
            if (!modelsByBrand[detail.brand.id]) modelsByBrand[detail.brand.id] = [];
            brandSelect.value = detail.brand.id;
        }

        if (detail.model && brandSelect.value) {
            const list = modelsByBrand[brandSelect.value] || (modelsByBrand[brandSelect.value] = []);
            if (!list.some(m => String(m.id) === String(detail.model.id))) {
                list.push(detail.model);
            }
            populateModels(brandSelect.value, detail.model.id);
        }

        syncNewModelButton();
    });

    brandSelect.addEventListener('change', function () {
        populateModels(this.value);
        syncNewModelButton();
    });

    syncNewModelButton();
    </script>
    @endpush
</x-app-layout>