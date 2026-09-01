<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">{{ __('Caja') }}</h2>
            <div class="flex gap-2">
                <a href="{{ route('cash.payment-methods') }}" class="btn btn-secondary">Métodos de Pago</a>
                <a href="{{ route('cash.banks') }}" class="btn btn-secondary">Bancos</a>
            </div>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            @if (session('success'))
                <div class="mb-4 px-4 py-3 bg-green-50 border border-green-200 text-green-700 rounded-lg text-sm">{{ session('success') }}</div>
            @endif
            @if ($errors->any())
                <div class="mb-4 px-4 py-3 bg-red-50 border border-red-200 text-red-700 rounded-lg text-sm">{{ $errors->first() }}</div>
            @endif

            <div class="grid lg:grid-cols-3 gap-4">
                <div class="card p-5">
                    @if ($register)
                        <h3 class="font-semibold text-gray-800 mb-1">{{ $register->name }}</h3>
                        <p class="text-sm text-green-600 font-medium mb-4">● Abierta desde {{ $register->opening_date->format('d/m/Y H:i') }}</p>
                        <dl class="text-sm space-y-2">
                            <div class="flex justify-between"><dt class="text-gray-500">Saldo inicial</dt><dd>S/ {{ number_format($register->opening_amount, 2) }}</dd></div>
                            <div class="flex justify-between"><dt class="text-gray-500">Ingresos</dt><dd class="text-green-600">S/ {{ number_format($register->movements->where('type','income')->sum('amount'), 2) }}</dd></div>
                            <div class="flex justify-between"><dt class="text-gray-500">Egresos</dt><dd class="text-red-600">S/ {{ number_format($register->movements->where('type','expense')->sum('amount'), 2) }}</dd></div>
                            <div class="flex justify-between font-medium"><dt>Esperado</dt><dd>S/ {{ number_format($register->opening_amount + $register->movements->where('type','income')->sum('amount') - $register->movements->where('type','expense')->sum('amount'), 2) }}</dd></div>
                        </dl>
                        <div class="mt-4 border-t pt-4">
                            <h4 class="text-sm font-semibold text-gray-700 mb-2">Registrar egreso</h4>
                            <form method="POST" action="{{ route('cash.expense', $register) }}" class="space-y-2">
                                @csrf
                                <input type="number" name="amount" step="0.01" min="0.01" placeholder="Monto" required class="block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm">
                                <input type="text" name="description" placeholder="Descripción" required class="block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm">
                                <button type="submit" class="btn btn-danger w-full" data-loading-text="Guardando...">Registrar egreso</button>
                            </form>
                        </div>
                        <div class="mt-4 border-t pt-4">
                            <h4 class="text-sm font-semibold text-gray-700 mb-2">Cerrar caja</h4>
                            <form method="POST" action="{{ route('cash.close') }}" data-confirm="¿Cerrar la caja y realizar el arqueo?" class="space-y-2">
                                @csrf
                                <input type="number" name="closing_amount" step="0.01" min="0" placeholder="Monto contado" class="block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm">
                                <button type="submit" class="btn btn-primary w-full" data-loading-text="Cerrando...">Cerrar caja</button>
                            </form>
                        </div>
                    @else
                        <h3 class="font-semibold text-gray-800 mb-2">No hay caja abierta</h3>
                        <form method="POST" action="{{ route('cash.open') }}" class="space-y-2 mt-3">
                            @csrf
                            <input type="number" name="opening_amount" step="0.01" min="0" value="0" placeholder="Saldo inicial" class="block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm">
                            <button type="submit" class="btn btn-primary w-full" data-loading-text="Abriendo...">Abrir caja</button>
                        </form>
                    @endif
                </div>

                <div class="card p-5">
                    <h3 class="font-semibold text-gray-800 mb-3">Registrar adelanto</h3>
                    <form method="POST" action="#" id="advance-form" class="space-y-2">
                        @csrf
                        <select id="advance-estimate" name="estimate_id" class="block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm"></select>
                        <select id="advance-party" name="party_id" class="block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm"></select>
                        <input type="number" id="advance-amount" step="0.01" min="0.01" placeholder="Monto del adelanto" required class="block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm">
                        <button type="submit" class="btn btn-primary w-full" data-loading-text="Guardando...">Cobrar adelanto y facturar</button>
                        <p class="text-xs text-gray-500">Registra el cobro y genera la factura/boleta de adelanto del presupuesto.</p>
                    </form>
                </div>
            </div>

            <div class="card overflow-hidden mt-4">
                <div class="p-4 sm:p-5">
                    <h3 class="font-semibold text-gray-800 mb-3">Movimientos</h3>
                    <div id="cash-movements-table" class="data-table"></div>
                </div>
            </div>
        </div>
    </div>


    @push('scripts')
    <script>
        const cashMovements = new Tabulator('#cash-movements-table', {
            ajaxURL: "{{ route('api.cash.movements') }}?limit=200",
            layout: 'fitColumns', responsiveLayout: 'collapse', height: 'auto', placeholder: 'Sin movimientos',
            columns: [
                { title: 'Fecha', field: 'date', width: 110 },
                { title: 'Tipo', field: 'type_label', width: 100, formatter: (cell) => {
                    const t = cell.getData().type;
                    return `<span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium ${t === 'income' ? 'bg-green-50 text-green-700' : 'bg-red-50 text-red-700'}">${cell.getData().type_label}</span>`;
                }},
                { title: 'Monto', field: 'amount', width: 110, hozAlign: 'right' },
                { title: 'Medio', field: 'payment_method', width: 130 },
                { title: 'Banco', field: 'bank', width: 130 },
                { title: 'Descripción', field: 'description' },
                { title: 'Ref.', field: 'reference', width: 120 },
                { title: 'Caja', field: 'register', width: 130 }
            ]
        });

        const ts = (el, url) => new TomSelect(el, {
            valueField: 'id', labelField: 'text', searchField: ['text'], maxItems: 1, closeAfterSelect: true, create: false, copyClassesToDropdown: false, dropdownParent: 'body',
            load: (q, cb) => fetch(url + '?q=' + encodeURIComponent(q), { headers: { 'X-Requested-With': 'XMLHttpRequest' } })
                .then(r => r.json()).then(d => cb(Array.isArray(d) ? d : d.data || [])).catch(() => cb()),
            render: { option: (d) => `<div>${d.text || d.document_sn}</div>` },
            onItemAdd: () => document.activeElement.blur()
        });

        ts('#advance-estimate', "{{ route('api.estimates.search') }}");
        ts('#advance-party', "{{ route('api.invoices.parties') }}");

        document.getElementById('advance-form').addEventListener('submit', function (e) {
            e.preventDefault();
            const estimateId = document.getElementById('advance-estimate').value;
            if (!estimateId) { alert('Selecciona el presupuesto.'); return; }
            this.action = "/estimates/" + estimateId + "/advance";
            this.requestSubmit();
        });
    </script>
    @endpush
</x-app-layout>

