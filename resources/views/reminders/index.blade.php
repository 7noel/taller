<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">{{ __('Recordatorios') }}</h2>
            <a href="{{ route('follow-ups.index') }}" class="btn btn-secondary">Ver Seguimientos</a>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            @if (session('success'))
                <div class="mb-4 px-4 py-3 bg-green-50 border border-green-200 text-green-700 rounded-lg text-sm">
                    {{ session('success') }}
                </div>
            @endif

            {{-- Pestañas --}}
            <div class="mb-4 flex flex-wrap gap-2">
                <button type="button" data-tab="technical_review"
                        class="reminder-tab inline-flex items-center gap-2 rounded-lg px-4 py-2 text-sm font-semibold transition-colors duration-150 bg-blue-600 text-white focus-visible:ring-2 ring-offset-2 ring-blue-500">
                    Revisión técnica
                </button>
                <button type="button" data-tab="maintenance"
                        class="reminder-tab inline-flex items-center gap-2 rounded-lg px-4 py-2 text-sm font-semibold transition-colors duration-150 bg-white text-gray-700 border border-gray-200 hover:bg-gray-50">
                    Mantenimiento preventivo
                </button>
                <button type="button" data-tab="estimates"
                        class="reminder-tab inline-flex items-center gap-2 rounded-lg px-4 py-2 text-sm font-semibold transition-colors duration-150 bg-white text-gray-700 border border-gray-200 hover:bg-gray-50">
                    Presupuestos en aprobación
                </button>
            </div>

            <div class="card overflow-hidden">
                <div class="p-4 sm:p-5">
                    <div id="reminder-table" class="data-table"></div>
                </div>
            </div>
        </div>
    </div>

    {{-- ===== Modal: Registrar seguimiento ===== --}}
    <div id="followUpModal" class="fixed inset-0 bg-gray-900 bg-opacity-50 hidden items-center justify-center z-50 p-4">
        <div class="bg-white rounded-lg shadow-xl max-w-2xl w-full p-6 max-h-screen overflow-y-auto">
            <div class="flex items-center justify-between gap-3 mb-4">
                <div>
                    <h3 class="text-xl font-bold mb-1 text-gray-800">Registrar seguimiento</h3>
                    <p class="text-sm text-gray-500" id="fu-context">Seguimiento del recordatorio.</p>
                </div>
                <button type="button" id="fu-cancel" class="text-gray-400 hover:text-gray-600">
                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>

            <form method="POST" action="{{ route('follow-ups.store') }}" class="space-y-4">
                @csrf
                <input type="hidden" name="vehicle_id" id="fu-vehicle-id">
                <input type="hidden" name="party_id" id="fu-party-id">
                <input type="hidden" name="estimate_id" id="fu-estimate-id">

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
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
                        <textarea id="fu-notes" name="notes" rows="3" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500" placeholder="Motivo del contacto con el cliente..."></textarea>
                    </div>
                    <div class="md:col-span-2">
                        <label for="fu-next-action" class="block text-sm font-medium text-gray-700">Próxima acción (opcional)</label>
                        <input type="date" id="fu-next-action" name="next_action_date" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                    </div>
                </div>

                <div class="flex gap-2 justify-end pt-2">
                    <button type="button" id="fu-cancel-2" class="btn btn-secondary">Cancelar</button>
                    <button type="submit" class="btn btn-primary" data-loading-text="Guardando...">Guardar seguimiento</button>
                </div>
            </form>
        </div>
    </div>

    {{-- ===== Modal: Ajustar próximo mantenimiento (manual) ===== --}}
    <div id="dateModal" class="fixed inset-0 bg-gray-900 bg-opacity-50 hidden items-center justify-center z-50 p-4">
        <div class="bg-white rounded-lg shadow-xl max-w-md w-full p-6">
            <div class="flex items-center justify-between gap-3 mb-4">
                <div>
                    <h3 class="text-xl font-bold mb-1 text-gray-800">Ajustar próximo mantenimiento</h3>
                    <p class="text-sm text-gray-500" id="dt-context">Ajuste manual (ej. el cliente no usó el vehículo).</p>
                </div>
                <button type="button" id="dt-cancel" class="text-gray-400 hover:text-gray-600">
                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>

            <form method="POST" action="" id="dt-form" class="space-y-4">
                @csrf
                <div>
                    <label for="dt-date" class="block text-sm font-medium text-gray-700">Nueva fecha <span class="text-red-500">*</span></label>
                    <input type="date" id="dt-date" name="next_maintenance_date" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                    <p class="mt-1 text-xs text-gray-500">Al guardar, la fecha queda en modo manual y el cálculo automático no la modificará hasta un nuevo preventivo.</p>
                </div>

                <div class="flex gap-2 justify-end pt-2">
                    <button type="button" id="dt-cancel-2" class="btn btn-secondary">Cancelar</button>
                    <button type="submit" class="btn btn-primary" data-loading-text="Guardando...">Guardar fecha</button>
                </div>
            </form>
        </div>
    </div>


    @push('scripts')
    <script>
    (function () {
        'use strict';

        let currentTab = 'technical_review';

        function esc(value) {
            return String(value ?? '').replace(/[&<>"']/g, function (c) {
                return { '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;' }[c];
            });
        }

        function openModal(id) {
            const el = document.getElementById(id);
            el.classList.remove('hidden');
            el.classList.add('flex');
        }

        function closeModal(id) {
            const el = document.getElementById(id);
            el.classList.add('hidden');
            el.classList.remove('flex');
        }

        function daysBadge(daysLeft) {
            if (daysLeft < 0) return `<span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-50 text-red-700">Vencido (${Math.abs(daysLeft)}d)</span>`;
            if (daysLeft <= 7) return `<span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-amber-50 text-amber-700">${daysLeft} días</span>`;
            return `<span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-50 text-blue-700">${daysLeft} días</span>`;
        }

        function statusBadge(status, label) {
            const map = {
                sent_insurance: 'bg-blue-50 text-blue-700',
                sent_client: 'bg-amber-50 text-amber-700',
                draft: 'bg-gray-100 text-gray-600',
                approved_insurance: 'bg-green-50 text-green-700',
                approved_client: 'bg-green-50 text-green-700',
                rejected_insurance: 'bg-red-50 text-red-700',
                rejected_client: 'bg-red-50 text-red-700'
            };
            const cls = map[status] || 'bg-gray-100 text-gray-600';
            return `<span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium ${cls}">${esc(label)}</span>`;
        }

        function appointmentBadge(has) {
            return has
                ? '<span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-50 text-green-700">Con cita</span>'
                : '<span class="text-gray-400">—</span>';
        }

        function payloadFor(d) {
            return encodeURIComponent(JSON.stringify(d));
        }

        // ===== Acciones por fila (vehículos: revisión técnica / mantenimiento) =====
        function vehicleActions(d, type) {
            const payload = payloadFor(d);
            const serviceType = type === 'maintenance' ? 'preventivo' : 'otro';
            const reason = type === 'maintenance'
                ? 'Mantenimiento preventivo programado'
                : 'Revisión técnica programada';
            const cita = `/appointments/create?vehicle_id=${d.id}&party_id=${d.party_id || ''}&party_name=${encodeURIComponent(d.contact_name || '')}&contact_name=${encodeURIComponent(d.contact_name || '')}&contact_phone=${encodeURIComponent(d.contact_phone || '')}&service_type=${serviceType}&reason=${encodeURIComponent(reason)}&scheduled_date=`;

            let html = `
                <a href="/vehicles/${d.id}/history" title="Historial del vehículo" class="btn-icon btn-icon-blue">
                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </a>
                <button type="button" onclick="window.openFollowUp('${payload}')" title="Registrar seguimiento" class="btn-icon btn-icon-amber">
                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/></svg>
                </button>
                <a href="${cita}" title="Agendar cita" class="btn-icon btn-icon-blue">
                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                </a>`;
            if (type === 'maintenance') {
                html += `
                <button type="button" onclick="window.openDateModal('${payload}')" title="Ajustar fecha manualmente" class="btn-icon btn-icon-amber">
                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                </button>`;
            }
            return `<div class="flex gap-1.5 justify-center">${html}</div>`;
        }

        // ===== Acciones por fila (presupuestos) =====
        function estimateActions(d) {
            const payload = payloadFor(d);
            return `
                <a href="/estimates/${d.id}" title="Ver presupuesto" class="btn-icon btn-icon-blue">
                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                </a>
                <button type="button" onclick="window.openFollowUp('${payload}')" title="Registrar seguimiento" class="btn-icon btn-icon-amber">
                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/></svg>
                </button>`;
        }

        function columnsFor(tab) {
            if (tab === 'estimates') {
                return [
                    { title: 'Presupuesto', field: 'document_sn', width: 150, formatter: c => `<a href="/estimates/${c.getData().id}" class="font-mono text-sm text-blue-600 hover:underline">${esc(c.getValue())}</a>` },
                    { title: 'Estado', field: 'status_label', width: 160, formatter: c => statusBadge(c.getData().status, c.getValue()) },
                    { title: 'Tipo', field: 'service_type_label', width: 110 },
                    { title: 'Vehículo', field: 'plate', minWidth: 150, formatter: c => {
                        const d = c.getData();
                        return `<div class="text-gray-800 font-medium">${esc(d.plate || '—')}</div><div class="text-xs text-gray-500">${esc(d.vehicle_label || '')}</div>`;
                    } },
                    { title: 'Cliente / Seguro', field: 'client_name', minWidth: 180, formatter: c => {
                        const d = c.getData();
                        return `<div class="text-gray-800">${esc(d.client_name || '—')}</div><div class="text-xs text-gray-500">${esc(d.insurance_name || '')}</div>`;
                    } },
                    { title: 'Total', field: 'total', width: 100, hozAlign: 'right' },
                    { title: 'Esperando', field: 'days_waiting', width: 110, hozAlign: 'center', formatter: c => daysBadge(-c.getValue()) },
                    { title: 'Enviado', field: 'last_sent_at', width: 100, hozAlign: 'center' },
                    { title: 'Acciones', field: 'id', width: 120, hozAlign: 'center', headerSort: false, formatter: c => estimateActions(c.getData()) }
                ];
            }

            return [
                { title: 'Vehículo', field: 'plate', minWidth: 180, formatter: c => {
                    const d = c.getData();
                    const manual = d.manual_source ? ' · <span class="text-amber-600">fecha manual</span>' : '';
                    return `<div class="text-gray-800 font-medium">${esc(d.plate)}</div><div class="text-xs text-gray-500">${esc(d.vehicle_label || '')}${manual}</div>`;
                } },
                { title: 'Fecha límite', field: 'due_date', width: 120, hozAlign: 'center' },
                { title: 'Plazo', field: 'days_left', width: 120, hozAlign: 'center', formatter: c => daysBadge(c.getValue()) },
                { title: 'Contacto', field: 'contact_name', minWidth: 180, formatter: c => {
                    const d = c.getData();
                    return `<div class="text-gray-800">${esc(d.contact_name || '—')}</div><div class="text-xs text-gray-500">${esc(d.contact_phone || '')}</div>`;
                } },
                { title: 'Cita', field: 'has_appointment', width: 100, hozAlign: 'center', formatter: c => appointmentBadge(c.getValue()) },
                { title: 'Acciones', field: 'id', width: 190, hozAlign: 'center', headerSort: false, formatter: c => vehicleActions(c.getData(), currentTab) }
            ];
        }

        const reminderTable = new Tabulator('#reminder-table', {
            layout: 'fitColumns',
            responsiveLayout: 'collapse',
            height: 'auto',
            placeholder: 'No hay recordatorios en este momento',
            ajaxURL: "{{ route('api.reminders.search') }}?tab=technical_review",
            columns: columnsFor('technical_review')
        });

        function switchTab(tab) {
            currentTab = tab;
            document.querySelectorAll('.reminder-tab').forEach(function (btn) {
                const active = btn.dataset.tab === tab;
                btn.className = 'reminder-tab inline-flex items-center gap-2 rounded-lg px-4 py-2 text-sm font-semibold transition-colors duration-150 ' +
                    (active ? 'bg-blue-600 text-white focus-visible:ring-2 ring-offset-2 ring-blue-500' : 'bg-white text-gray-700 border border-gray-200 hover:bg-gray-50');
            });
            reminderTable.setColumns(columnsFor(tab));
            reminderTable.setData("{{ route('api.reminders.search') }}?tab=" + tab);
        }

        document.querySelectorAll('.reminder-tab').forEach(function (btn) {
            btn.addEventListener('click', function () { switchTab(btn.dataset.tab); });
        });

        // ===== Modal seguimiento =====
        window.openFollowUp = function (payload) {
            const d = JSON.parse(decodeURIComponent(payload));
            document.getElementById('fu-vehicle-id').value = d.vehicle_id || '';
            document.getElementById('fu-party-id').value = d.party_id || '';
            document.getElementById('fu-estimate-id').value = d.estimate_id || '';
            document.getElementById('fu-notes').value = d.reminder_note || '';
            document.getElementById('fu-context').textContent = d.context || 'Seguimiento del recordatorio.';
            openModal('followUpModal');
        };

        document.getElementById('fu-cancel').addEventListener('click', function () { closeModal('followUpModal'); });
        document.getElementById('fu-cancel-2').addEventListener('click', function () { closeModal('followUpModal'); });

        // ===== Modal ajustar fecha (mantenimiento manual) =====
        window.openDateModal = function (payload) {
            const d = JSON.parse(decodeURIComponent(payload));
            document.getElementById('dt-form').action = '/vehicles/' + d.id + '/maintenance-date';
            document.getElementById('dt-date').value = '';
            document.getElementById('dt-context').textContent = d.plate + ' · ajuste manual del próximo mantenimiento preventivo.';
            openModal('dateModal');
        };

        document.getElementById('dt-cancel').addEventListener('click', function () { closeModal('dateModal'); });
        document.getElementById('dt-cancel-2').addEventListener('click', function () { closeModal('dateModal'); });

        ['followUpModal', 'dateModal'].forEach(function (id) {
            const el = document.getElementById(id);
            el.addEventListener('click', function (e) { if (e.target === el) closeModal(id); });
        });
        document.addEventListener('keydown', function (e) {
            if (e.key === 'Escape') { closeModal('followUpModal'); closeModal('dateModal'); }
        });
    })();
    </script>
    @endpush
</x-app-layout>
