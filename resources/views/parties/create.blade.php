<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Nuevo Contacto de Vehículo') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <form method="POST" action="{{ route('parties.store') }}">
                        @csrf

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            {{-- Tipo de documento (códigos SUNAT) --}}
                            <div class="md:col-span-2">
                                <label for="document_type" class="block text-sm font-medium text-gray-700">Tipo de Documento *</label>
                                <select id="document_type" name="document_type" required
                                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                    <option value="">Seleccionar...</option>
                                    <option value="1" @selected(old('document_type') == '1')>DNI</option>
                                    <option value="6" @selected(old('document_type') == '6')>RUC</option>
                                    <option value="4" @selected(old('document_type') == '4')>Carné de Extranjería</option>
                                    <option value="7" @selected(old('document_type') == '7')>Pasaporte</option>
                                    <option value="A" @selected(old('document_type') == 'A')>Cédula Diplomática</option>
                                </select>
                                @error('document_type') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                            </div>

                            {{-- Número de documento --}}
                            <div>
                                <label for="document_number" class="block text-sm font-medium text-gray-700">Número de Documento *</label>
                                <div class="flex gap-2">
                                    <input type="text" id="document_number" name="document_number" value="{{ old('document_number') }}" required
                                           class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                    <button type="button" id="btnSearchDocument" data-party-search-btn
                                            class="mt-1 inline-flex items-center px-3 py-2 bg-blue-600 text-white rounded-md text-sm hover:bg-blue-700 transition whitespace-nowrap">
                                        🔍 Buscar
                                    </button>
                                </div>
                                @error('document_number') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                            </div>

                            {{-- Campos persona --}}
                            <div id="person-fields" class="md:col-span-2 grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <label for="first_name" class="block text-sm font-medium text-gray-700">Nombres</label>
                                    <input type="text" id="first_name" name="first_name" value="{{ old('first_name') }}"
                                           class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                    @error('first_name') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                                </div>
                                <div>
                                    <label for="last_name" class="block text-sm font-medium text-gray-700">Apellidos</label>
                                    <input type="text" id="last_name" name="last_name" value="{{ old('last_name') }}"
                                           class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                    @error('last_name') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                                </div>
                            </div>

                            {{-- Campos empresa --}}
                            <div id="company-fields" class="md:col-span-2 hidden">
                                <label for="business_name" class="block text-sm font-medium text-gray-700">Razón Social *</label>
                                <input type="text" id="business_name" name="business_name" value="{{ old('business_name') }}"
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
                                        <option value="{{ $departamento }}">{{ $departamento }}</option>
                                    @endforeach
                                </select>
                            </div>

                            {{-- Ubigeo: Provincia --}}
                            <div>
                                <label for="provincia" class="block text-sm font-medium text-gray-700">Provincia</label>
                                <select id="provincia" name="provincia" disabled
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
                                          class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">{{ old('address') }}</textarea>
                            </div>

                            {{-- Teléfono --}}
                            <div>
                                <label for="phone" class="block text-sm font-medium text-gray-700">Teléfono</label>
                                <input type="text" id="phone" name="phone" value="{{ old('phone') }}"
                                       class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                            </div>

                            {{-- Celular --}}
                            <div>
                                <label for="mobile" class="block text-sm font-medium text-gray-700">Celular</label>
                                <input type="text" id="mobile" name="mobile" value="{{ old('mobile') }}"
                                       class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                            </div>

                            {{-- Email --}}
                            <div>
                                <label for="email" class="block text-sm font-medium text-gray-700">Email</label>
                                <input type="email" id="email" name="email" value="{{ old('email') }}"
                                       class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                            </div>

                            {{-- Es aseguradora --}}
                            <div class="md:col-span-2 flex items-center gap-2">
                                <input type="checkbox" id="is_insurance_company" name="is_insurance_company" value="1"
                                       class="rounded border-gray-300 text-blue-600 shadow-sm focus:ring-blue-500"
                                       @checked(old('is_insurance_company'))>
                                <label for="is_insurance_company" class="text-sm font-medium text-gray-700">Es compañía de seguros</label>
                            </div>

                            {{-- Tarifas aseguradora (ocultas por defecto) --}}
                            <div id="insurance-fields" class="md:col-span-2 grid grid-cols-1 md:grid-cols-2 gap-4 hidden">
                                <div>
                                    <label for="insurance_hourly_rate" class="block text-sm font-medium text-gray-700">Precio Hora Hombre (S/)</label>
                                    <input type="number" step="0.01" min="0" id="insurance_hourly_rate" name="insurance_hourly_rate"
                                           value="{{ old('insurance_hourly_rate') }}"
                                           class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                </div>
                                <div>
                                    <label for="insurance_panel_rate" class="block text-sm font-medium text-gray-700">Precio Paño de Pintura (S/)</label>
                                    <input type="number" step="0.01" min="0" id="insurance_panel_rate" name="insurance_panel_rate"
                                           value="{{ old('insurance_panel_rate') }}"
                                           class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                </div>
                            </div>

                            {{-- Recibir promociones --}}
                            <div class="md:col-span-2 flex items-center gap-2">
                                <input type="checkbox" id="receive_promotions" name="receive_promotions" value="1"
                                       class="rounded border-gray-300 text-blue-600 shadow-sm focus:ring-blue-500"
                                       @checked(old('receive_promotions', true))>
                                <label for="receive_promotions" class="text-sm font-medium text-gray-700">Recibir promociones</label>
                            </div>
                        </div>

                        <div class="mt-6 flex gap-2">
                            <button type="submit"
                                    class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition ease-in-out duration-150">
                                Guardar
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
    <script src="{{ asset('js/party-helper.js') }}"></script>
    <script>
        // Toggle persona / empresa según tipo de documento (1=DNI, 6=RUC)
        const docTypeSelect = document.getElementById('document_type');
        const personFields = document.getElementById('person-fields');
        const companyFields = document.getElementById('company-fields');

        function toggleTypeFields() {
            const isCompany = docTypeSelect.value === '6';
            personFields.classList.toggle('hidden', isCompany);
            companyFields.classList.toggle('hidden', !isCompany);
        }

        docTypeSelect.addEventListener('change', toggleTypeFields);
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
                    distrito.disabled = false;
                });
        });

        // Consulta DNI/RUC automática
        if (window.PartyHelper) {
            window.PartyHelper.init(
                document.getElementById('document_number'),
                { button: document.getElementById('btnSearchDocument') }
            );
        }
    </script>
    @endpush
</x-app-layout>
