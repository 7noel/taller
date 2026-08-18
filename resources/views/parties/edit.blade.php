<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Editar Party') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <form method="POST" action="{{ route('parties.update', $party) }}">
                        @csrf
                        @method('PUT')

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            {{-- Tipo --}}
                            <div>
                                <label for="type" class="block text-sm font-medium text-gray-700">Tipo *</label>
                                <select id="type" name="type" required
                                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                    <option value="">Seleccionar...</option>
                                    <option value="person" @selected(old('type', $party->type) === 'person')>Persona</option>
                                    <option value="company" @selected(old('type', $party->type) === 'company')>Empresa</option>
                                </select>
                                @error('type') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                            </div>

                            {{-- Tipo de documento --}}
                            <div>
                                <label for="document_type" class="block text-sm font-medium text-gray-700">Tipo de Documento *</label>
                                <select id="document_type" name="document_type" required
                                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                    <option value="">Seleccionar...</option>
                                    <option value="DNI" @selected(old('document_type', $party->document_type) == 'DNI')>DNI</option>
                                    <option value="RUC" @selected(old('document_type', $party->document_type) == 'RUC')>RUC</option>
                                    <option value="PAS" @selected(old('document_type', $party->document_type) == 'PAS')>Pasaporte</option>
                                    <option value="CEX" @selected(old('document_type', $party->document_type) == 'CEX')>Carné de Extranjería</option>
                                </select>
                                @error('document_type') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                            </div>

                            {{-- Número de documento --}}
                            <div>
                                <label for="document_number" class="block text-sm font-medium text-gray-700">Número de Documento *</label>
                                <input type="text" id="document_number" name="document_number" value="{{ old('document_number', $party->document_number) }}" required
                                       class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                @error('document_number') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                            </div>

                            {{-- Campos persona --}}
                            <div id="person-fields" class="md:col-span-2 grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <label for="first_name" class="block text-sm font-medium text-gray-700">Nombres</label>
                                    <input type="text" id="first_name" name="first_name" value="{{ old('first_name', $party->first_name) }}"
                                           class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                </div>
                                <div>
                                    <label for="last_name" class="block text-sm font-medium text-gray-700">Apellidos</label>
                                    <input type="text" id="last_name" name="last_name" value="{{ old('last_name', $party->last_name) }}"
                                           class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                </div>
                            </div>

                            {{-- Campos empresa --}}
                            <div id="company-fields" class="md:col-span-2 hidden">
                                <label for="business_name" class="block text-sm font-medium text-gray-700">Razón Social</label>
                                <input type="text" id="business_name" name="business_name" value="{{ old('business_name', $party->business_name) }}"
                                       class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                @error('business_name') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                            </div>

                            {{-- Ubigeo: Departamentos --}}
                            <div>
                                <label for="departamento" class="block text-sm font-medium text-gray-700">Departamento</label>
                                <select id="departamento" name="departamento"
                                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                    <option value="">Seleccionar...</option>
                                    @foreach ($departamentos as $departamento)
                                        <option value="{{ $departamento }}" @selected($party->ubigeo?->departamento == $departamento)>{{ $departamento }}</option>
                                    @endforeach
                                </select>
                            </div>

                            {{-- Ubigeo: Provincia --}}
                            <div>
                                <label for="provincia" class="block text-sm font-medium text-gray-700">Provincia</label>
                                <select id="provincia" name="provincia"
                                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                    <option value="">Seleccionar...</option>
                                </select>
                            </div>

                            {{-- Ubigeo: Distrito --}}
                            <div>
                                <label for="distrito" class="block text-sm font-medium text-gray-700">Distrito</label>
                                <select id="distrito" name="ubigeo_code" disabled
                                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                    <option value="">Seleccionar...</option>
                                </select>
                                @error('ubigeo_code') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                            </div>

                            {{-- Dirección --}}
                            <div>
                                <label for="address" class="block text-sm font-medium text-gray-700">Dirección</label>
                                <textarea id="address" name="address" rows="2"
                                          class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">{{ old('address', $party->address) }}</textarea>
                            </div>

                            {{-- Teléfono --}}
                            <div>
                                <label for="phone" class="block text-sm font-medium text-gray-700">Teléfono</label>
                                <input type="text" id="phone" name="phone" value="{{ old('phone', $party->phone) }}"
                                       class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                            </div>

                            {{-- Celular --}}
                            <div>
                                <label for="mobile" class="block text-sm font-medium text-gray-700">Celular</label>
                                <input type="text" id="mobile" name="mobile" value="{{ old('mobile', $party->mobile) }}"
                                       class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                            </div>

                            {{-- Email --}}
                            <div>
                                <label for="email" class="block text-sm font-medium text-gray-700">Email</label>
                                <input type="email" id="email" name="email" value="{{ old('email', $party->email) }}"
                                       class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                            </div>

                            {{-- Establecimiento --}}
                            <div>
                                <label for="establishment_id" class="block text-sm font-medium text-gray-700">Establecimiento *</label>
                                <select id="establishment_id" name="establishment_id" required
                                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                    @foreach ($establishments as $est)
                                        <option value="{{ $est->id }}" @selected(old('establishment_id', $party->establishment_id) == $est->id)>{{ $est->name }}</option>
                                    @endforeach
                                </select>
                                @error('establishment_id') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                            </div>

                            {{-- Es aseguradora --}}
                            <div class="md:col-span-2 flex items-center gap-2">
                                <input type="checkbox" id="is_insurance_company" name="is_insurance_company" value="1"
                                       class="rounded border-gray-300 text-blue-600 shadow-sm focus:ring-blue-500"
                                       @checked(old('is_insurance_company', $party->is_insurance_company))>
                                <label for="is_insurance_company" class="text-sm font-medium text-gray-700">Es compañía de seguros</label>
                            </div>

                            {{-- Tarifas aseguradora --}}
                            <div id="insurance-fields" class="md:col-span-2 grid grid-cols-1 md:grid-cols-2 gap-4 hidden">
                                <div>
                                    <label for="insurance_hourly_rate" class="block text-sm font-medium text-gray-700">Precio Hora Hombre (S/)</label>
                                    <input type="number" step="0.01" min="0" id="insurance_hourly_rate" name="insurance_hourly_rate"
                                           value="{{ old('insurance_hourly_rate', $party->insurance_hourly_rate) }}"
                                           class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                </div>
                                <div>
                                    <label for="insurance_panel_rate" class="block text-sm font-medium text-gray-700">Precio Paño de Pintura (S/)</label>
                                    <input type="number" step="0.01" min="0" id="insurance_panel_rate" name="insurance_panel_rate"
                                           value="{{ old('insurance_panel_rate', $party->insurance_panel_rate) }}"
                                           class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                </div>
                            </div>

                            {{-- Recibir promociones --}}
                            <div class="md:col-span-2 flex items-center gap-2">
                                <input type="checkbox" id="receive_promotions" name="receive_promotions" value="1"
                                       class="rounded border-gray-300 text-blue-600 shadow-sm focus:ring-blue-500"
                                       @checked(old('receive_promotions', $party->receive_promotions))>
                                <label for="receive_promotions" class="text-sm font-medium text-gray-700">Recibir promociones</label>
                            </div>
                        </div>

                        <div class="mt-6 flex gap-2">
                            <button type="submit"
                                    class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition ease-in-out duration-150">
                                Actualizar
                            </button>
                            <a href="{{ route('parties.index') }}"
                               class="inline-flex items-center px-4 py-2 bg-gray-200 border border-transparent rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest hover:bg-gray-300 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 transition ease-in-out duration-150">
                                Cancelar
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        // Toggle persona / empresa
        const typeSelect = document.getElementById('type');
        const personFields = document.getElementById('person-fields');
        const companyFields = document.getElementById('company-fields');

        function toggleTypeFields() {
            const isPerson = typeSelect.value === 'person';
            personFields.classList.toggle('hidden', !isPerson);
            companyFields.classList.toggle('hidden', isPerson);
        }

        typeSelect.addEventListener('change', toggleTypeFields);
        toggleTypeFields();

        // Mostrar/ocultar campos de tarifas si es aseguradora
        const chkInsurance = document.getElementById('is_insurance_company');
        const insuranceFields = document.getElementById('insurance-fields');

        function toggleInsuranceFields() {
            insuranceFields.classList.toggle('hidden', !chkInsurance.checked);
        }

        chkInsurance.addEventListener('change', toggleInsuranceFields);
        toggleInsuranceFields();

        // Selects en cascada de ubigeo
        const departamento = document.getElementById('departamento');
        const provincia = document.getElementById('provincia');
        const distrito = document.getElementById('distrito');

        @if ($party->ubigeo)
            // Precargar provincia y distrito si hay ubigeo
            const initialDepartamento = @json($party->ubigeo->departamento);
            const initialProvincia = @json($party->ubigeo->provincia);
            const initialDistritoCode = @json($party->ubigeo_code);

            if (initialDepartamento) {
                departamento.value = initialDepartamento;
                fetch(`{{ route('api.ubigeo.provincias') }}?departamento=${encodeURIComponent(initialDepartamento)}`)
                    .then(r => r.json())
                    .then(data => {
                        data.forEach(p => {
                            const opt = document.createElement('option');
                            opt.value = p;
                            opt.textContent = p;
                            provincia.appendChild(opt);
                        });
                        if (initialProvincia) {
                            provincia.value = initialProvincia;
                            provincia.dispatchEvent(new Event('change'));
                        }
                    });
            }
        @endif

        departamento.addEventListener('change', function() {
            provincia.disabled = !this.value;
            provincia.innerHTML = '<option value="">Seleccionar...</option>';
            distrito.disabled = true;
            distrito.innerHTML = '<option value="">Seleccionar...</option>';

            if (!this.value) return;

            fetch(`{{ route('api.ubigeo.provincias') }}?departamento=${encodeURIComponent(this.value)}`)
                .then(r => r.json())
                .then(data => {
                    data.forEach(p => {
                        const opt = document.createElement('option');
                        opt.value = p;
                        opt.textContent = p;
                        provincia.appendChild(opt);
                    });
                    provincia.disabled = false;
                });
        });

        provincia.addEventListener('change', function() {
            distrito.disabled = !this.value || !departamento.value;
            distrito.innerHTML = '<option value="">Seleccionar...</option>';

            if (!this.value) return;

            fetch(`{{ route('api.ubigeo.distritos') }}?departamento=${encodeURIComponent(departamento.value)}&provincia=${encodeURIComponent(this.value)}`)
                .then(r => r.json())
                .then(data => {
                    data.forEach(d => {
                        const opt = document.createElement('option');
                        opt.value = d.code;
                        opt.textContent = d.distrito;
                        distrito.appendChild(opt);
                    });
                    @if ($party->ubigeo)
                        const initialDistritoCode = @json($party->ubigeo_code);
                        if (initialDistritoCode) distrito.value = initialDistritoCode;
                    @endif
                    distrito.disabled = false;
                });
        });
    </script>
    @endpush
</x-app-layout>