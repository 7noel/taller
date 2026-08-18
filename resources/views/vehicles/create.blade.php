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
                            <a href="#" id="btn-sunarp" class="inline-flex items-center px-4 py-2 bg-yellow-500 rounded-md font-semibold text-xs text-white uppercase hover:bg-yellow-600">
                                📷 Obtener datos de Sunarp
                            </a>
                        </div>

                        <div class="mt-6 flex gap-2">
                            <button type="submit" class="inline-flex items-center px-4 py-2 bg-blue-600 rounded-md font-semibold text-xs text-white uppercase hover:bg-blue-700">Guardar</button>
                            <a href="{{ route('vehicles.index') }}" class="inline-flex items-center px-4 py-2 bg-gray-200 rounded-md font-semibold text-xs text-gray-700 uppercase hover:bg-gray-300">Cancelar</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    {{-- Modal OCR Sunarp --}}
    <div id="sunarp-modal" class="fixed inset-0 z-50 hidden overflow-y-auto">
        <div class="flex items-center justify-center min-h-screen px-4">
            <div class="fixed inset-0 bg-gray-500 bg-opacity-75" onclick="closeSunarp()"></div>
            <div class="relative bg-white rounded-lg shadow-xl max-w-lg w-full p-6">
                <div class="flex justify-between items-center mb-4">
                    <h3 class="text-lg font-semibold text-gray-900">Obtener Datos de Sunarp</h3>
                    <button type="button" onclick="closeSunarp()" class="text-gray-400 hover:text-gray-600">✕</button>
                </div>
                <div class="space-y-4">
                    <input type="file" id="sunarp-image" accept="image/*" class="block w-full text-sm text-gray-700 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100">
                    <button type="button" id="sunarp-process" class="inline-flex items-center px-4 py-2 bg-yellow-500 rounded-md font-semibold text-xs text-white uppercase hover:bg-yellow-600">Procesar OCR</button>
                    <div id="sunarp-spinner" class="hidden text-sm text-gray-600">Procesando imagen... (puede tardar unos segundos)</div>
                    <div id="sunarp-result" class="hidden text-sm text-green-700">Datos detectados y aplicados al formulario.</div>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/tesseract.js@4/dist/tesseract.min.js"></script>
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

    // ----- OCR Sunarp -----
    const sunarpModal = document.getElementById('sunarp-modal');
    function openSunarp() { sunarpModal.classList.remove('hidden'); }
    function closeSunarp() { sunarpModal.classList.add('hidden'); }
    document.getElementById('btn-sunarp').addEventListener('click', (e) => { e.preventDefault(); openSunarp(); });

    // Autocompletar marca/modelo por nombre
    function selectBrandModel(brandName, modelName) {
        if (!brandName) return;
        const bOpt = [...brandSelect.options].find(o => o.text.toUpperCase() === brandName.toUpperCase());
        if (bOpt) { brandSelect.value = bOpt.value; brandSelect.dispatchEvent(new Event('change')); }
        if (modelName) {
            const mOpt = [...modelSelect.options].find(o => o.text.toUpperCase() === modelName.toUpperCase());
            if (mOpt) modelSelect.value = mOpt.value;
        }
    }

    document.getElementById('sunarp-process').addEventListener('click', async function() {
        const file = document.getElementById('sunarp-image').files[0];
        if (!file) { alert('Seleccione una imagen'); return; }
        this.disabled = true;
        document.getElementById('sunarp-spinner').classList.remove('hidden');
        try {
            const { data } = await Tesseract.recognize(file, 'eng+spa');
            const text = data.text.toUpperCase();
            const clean = (m) => m ? m[1].trim() : null;
            const brand = clean(text.match(/(?:MARCA|MARCA\/MODELO)[:\s]*([A-Z0-9\.\-\s]{2,12})/i)) || clean(text.match(/(TOYOTA|HYUNDAI|KIA|CHEVROLET|FORD|NISSAN|HONDA|VOLKSWAGEN|MITSUBISHI|MAZDA|SUBARU|RENAULT|PEUGEOT|CITROEN|FIAT|JEEP|SUZUKI)/i));
            const model = clean(text.match(/(?:MODELO)[:\s]*([A-Z0-9\-]{2,20})/i));
            const color = clean(text.match(/(?:COLOR)[:\s]*([A-ZÑÁÉÍÓÚ\s]{3,15})/i));
            const vin = clean(text.match(/(?:VIN|NUMERO DE VIN|N\s?VIN)[:\s]*([A-Z0-9]{17})/i)) || clean(text.match(/[A-HJ-NPR-Z0-9]{17}/i));
            const engine = clean(text.match(/(?:MOTOR|NRO MOTOR|N\.? MOTOR)[:\s]*([A-Z0-9\-]{6,15})/i));
            const year = clean(text.match(/(?:A[ÑN]O|FABRICACION)[:\s]*([0-9]{4})/i));

            // Aplicar a campos
            if (brand) selectBrandModel(brand, model);
            if (color) document.querySelector('input[name="color"]').value = color;
            if (vin) document.querySelector('input[name="vin"]').value = vin;
            if (engine) document.querySelector('input[name="engine_number"]').value = engine;
            if (year) document.querySelector('input[name="year"]').value = year;

            document.getElementById('sunarp-result').classList.remove('hidden');
            setTimeout(closeSunarp, 2000);
        } catch (err) {
            alert('No se pudo procesar la imagen: ' + err.message);
        } finally {
            this.disabled = false;
            document.getElementById('sunarp-spinner').classList.add('hidden');
        }
    });
    </script>
    @endpush
</x-app-layout>