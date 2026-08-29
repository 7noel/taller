<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">{{ __('Pedidos de Repuestos') }}</h2>
            <button type="button" id="btn-new-order" class="btn btn-primary">
                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/>
                </svg>
                Nuevo pedido
            </button>
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

            <div class="card overflow-hidden">
                <div class="p-4 sm:p-5">
                    <div class="flex flex-col sm:flex-row sm:items-center gap-3 mb-3">
                        <div class="relative max-w-md flex-1">
                            <svg class="pointer-events-none absolute left-3 top-1/2 h-4 w-4 -translate-y-1/2 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                            </svg>
                            <input type="text" id="po-search" placeholder="Buscar por repuesto o SKU..." class="search-input">
                        </div>
                        <select id="po-status" class="rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm">
                            <option value="">Todos los estados</option>
                            <option value="pending">Pendiente de pedido</option>
                            <option value="ordered">Pedido realizado</option>
                            <option value="in_transit">En camino</option>
                            <option value="received">En almacén</option>
                        </select>
                    </div>
                    <div id="part-order-table" class="data-table"></div>
                </div>
            </div>
        </div>
    </div>

    {{-- Modal nuevo pedido --}}
    <div id="new-order-modal" class="hidden fixed inset-0 z-50 overflow-y-auto" aria-hidden="true" role="dialog" aria-modal="true">
        <div class="fixed inset-0 bg-gray-900/50" data-close-order></div>
        <div class="relative min-h-screen flex items-center justify-center p-4">
            <div class="relative w-full max-w-lg rounded-lg bg-white shadow-xl">
                <div class="flex items-center justify-between px-6 py-4 border-b border-gray-200">
                    <h3 class="text-lg font-semibold text-gray-800">Nuevo pedido de repuesto</h3>
                    <button type="button" data-close-order class="text-gray-400 hover:text-gray-600" title="Cerrar">
                        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                </div>
                <form method="POST" action="{{ route('part-orders.store') }}" class="px-6 py-5 space-y-4">
                    @csrf
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Repuesto <span class="text-red-500">*</span></label>
                        <select id="order-part" name="part_id" placeholder="Buscar repuesto..."></select>
                    </div>
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Cantidad <span class="text-red-500">*</span></label>
                            <input type="number" step="0.01" min="0" name="quantity" value="1" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Entrega estimada</label>
                            <input type="date" name="expected_delivery" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                        </div>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Presupuesto (opcional)</label>
                        <select id="order-estimate" name="estimate_id" placeholder="Buscar presupuesto..."></select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Proveedor (opcional)</label>
                        <select id="order-provider" name="provider_id" placeholder="Buscar proveedor..."></select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Tracking / N° pedido</label>
                        <input type="text" name="tracking_number" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500" maxlength="100">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Notas</label>
                        <textarea name="notes" rows="2" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"></textarea>
                    </div>
                    <div class="flex justify-end gap-2 pt-2">
                        <button type="button" data-close-order class="btn btn-secondary">Cancelar</button>
                        <button type="submit" id="order-submit" class="btn btn-primary">Registrar pedido</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        const STATUS_NEXT = { pending: 'ordered', ordered: 'in_transit', in_transit: 'received', received: null };
    const STATUS_LABELS = { pending: 'Pendiente de pedido', ordered: 'Pedido realizado', in_transit: 'En camino', received: 'En almacén' };

    const statusBadge = (cell) => {
        const v = cell.getValue();
        const map = {
            pending: ['Pendiente de pedido', 'bg-amber-50 text-amber-700'],
            ordered: ['Pedido realizado', 'bg-blue-50 text-blue-700'],
            in_transit: ['En camino', 'bg-indigo-50 text-indigo-700'],
            received: ['En almacén', 'bg-green-50 text-green-700'],
        };
        const [label, cls] = map[v] || [v, 'bg-gray-100 text-gray-600'];
        return `<span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium ${cls}">${label}</span>`;
    };

    const table = new Tabulator('#part-order-table', {
        ajaxURL: "{{ route('api.part-orders.search') }}?limit=100",
        layout: 'fitColumns',
        responsiveLayout: 'collapse',
        placeholder: 'No hay pedidos de repuestos registrados',
        height: 'auto',
        columns: [
            { title: 'Repuesto', field: 'part', minWidth: 180 },
            { title: 'SKU', field: 'sku', width: 100 },
            { title: 'Cant.', field: 'quantity', width: 80, hozAlign: 'right' },
            { title: 'Presupuesto', field: 'estimate_sn', width: 130, formatter: cell => cell.getValue() || '—' },
            { title: 'Proveedor', field: 'provider', width: 140, formatter: cell => cell.getValue() || '—' },
            { title: 'Estado', field: 'status', width: 150, hozAlign: 'center', formatter: statusBadge },
            { title: 'Entrega', field: 'expected_delivery', width: 100 },
            { title: '', field: 'id', width: 120, hozAlign: 'center', headerSort: false,
              formatter: (cell) => {
                  const d = cell.getData();
                  const next = STATUS_NEXT[d.status];
                  const advance = next
                      ? `<button type="button" class="btn-icon btn-icon-blue" title="Avanzar a: ${STATUS_LABELS[next]}" data-advance="${d.id}" data-next="${next}"><svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M13 7l5 5-5 5M6 7l5 5-5 5"/></svg></button>`
                      : '';
                  return advance;
              } }
        ]
    });

    function reload() {
        const params = new URLSearchParams({ limit: 100 });
        const q = document.getElementById('po-search').value;
        const status = document.getElementById('po-status').value;
        if (q) params.set('q', q);
        if (status) params.set('status', status);
        table.setData("{{ route('api.part-orders.search') }}?" + params.toString());
    }
    document.getElementById('po-search').addEventListener('input', reload);
    document.getElementById('po-status').addEventListener('change', reload);

    // Modal
    const modal = document.getElementById('new-order-modal');
    const openBtn = document.getElementById('btn-new-order');
    function openModal() { modal.classList.remove('hidden'); document.body.classList.add('overflow-hidden'); }
    function closeModal() { modal.classList.add('hidden'); document.body.classList.remove('overflow-hidden'); }
    openBtn.addEventListener('click', openModal);
    document.querySelectorAll('[data-close-order]').forEach(b => b.addEventListener('click', closeModal));

    // Tom Selects
    if (window.TomSelect) {
        new TomSelect('#order-part', {
            valueField: 'id', labelField: 'name', searchField: ['name', 'sku', 'barcode'],
            maxItems: 1, closeAfterSelect: true, create: false, copyClassesToDropdown: false, dropdownParent: 'body',
            load: (q, cb) => fetch(`{{ route('api.parts.search') }}?q=${encodeURIComponent(q)}`).then(r => r.json()).then(cb).catch(() => cb())
        });
        new TomSelect('#order-estimate', {
            valueField: 'id', labelField: 'document_sn', searchField: ['document_sn', 'plate'],
            maxItems: 1, closeAfterSelect: true, create: false, copyClassesToDropdown: false, dropdownParent: 'body',
            load: (q, cb) => fetch(`{{ route('api.estimates.search') }}?q=${encodeURIComponent(q)}`).then(r => r.json()).then(cb).catch(() => cb())
        });
        new TomSelect('#order-provider', {
            valueField: 'id', labelField: 'display_name', searchField: ['display_name', 'document_number', 'name'],
            maxItems: 1, closeAfterSelect: true, create: false, copyClassesToDropdown: false, dropdownParent: 'body',
            load: (q, cb) => fetch(`{{ route('api.parties.suppliers') }}?q=${encodeURIComponent(q)}`).then(r => r.json()).then(cb).catch(() => cb())
        });
    }

    // Avanzar estado
    document.addEventListener('click', function (e) {
        const btn = e.target.closest('[data-advance]');
        if (!btn) return;
        const token = document.querySelector('meta[name="csrf-token"]')?.content;
        fetch(`{{ url('part-orders') }}/${btn.dataset.advance}/status`, {
            method: 'POST',
            headers: { 'X-CSRF-TOKEN': token, 'Content-Type': 'application/json', 'Accept': 'application/json' },
            body: JSON.stringify({ status: btn.dataset.next })
        }).then(r => { if (!r.ok) throw new Error(); reload(); }).catch(() => alert('No se pudo actualizar el estado.'));
    });
    </script>
    @endpush
</x-app-layout>
