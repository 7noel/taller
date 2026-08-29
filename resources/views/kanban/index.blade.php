<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-wrap justify-between items-center gap-3">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">{{ __('Tablero Kanban') }}</h2>
            <div class="flex flex-wrap gap-2">
                <button type="button" id="kanban-refresh" class="btn btn-secondary">
                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                    </svg>
                    Actualizar
                </button>
                @can('crear inventarios')
                    <a href="{{ route('check-ins.create') }}" class="btn btn-primary">
                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/>
                        </svg>
                        + Inventario
                    </a>
                @endcan
            </div>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="px-4 sm:px-6 lg:px-8">
            @if (session('success'))
                <div class="mb-4 px-4 py-3 bg-green-50 border border-green-200 text-green-700 rounded-lg text-sm">{{ session('success') }}</div>
            @endif

            {{-- Buscador por placa / documento --}}
            <div class="mb-4">
                <div class="relative max-w-md">
                    <svg class="pointer-events-none absolute left-3 top-1/2 h-4 w-4 -translate-y-1/2 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                    </svg>
                    <input type="text" id="kanban-search" placeholder="Buscar por placa, VIN o documento..." class="search-input">
                </div>
                <div id="kanban-search-summary" class="mt-2 text-sm text-gray-500"></div>
            </div>

            {{-- Chips resumen por columna --}}
            <div id="kanban-chips" class="flex flex-wrap gap-2 mb-4"></div>

            {{-- Tablero (scroll horizontal) --}}
            <div id="kanban-board" class="flex gap-4 overflow-x-auto pb-4">
                <div class="text-sm text-gray-500 py-8">Cargando tablero...</div>
            </div>

            <div id="kanban-empty" class="hidden text-center py-16">
                <p class="text-sm text-gray-500">No hay elementos en el tablero.</p>
            </div>
        </div>
    </div>

    {{-- Modal de motivo (rechazos) --}}
    <div id="kanbanReasonModal" class="fixed inset-0 bg-gray-900 bg-opacity-50 hidden items-center justify-center z-50 p-4">
        <div class="bg-white rounded-lg shadow-xl max-w-md w-full p-6">
            <div class="flex items-start gap-3">
                <div class="shrink-0 inline-flex h-10 w-10 items-center justify-center rounded-full bg-amber-50">
                    <svg class="h-5 w-5 text-amber-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v4m0 4h.01M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z"/>
                    </svg>
                </div>
                <div class="min-w-0 w-full">
                    <h3 class="text-lg font-bold text-gray-800">Motivo de la acción</h3>
                    <p class="mt-1 text-sm text-gray-600">Indica el motivo para registrar la transición.</p>
                    <textarea id="kanbanReasonTextarea" rows="3" maxlength="500" class="mt-3 w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm"></textarea>
                    <p id="kanbanReasonError" class="hidden mt-1 text-sm text-red-600">El motivo es obligatorio.</p>
                </div>
            </div>
            <div class="mt-6 flex gap-2 justify-end">
                <button type="button" id="kanbanReasonCancel" class="btn btn-secondary">Cancelar</button>
                <button type="button" id="kanbanReasonOk" class="btn btn-primary">Confirmar</button>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
    const API_URL = "{{ route('api.kanban.data') }}";
    const COLUMN_TITLES = { inventario: 'Inventario', presupuesto: 'Presupuesto', aprobacion: 'Aprobación', reparacion: 'Reparación', control_calidad: 'Control de Calidad', entrega: 'Entrega' };
    const DOT_COLORS = { inventario: 'bg-blue-500', presupuesto: 'bg-amber-500', aprobacion: 'bg-violet-500', reparacion: 'bg-sky-500', control_calidad: 'bg-purple-500', entrega: 'bg-green-500' };
    const TYPE_LABELS = { check_in: 'Inventario', estimate: 'Presupuesto', work_order: 'OT' };
    const STATUS_COLORS = {
        draft: 'bg-gray-100 text-gray-700',
        pending_approval: 'bg-amber-50 text-amber-700',
        approved: 'bg-green-50 text-green-700',
        approved_insurance: 'bg-green-50 text-green-700',
        approved_client: 'bg-green-50 text-green-700',
        rejected: 'bg-red-50 text-red-700',
        rejected_insurance: 'bg-red-50 text-red-700',
        rejected_client: 'bg-red-50 text-red-700',
        sent_insurance: 'bg-blue-50 text-blue-700',
        sent_client: 'bg-blue-50 text-blue-700',
        open: 'bg-gray-100 text-gray-700',
        in_progress: 'bg-blue-50 text-blue-700',
        waiting_parts: 'bg-amber-50 text-amber-700',
        quality_control: 'bg-purple-50 text-purple-700',
        ready_for_delivery: 'bg-teal-50 text-teal-700',
        delivered: 'bg-green-50 text-green-700',
        delivered_pending: 'bg-orange-50 text-orange-700',
    };

    function escapeHtml(s) {
        return String(s ?? '').replace(/[&<>"']/g, c => ({ '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;' }[c]));
    }
    function escapeAttr(s) {
        return String(s ?? '').replace(/&/g, '&amp;').replace(/"/g, '&quot;');
    }

    function renderCard(card) {
        const statusColor = STATUS_COLORS[card.status] || 'bg-gray-100 text-gray-700';
        const typeLabel = TYPE_LABELS[card.type] || card.type;
        const last = card.last_action;
        const searchText = escapeAttr((card.plate + ' ' + (card.document_sn || '') + ' ' + (card.client || '') + ' ' + (card.vehicle_label || '')).toLowerCase());
        const actions = (card.actions || []).length
            ? card.actions.map(a =>
                '<button type="button" class="kanban-action block w-full text-left px-3 py-2 text-sm text-gray-700 hover:bg-blue-50" data-action="' + escapeAttr(JSON.stringify(a)) + '">' + escapeHtml(a.label) + '</button>'
              ).join('')
            : '<div class="px-3 py-2 text-xs text-gray-400">Sin acciones</div>';

        return '' +
        '<div class="kanban-card bg-white rounded-lg border border-gray-200 shadow-sm p-3 space-y-2" data-card data-column="' + card.column + '" data-search="' + searchText + '">' +
            '<div class="flex items-start justify-between gap-2">' +
                '<a href="' + escapeAttr(card.show_url) + '" class="font-bold text-sm uppercase text-gray-900 hover:text-blue-600 truncate" title="Ver ' + escapeHtml(typeLabel) + '">' + escapeHtml(card.plate || '—') + '</a>' +
                '<div class="flex items-center gap-1.5 shrink-0">' +
                    '<span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium ' + statusColor + '">' + escapeHtml(card.status_label) + '</span>' +
                    '<div class="relative">' +
                        '<button type="button" class="kanban-menu-btn h-7 w-7 inline-flex items-center justify-center rounded-md text-gray-500 hover:bg-gray-100 hover:text-gray-700" title="Acciones">' +
                            '<svg class="h-4 w-4" fill="currentColor" viewBox="0 0 24 24"><path d="M12 5a1.5 1.5 0 110 3 1.5 1.5 0 010-3zm0 5.5a1.5 1.5 0 110 3 1.5 1.5 0 010-3zm0 5.5a1.5 1.5 0 110 3 1.5 1.5 0 010-3z"/></svg>' +
                        '</button>' +
                        '<div class="kanban-menu hidden absolute right-0 top-8 z-30 w-60 max-h-80 overflow-y-auto bg-white rounded-lg border border-gray-200 shadow-lg py-1">' + actions + '</div>' +
                    '</div>' +
                '</div>' +
            '</div>' +
            (card.vehicle_label ? '<div class="text-sm text-gray-600 truncate">' + escapeHtml(card.vehicle_label) + '</div>' : '') +
            (card.client ? '<div class="text-sm text-gray-500 truncate">' + escapeHtml(card.client) + '</div>' : '') +
            '<div class="flex items-center gap-1.5 text-xs">' +
                '<span class="inline-flex items-center px-1.5 py-0.5 rounded bg-gray-100 text-gray-600 font-medium">' + escapeHtml(card.document_sn || '—') + '</span>' +
                '<span class="text-gray-400">· ' + escapeHtml(typeLabel) + '</span>' +
            '</div>' +
            (last
                ? '<div class="rounded-md bg-gray-50 border border-gray-100 px-2 py-1.5">' +
                    '<div class="flex items-center gap-1 text-xs font-medium text-gray-700">' +
                        '<svg class="h-3 w-3 text-gray-400 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>' +
                        escapeHtml(last.text) +
                    '</div>' +
                    '<div class="mt-0.5 pl-4 text-xs text-gray-500">' + (last.by ? 'por ' + escapeHtml(last.by) + ' · ' : '') + escapeHtml(last.human || '') + '</div>' +
                  '</div>'
                : '<div class="text-xs text-gray-400">Sin transiciones registradas.</div>') +
            (card.next_action ? '<div class="text-xs text-blue-700"><span class="font-semibold">Próximo paso:</span> ' + escapeHtml(card.next_action) + '</div>' : '') +
        '</div>';
    }

    function render(data) {
        const board = document.getElementById('kanban-board');
        const chips = document.getElementById('kanban-chips');
        const empty = document.getElementById('kanban-empty');

        if (!data.total) {
            board.innerHTML = '';
            chips.innerHTML = '';
            empty.classList.remove('hidden');
            return;
        }
        empty.classList.add('hidden');

        chips.innerHTML = data.columns.map(col =>
            '<button type="button" class="kanban-chip inline-flex items-center gap-1.5 rounded-lg px-3 py-1.5 text-sm font-medium border border-gray-200 bg-white text-gray-700 hover:bg-gray-50" data-col="' + col.key + '">' +
                '<span class="h-2 w-2 rounded-full ' + DOT_COLORS[col.key] + '"></span>' +
                escapeHtml(col.title) +
                '<span class="font-semibold">' + col.count + '</span>' +
            '</button>'
        ).join('');

        board.innerHTML = data.columns.map(col =>
            '<div class="kanban-column flex-1 min-w-[250px] shrink-0" id="col-' + col.key + '">' +
                '<div class="flex items-center justify-between gap-2 mb-2 px-1">' +
                    '<div class="flex items-center gap-2">' +
                        '<span class="h-2.5 w-2.5 rounded-full ' + DOT_COLORS[col.key] + '"></span>' +
                        '<h3 class="text-xs font-semibold uppercase tracking-wider text-gray-500">' + escapeHtml(col.title) + '</h3>' +
                    '</div>' +
                    '<span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-semibold bg-gray-100 text-gray-700">' + col.count + '</span>' +
                '</div>' +
                '<div class="space-y-3 pb-2">' +
                    (col.cards.length
                        ? col.cards.map(renderCard).join('')
                        : '<div class="rounded-lg border border-dashed border-gray-200 p-4 text-center text-xs text-gray-400">Sin elementos</div>') +
                '</div>' +
            '</div>'
        ).join('');

        applyFilter();
    }

    async function loadBoard() {
        const board = document.getElementById('kanban-board');
        board.innerHTML = '<div class="text-sm text-gray-500 py-8">Cargando tablero...</div>';
        try {
            const res = await fetch(API_URL, { headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' }, credentials: 'same-origin', cache: 'no-store' });
            if (!res.ok) throw new Error('HTTP ' + res.status);
            render(await res.json());
        } catch (e) {
            board.innerHTML = '<div class="text-sm text-red-600 py-8">No se pudo cargar el tablero. Intenta de nuevo.</div>';
        }
    }

    document.getElementById('kanban-refresh').addEventListener('click', loadBoard);

    // ===== Menús de acciones (⋮) =====
    function closeAllMenus() {
        document.querySelectorAll('.kanban-menu').forEach(m => m.classList.add('hidden'));
    }

    document.addEventListener('click', function (e) {
        const btn = e.target.closest('.kanban-menu-btn');
        if (btn) {
            const menu = btn.parentElement.querySelector('.kanban-menu');
            const wasOpen = menu && !menu.classList.contains('hidden');
            closeAllMenus();
            if (menu && !wasOpen) menu.classList.remove('hidden');
            return;
        }
        if (!e.target.closest('.kanban-menu')) closeAllMenus();
    });
    document.addEventListener('keydown', function (e) { if (e.key === 'Escape') closeAllMenus(); });
    document.addEventListener('scroll', closeAllMenus, true);

    // ===== Ejecución de acciones (reusa form-guard + confirm-modal) =====
    let pendingReason = null;

    function executeAction(action) {
        if (action.method === 'GET') { window.location.href = action.url; return; }

        const form = document.createElement('form');
        form.method = 'POST';
        form.action = action.url;
        form.style.display = 'none';

        const token = document.createElement('input');
        token.type = 'hidden'; token.name = '_token';
        token.value = (document.querySelector('meta[name="csrf-token"]') || {}).content || '';
        form.appendChild(token);

        Object.entries(action.fields || {}).forEach(function (entry) {
            const inp = document.createElement('input');
            inp.type = 'hidden'; inp.name = entry[0]; inp.value = entry[1];
            form.appendChild(inp);
        });

        const submitBtn = document.createElement('button');
        submitBtn.type = 'submit'; submitBtn.style.display = 'none';
        form.appendChild(submitBtn);

        if (action.reason) {
            const reasonInput = document.createElement('input');
            reasonInput.type = 'hidden'; reasonInput.name = 'reason';
            form.appendChild(reasonInput);
            pendingReason = { form: form, submitBtn: submitBtn, input: reasonInput, required: !!action.reason_required };
            openReasonModal();
            return;
        }

        document.body.appendChild(form);
        if (action.confirm) {
            window.ConfirmModal.open(form, { message: action.confirm, confirmLabel: 'Confirmar' });
        } else {
            form.requestSubmit(submitBtn);
        }
    }

    document.addEventListener('click', function (e) {
        const btn = e.target.closest('.kanban-action');
        if (!btn) return;
        e.preventDefault();
        closeAllMenus();
        let action;
        try { action = JSON.parse(btn.dataset.action); } catch (err) { return; }
        executeAction(action);
    });

    // ===== Modal de motivo (rechazos) =====
    function openReasonModal() {
        const modal = document.getElementById('kanbanReasonModal');
        document.getElementById('kanbanReasonTextarea').value = '';
        document.getElementById('kanbanReasonError').classList.add('hidden');
        modal.classList.remove('hidden');
        modal.classList.add('flex');
        setTimeout(() => document.getElementById('kanbanReasonTextarea').focus(), 50);
    }
    function closeReasonModal() {
        document.getElementById('kanbanReasonModal').classList.add('hidden');
        document.getElementById('kanbanReasonModal').classList.remove('flex');
    }
    document.getElementById('kanbanReasonOk').addEventListener('click', function () {
        if (!pendingReason) return;
        const val = document.getElementById('kanbanReasonTextarea').value.trim();
        const err = document.getElementById('kanbanReasonError');
        if (pendingReason.required && !val) { err.classList.remove('hidden'); return; }
        err.classList.add('hidden');
        pendingReason.input.value = val;
        document.body.appendChild(pendingReason.form);
        closeReasonModal();
        const form = pendingReason.form, btn = pendingReason.submitBtn;
        pendingReason = null;
        form.requestSubmit(btn);
    });
    document.getElementById('kanbanReasonCancel').addEventListener('click', function () { pendingReason = null; closeReasonModal(); });
    document.getElementById('kanbanReasonModal').addEventListener('click', function (e) {
        if (e.target === this) { pendingReason = null; closeReasonModal(); }
    });

    // ===== Buscador por placa / documento =====
    const searchInput = document.getElementById('kanban-search');
    let highlightTimer = null;

    function applyFilter() {
        const q = searchInput.value.trim().toLowerCase();
        const cards = document.querySelectorAll('.kanban-card');
        let total = 0;
        const perCol = {};
        cards.forEach(card => {
            const match = q.length < 2 || (card.dataset.search || '').includes(q);
            card.classList.toggle('hidden', !match);
            if (match) {
                total++;
                const col = card.dataset.column;
                perCol[col] = (perCol[col] || 0) + 1;
            }
        });

        const summary = document.getElementById('kanban-search-summary');
        if (q.length < 2) { summary.textContent = ''; return; }

        const parts = Object.keys(perCol).map(k => COLUMN_TITLES[k] + ': ' + perCol[k]);
        summary.textContent = total + ' coincidencia(s) · ' + (parts.join(' · ') || 'sin resultados');

        clearTimeout(highlightTimer);
        if (total === 1) {
            const card = Array.from(cards).find(c => !c.classList.contains('hidden'));
            if (card) {
                card.scrollIntoView({ behavior: 'smooth', block: 'center' });
                card.classList.add('ring-2', 'ring-blue-500');
                highlightTimer = setTimeout(() => card.classList.remove('ring-2', 'ring-blue-500'), 2500);
            }
        }
    }

    searchInput.addEventListener('input', applyFilter);

    // ===== Chips: scroll a columna =====
    document.addEventListener('click', function (e) {
        const chip = e.target.closest('.kanban-chip');
        if (!chip) return;
        const col = document.getElementById('col-' + chip.dataset.col);
        if (col) col.scrollIntoView({ behavior: 'smooth', inline: 'center', block: 'nearest' });
    });

    // ===== Init =====
    loadBoard();
    </script>
    @endpush
</x-app-layout>

