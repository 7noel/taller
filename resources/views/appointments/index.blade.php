<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">{{ __('Citas') }}</h2>
            <a href="{{ route('appointments.create') }}" class="btn btn-primary">
                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/>
                </svg>
                Nueva Cita
            </a>
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
                            <input type="text" id="appointment-search" placeholder="Buscar por placa, contacto o teléfono..." class="search-input">
                        </div>
                        <div class="flex items-end gap-2">
                            <div>
                                <label for="filter-from" class="block text-sm font-medium text-gray-700">Desde</label>
                                <input type="date" id="filter-from" value="{{ $from }}" class="mt-1 block rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm">
                            </div>
                            <div>
                                <label for="filter-to" class="block text-sm font-medium text-gray-700">Hasta</label>
                                <input type="date" id="filter-to" value="{{ $to }}" class="mt-1 block rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm">
                            </div>
                        </div>
                    </div>
                    <div id="appointment-table" class="data-table"></div>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        const statusBadge = (status, label) => {
            const map = {
                scheduled: 'bg-blue-50 text-blue-700',
                confirmed: 'bg-green-50 text-green-700',
                cancelled: 'bg-red-50 text-red-700',
                completed: 'bg-gray-100 text-gray-600'
            };
            const cls = map[status] || 'bg-gray-100 text-gray-600';
            return `<span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium ${cls}">${label}</span>`;
        };

        const appointmentTable = new Tabulator('#appointment-table', {
            ajaxURL: "{{ route('api.appointments.search') }}?from={{ $from }}&to={{ $to }}&limit=200",
            layout: 'fitColumns',
            responsiveLayout: 'collapse',
            placeholder: 'No hay citas en el rango seleccionado',
            height: 'auto',
            columns: [
                {
                    title: 'Fecha / Hora', field: 'scheduled_at', minWidth: 120,
                    formatter: function (cell) {
                        const d = cell.getData();
                        return `<div class="font-medium text-gray-800">${d.scheduled_date || ''}</div><div class="text-xs text-gray-500">${d.time || ''}</div>`;
                    }
                },
                {
                    title: 'Vehículo', field: 'plate', minWidth: 140,
                    formatter: function (cell) {
                        const d = cell.getData();
                        return d.plate
                            ? `<div class="font-medium text-gray-800">${d.plate}</div><div class="text-xs text-gray-500">${d.vehicle_label || ''}</div>`
                            : '<span class="text-gray-400">—</span>';
                    }
                },
                {
                    title: 'Contacto', field: 'contact_name', minWidth: 150,
                    formatter: function (cell) {
                        const d = cell.getData();
                        return `<div class="text-gray-800">${d.contact_name || '—'}</div><div class="text-xs text-gray-500">${d.contact_phone || ''}</div>`;
                    }
                },
                { title: 'Asesor', field: 'advisor_name', minWidth: 120, formatter: c => c.getValue() || '—' },
                { title: 'Tipo', field: 'service_type_label', width: 110, formatter: c => c.getValue() || '—' },
                { title: 'Estado', field: 'status', width: 110, hozAlign: 'center', formatter: c => statusBadge(c.getValue(), c.getData().status_label) },
                {
                    title: 'Ingreso', field: 'check_in_sn', width: 130, hozAlign: 'center',
                    formatter: function (cell) {
                        const d = cell.getData();
                        if (!d.check_in_sn) return '<span class="text-xs text-gray-400">Sin cita</span>';
                        return `<a href="/check-ins/${d.check_in_id}" class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-50 text-blue-700 hover:bg-blue-100">${d.check_in_sn}</a>`;
                    }
                },
                {
                    title: 'Acciones', field: 'id', width: 190, hozAlign: 'center', headerSort: false,
                    formatter: function (cell) {
                        const d = cell.getData();
                        let actions = `
                            <a href="/appointments/${d.id}" title="Ver cita" class="btn-icon btn-icon-blue">
                                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                            </a>
                            <a href="/appointments/${d.id}/edit" title="Editar cita" class="btn-icon btn-icon-amber">
                                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                            </a>`;
                        if (d.status === 'scheduled' || d.status === 'confirmed') {
                            actions += `<form method="POST" action="/appointments/${d.id}/cancel" class="inline" data-confirm="¿Cancelar esta cita?">
                                @csrf
                                <button type="submit" title="Cancelar cita" class="btn-icon btn-icon-red">
                                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                                </button>
                            </form>`;
                        }
                        actions += `<form method="POST" action="/appointments/${d.id}" class="inline" data-confirm="¿Eliminar esta cita?">
                            @csrf
                            @method('DELETE')
                            <button type="submit" title="Eliminar cita" class="btn-icon btn-icon-red">
                                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                            </button>
                        </form>`;
                        return `<div class="flex gap-1.5 justify-center">${actions}</div>`;
                    }
                }
            ]
        });

        function reloadAppointments() {
            const params = new URLSearchParams({ limit: 200 });
            if (document.getElementById('filter-from').value) params.set('from', document.getElementById('filter-from').value);
            if (document.getElementById('filter-to').value) params.set('to', document.getElementById('filter-to').value);
            if (document.getElementById('appointment-search').value) params.set('q', document.getElementById('appointment-search').value);
            appointmentTable.setData("{{ route('api.appointments.search') }}?" + params.toString());
        }

        document.getElementById('appointment-search').addEventListener('input', reloadAppointments);
        document.getElementById('filter-from').addEventListener('change', reloadAppointments);
        document.getElementById('filter-to').addEventListener('change', reloadAppointments);
    </script>
    @endpush
</x-app-layout>

