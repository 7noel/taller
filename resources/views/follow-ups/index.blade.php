<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">{{ __('Seguimientos') }}</h2>
            <button type="button" id="btn-new-followup" class="btn btn-primary">
                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/>
                </svg>
                Nuevo Seguimiento
            </button>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            @if (session('success'))
                <div class="mb-4 px-4 py-3 bg-green-50 border border-green-200 text-green-700 rounded-lg text-sm">
                    {{ session('success') }}
                </div>
            @endif

            <div class="card overflow-hidden">
                <div class="p-4 sm:p-5">
                    <div class="mb-3 flex flex-col md:flex-row md:items-end gap-3">
                        <div class="relative max-w-md flex-1">
                            <svg class="pointer-events-none absolute left-3 top-1/2 h-4 w-4 -translate-y-1/2 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                            </svg>
                            <input type="text" id="followup-search" placeholder="Buscar por placa, cliente o notas..." class="search-input">
                        </div>
                        <label class="inline-flex items-center gap-2 text-sm font-medium text-gray-700">
                            <input type="checkbox" id="filter-pending" class="rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                            Solo pendientes
                        </label>
                    </div>
                    <div id="followup-table" class="data-table"></div>
                </div>
            </div>
        </div>
    </div>

    {{-- ===== Modal: Nuevo seguimiento ===== --}}
    <div id="followUpModal" class="fixed inset-0 bg-gray-900 bg-opacity-50 hidden items-center justify-center z-50 p-4">
        <div class="bg-white rounded-lg shadow-xl max-w-2xl w-full p-6 max-h-screen overflow-y-auto">
            <div class="flex items-center justify-between gap-3 mb-4">
                <div>
                    <h3 class="text-xl font-bold mb-1 text-gray-800">Nuevo seguimiento</h3>
                    <p class="text-sm text-gray-500">Registre una llamada, WhatsApp, email o visita de seguimiento.</p>
                </div>
                <button type="button" id="fu-cancel" class="text-gray-400 hover:text-gray-600">
                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>

            <form method="POST" action="{{ route('follow-ups.store') }}">
                @csrf
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label for="fu-vehicle" class="block text-sm font-medium text-gray-700">Vehículo</label>
                        <select id="fu-vehicle" name="vehicle_id" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm"></select>
                    </div>
                    <div>
                        <label for="fu-party" class="block text-sm font-medium text-gray-700">Cliente / Contacto</label>
                        <select id="fu-party" name="party_id" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm"></select>
                    </div>
                    <div>
                        <label for="fu-date" class="block text-sm font-medium text-gray-700">Fecha <span class="text-red-500">*</span></label>
                        <input type="date" id="fu-date" name="date" value="{{ now()->format('Y-m-d') }}" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                    </div>
                    <div>
                        <label for="fu-type" class="block text-sm font-medium text-gray-700">Tipo <span class="text-red-500">*</span></label>
                        <select id="fu-type" name="type" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                            @foreach (\App\Models\FollowUp::TYPE_LABELS as $value => $label)
                                <option value="{{ $value }}">{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="md:col-span-2">
                        <label for="fu-notes" class="block text-sm font-medium text-gray-700">Notas</label>
                        <textarea id="fu-notes" name="notes" rows="3" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"></textarea>
                    </div>
                    <div>
                        <label for="fu-next" class="block text-sm font-medium text-gray-700">Próxima acción</label>
                        <input type="date" id="fu-next" name="next_action_date" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                    </div>
                    <div class="flex items-end pb-1">
                        <label class="inline-flex items-center gap-2 text-sm font-medium text-gray-700">
                            <input type="checkbox" name="done" value="1" class="rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                            Ya realizado
                        </label>
                    </div>
                </div>

                <div class="mt-6 flex gap-2 justify-end">
                    <button type="button" id="fu-cancel-2" class="btn btn-secondary">Cancelar</button>
                    <button type="submit" class="btn btn-primary">Guardar seguimiento</button>
                </div>
            </form>
        </div>
    </div>
    @push('scripts')
    <script>
        (function () {
            'use strict';
            try {
                const modal = document.getElementById('followUpModal');

                document.getElementById('btn-new-followup').addEventListener('click', function () {
                    modal.classList.remove('hidden');
                    modal.classList.add('flex');
                    document.getElementById('fu-date').focus();
                });

                function closeModal() {
                    modal.classList.add('hidden');
                    modal.classList.remove('flex');
                }
                document.getElementById('fu-cancel').addEventListener('click', closeModal);
                document.getElementById('fu-cancel-2').addEventListener('click', closeModal);

                // ===== Tom Select: vehículo y cliente =====
                new TomSelect('#fu-vehicle', {
                    valueField: 'id', labelField: 'plate', searchField: ['plate'],
                    maxItems: 1, closeAfterSelect: true, create: false, copyClassesToDropdown: false,
                    placeholder: 'Buscar por placa...',
                    load: function (query, callback) {
                        if (query.length < 1) return callback();
                        fetch(`/api/vehicles/search?q=${encodeURIComponent(query)}&limit=100`)
                            .then(r => r.json()).then(data => callback(data)).catch(() => callback());
                    },
                    render: { option: function (item, escape) {
                        return `<div><span class="font-medium">${escape(item.plate)}</span>${item.brand ? ` <span class="text-gray-500">· ${escape(item.brand)} ${escape(item.model || '')}</span>` : ''}</div>`;
                    } },
                    onItemAdd: function () { this.blur(); this.close(); },
                    onDropdownOpen: function () {
                        if (this.items.length) { this.setTextValue(''); const i = this.input; if (i && i.setSelectionRange) i.setSelectionRange(0, 0); }
                    }
                });

                new TomSelect('#fu-party', {
                    valueField: 'id', labelField: 'display_name', searchField: ['display_name', 'document_number'],
                    maxItems: 1, closeAfterSelect: true, create: false, copyClassesToDropdown: false,
                    placeholder: 'Buscar cliente...',
                    load: function (query, callback) {
                        fetch(`/api/parties/search?q=${encodeURIComponent(query)}&limit=100`)
                            .then(r => r.json()).then(data => callback(data)).catch(() => callback());
                    },
                    render: { option: function (item, escape) {
                        return `<div><span class="font-medium">${escape(item.display_name)}</span>${item.document_number ? ` <span class="text-gray-500">· ${escape(item.document_number)}</span>` : ''}</div>`;
                    } },
                    onItemAdd: function () { this.blur(); this.close(); },
                    onDropdownOpen: function () {
                        if (this.items.length) { this.setTextValue(''); const i = this.input; if (i && i.setSelectionRange) i.setSelectionRange(0, 0); }
                    }
                });
            } catch (error) {
                console.error('[followup] Error inicializando:', error);
            }
        })();
        const typeBadge = (type, label) => {
            const map = { call: 'bg-blue-50 text-blue-700', whatsapp: 'bg-green-50 text-green-700', email: 'bg-amber-50 text-amber-700', visit: 'bg-purple-50 text-purple-700' };
            const cls = map[type] || 'bg-gray-100 text-gray-600';
            return `<span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium ${cls}">${label}</span>`;
        };

        const followUpTable = new Tabulator('#followup-table', {
            ajaxURL: "{{ route('api.follow-ups.search') }}?limit=200",
            layout: 'fitColumns', responsiveLayout: 'collapse', height: 'auto',
            placeholder: 'No hay seguimientos registrados',
            columns: [
                { title: 'Fecha', field: 'date', width: 100 },
                { title: 'Tipo', field: 'type_label', width: 110, hozAlign: 'center', formatter: c => typeBadge(c.getData().type, c.getValue()) },
                { title: 'Cliente / Vehículo', field: 'plate', minWidth: 160, formatter: c => {
                    const d = c.getData();
                    return `<div class="text-gray-800">${d.party_name || d.plate || '—'}</div><div class="text-xs text-gray-500">${d.plate ? d.plate + (d.vehicle_label ? ' · ' + d.vehicle_label : '') : ''}</div>`;
                } },
                { title: 'Notas', field: 'notes', minWidth: 220, formatter: c => c.getValue() || '—' },
                { title: 'Próxima acción', field: 'next_action_date', width: 120, hozAlign: 'center', formatter: c => c.getValue() || '—' },
                { title: 'Estado', field: 'done', width: 100, hozAlign: 'center', formatter: c => c.getValue()
                    ? '<span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-50 text-green-700">Hecho</span>'
                    : '<span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-amber-50 text-amber-700">Pendiente</span>' },
                { title: 'Acciones', field: 'id', width: 150, hozAlign: 'center', headerSort: false, formatter: function (cell) {
                    const d = cell.getData();
                    let actions = '';
                    if (!d.done) {
                        actions += `<form method="POST" action="/follow-ups/${d.id}/done" class="inline">
                            @csrf
                            <button type="submit" title="Marcar como realizado" class="btn-icon btn-icon-blue">
                                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                            </button>
                        </form>`;
                    }
                    actions += `<form method="POST" action="/follow-ups/${d.id}" class="inline" data-confirm="¿Eliminar este seguimiento?">
                        @csrf
                        @method('DELETE')
                        <button type="submit" title="Eliminar seguimiento" class="btn-icon btn-icon-red">
                            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                        </button>
                    </form>`;
                    return `<div class="flex gap-1.5 justify-center">${actions}</div>`;
                } }
            ]
        });

        function reloadFollowUps() {
            const params = new URLSearchParams({ limit: 200 });
            if (document.getElementById('followup-search').value) params.set('q', document.getElementById('followup-search').value);
            if (document.getElementById('filter-pending').checked) params.set('pending', '1');
            followUpTable.setData("{{ route('api.follow-ups.search') }}?" + params.toString());
        }

        document.getElementById('followup-search').addEventListener('input', reloadFollowUps);
        document.getElementById('filter-pending').addEventListener('change', reloadFollowUps);
    </script>
    @endpush
</x-app-layout>


