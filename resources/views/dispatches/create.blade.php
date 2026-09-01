<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">{{ __('Nueva Guía de Remisión') }}</h2>
            <a href="{{ route('dispatches.index') }}" class="btn btn-secondary">Volver</a>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            @if ($errors->any())
                <div class="mb-4 px-4 py-3 bg-red-50 border border-red-200 text-red-700 rounded-lg text-sm">
                    @foreach ($errors->all() as $error) <p>{{ $error }}</p> @endforeach
                </div>
            @endif

            <div class="card p-6">
                <form method="POST" action="{{ route('dispatches.store') }}">
                    @csrf
                    <div class="grid md:grid-cols-3 gap-4">
                        <div>
                            <label for="dispatch_type" class="block text-sm font-medium text-gray-700">Tipo <span class="text-red-500">*</span></label>
                            <select name="dispatch_type" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm">
                                <option value="remitente">Guía Remitente</option>
                                <option value="transportista">Guía Transportista</option>
                            </select>
                        </div>
                        <div>
                            <label for="motivo_traslado" class="block text-sm font-medium text-gray-700">Motivo de traslado <span class="text-red-500">*</span></label>
                            <select name="motivo_traslado" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm">
                                @foreach (App\Models\Dispatch::MOTIVOS_TRASLADO as $code => $label)
                                    <option value="{{ $code }}">{{ $code }} · {{ $label }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label for="modo_transporte" class="block text-sm font-medium text-gray-700">Transporte</label>
                            <select name="modo_transporte" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm">
                                <option value="02">Privado</option>
                                <option value="01">Público</option>
                            </select>
                        </div>
                        <div>
                            <label for="party_id" class="block text-sm font-medium text-gray-700">Destinatario</label>
                            <select id="party_id" name="party_id" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm"></select>
                        </div>
                        <div>
                            <label for="invoice_id" class="block text-sm font-medium text-gray-700">Factura que ampara</label>
                            <select id="invoice_id" name="invoice_id" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm"></select>
                        </div>
                        <div>
                            <label for="fecha_de_traslado" class="block text-sm font-medium text-gray-700">Fecha de traslado <span class="text-red-500">*</span></label>
                            <input type="date" name="fecha_de_traslado" value="{{ now()->toDateString() }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm">
                        </div>
                        <div><label for="peso_total" class="block text-sm font-medium text-gray-700">Peso total</label><input type="number" step="0.001" name="peso_total" value="0" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm"></div>
                        <div><label for="numero_de_bultos" class="block text-sm font-medium text-gray-700">N° de bultos</label><input type="number" name="numero_de_bultos" value="1" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm"></div>
                        <div><label for="vehiculo_placa" class="block text-sm font-medium text-gray-700">Placa</label><input type="text" name="vehiculo_placa" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm"></div>
                    </div>

                    <div class="grid md:grid-cols-3 gap-4 mt-4">
                        <div><label class="block text-sm font-medium text-gray-700">Ubigeo partida</label><input type="text" name="punto_partida_ubigeo" placeholder="150101" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm"></div>
                        <div class="md:col-span-2"><label class="block text-sm font-medium text-gray-700">Dirección partida</label><input type="text" name="punto_partida_direccion" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm"></div>
                        <div><label class="block text-sm font-medium text-gray-700">Ubigeo llegada</label><input type="text" name="punto_llegada_ubigeo" placeholder="150101" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm"></div>
                        <div class="md:col-span-2"><label class="block text-sm font-medium text-gray-700">Dirección llegada</label><input type="text" name="punto_llegada_direccion" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm"></div>
                        <div><label class="block text-sm font-medium text-gray-700">Transportista</label><input type="text" name="transportista_denominacion" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm"></div>
                        <div><label class="block text-sm font-medium text-gray-700">Transportista RUC</label><input type="text" name="transportista_documento_numero" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm"></div>
                        <div><label class="block text-sm font-medium text-gray-700">Conductor</label><input type="text" name="conductor_nombre" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm"></div>
                        <div><label class="block text-sm font-medium text-gray-700">Licencia</label><input type="text" name="conductor_numero_licencia" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm"></div>
                    </div>

                    <div class="mt-6">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Ítems trasladados <span class="text-red-500">*</span></label>
                        <div class="space-y-2" id="dispatch-items">
                            <div class="grid grid-cols-10 gap-2 items-center">
                                <div class="col-span-6"><input name="items[0][description]" placeholder="Descripción" required class="block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm"></div>
                                <div class="col-span-2"><input name="items[0][quantity]" type="number" step="0.01" min="0.01" value="1" class="block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm"></div>
                                <div class="col-span-1"><select name="items[0][uom]" class="block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm"><option value="NIU">NIU</option><option value="ZZ">ZZ</option></select></div>
                                <div class="col-span-1"><button type="button" onclick="this.closest('.grid').remove()" class="btn-icon btn-icon-red" title="Quitar"><svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg></button></div>
                            </div>
                        </div>
                        <button type="button" id="add-dispatch-item" class="mt-2 btn btn-secondary">+ Agregar ítem</button>
                    </div>

                    <div class="mt-6 flex justify-end">
                        <button type="submit" class="btn btn-primary" data-loading-text="Guardando...">Crear guía</button>
                    </div>
                </form>
            </div>
        </div>
    </div>


    @push('scripts')
    <script>
        document.getElementById('add-dispatch-item').addEventListener('click', () => {
            const idx = document.querySelectorAll('#dispatch-items .grid').length;
            const div = document.createElement('div');
            div.className = 'grid grid-cols-10 gap-2 items-center';
            div.innerHTML = `
                <div class="col-span-6"><input name="items[${idx}][description]" placeholder="Descripción" required class="block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm"></div>
                <div class="col-span-2"><input name="items[${idx}][quantity]" type="number" step="0.01" min="0.01" value="1" class="block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm"></div>
                <div class="col-span-1"><select name="items[${idx}][uom]" class="block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm"><option value="NIU">NIU</option><option value="ZZ">ZZ</option></select></div>
                <div class="col-span-1"><button type="button" onclick="this.closest('.grid').remove()" class="btn-icon btn-icon-red" title="Quitar"><svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg></button></div>`;
            document.getElementById('dispatch-items').appendChild(div);
        });

        const ts = (el, url) => new TomSelect(el, {
            valueField: 'id', labelField: 'text', searchField: ['text'], maxItems: 1, closeAfterSelect: true, create: false, copyClassesToDropdown: false, dropdownParent: 'body',
            load: (q, cb) => fetch(url + '?q=' + encodeURIComponent(q), { headers: { 'X-Requested-With': 'XMLHttpRequest' } })
                .then(r => r.json()).then(d => cb(Array.isArray(d) ? d : d.data || [])).catch(() => cb()),
            render: { option: (d) => `<div>${d.text || d.document_sn}</div>` },
            onItemAdd: () => document.activeElement.blur()
        });

        ts('#party_id', "{{ route('api.parties.search') }}");
        ts('#invoice_id', "{{ route('api.invoices.search') }}");
    </script>
    @endpush
</x-app-layout>

