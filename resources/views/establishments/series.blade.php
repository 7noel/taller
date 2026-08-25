@php
    $seriesData = $series->map(fn ($s) => [
        'id' => $s->id,
        'code' => $s->documentType->code,
        'document_name' => $s->documentType->name,
        'is_electronic' => $s->documentType->is_electronic,
        'document_type_id' => $s->document_type_id,
        'prefix_serie' => $s->prefix_serie,
        'current_number' => $s->current_number,
        'number_source' => $s->number_source,
        'status' => $s->status,
        'formatted' => $s->formatted_number,
    ])->values();

    $documentTypesData = $documentTypes->map(fn ($t) => [
        'id' => $t->id,
        'name' => $t->name,
        'code' => $t->code,
        'is_electronic' => $t->is_electronic,
    ])->values();
@endphp

<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center gap-3 flex-wrap">
            <div>
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">{{ __('Series de Documentos') }}</h2>
                <p class="text-sm text-gray-500 mt-1">{{ $establishment->name }} ({{ $establishment->code }})</p>
            </div>
            <div class="flex items-center gap-2">
                @can('crear series')
                    <button type="button" id="btn-new-series" class="btn btn-primary">
                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg>
                        Nueva Serie
                    </button>
                @endcan
                <a href="{{ route('establishments.edit', $establishment) }}" class="btn btn-secondary">
                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                    Volver
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            @if (session('success'))
                <div class="mb-4 px-4 py-3 bg-green-50 border border-green-200 text-green-700 rounded-lg text-sm">{{ session('success') }}</div>
            @endif
            @if (session('error'))
                <div class="mb-4 px-4 py-3 bg-red-50 border border-red-200 text-red-700 rounded-lg text-sm">{{ session('error') }}</div>
            @endif

            <div class="card overflow-hidden">
                <div class="p-6">
                    <div class="mb-4 px-4 py-3 bg-blue-50 border border-blue-200 text-blue-700 rounded-lg text-sm">
                        {!! __('Los números se generan con <code class="font-semibold">getNextNumber()</code> (lock pesimista). Nunca usar <code class="font-semibold">MAX()+1</code>.') !!}
                    </div>
                    <div id="series-table" class="data-table"></div>
                </div>
            </div>
        </div>
    </div>

    {{-- Modal crear / editar serie --}}
    <div id="series-modal" class="fixed inset-0 bg-gray-900 bg-opacity-50 hidden items-center justify-center z-50 p-4">
        <div class="bg-white rounded-lg shadow-xl max-w-lg w-full p-6 max-h-screen overflow-y-auto">
            <h3 id="series-modal-title" class="text-xl font-bold mb-1 text-gray-800">Nueva Serie</h3>
            <p class="text-sm text-gray-500 mb-4">Define el prefijo y el correlativo de la serie.</p>

            <div id="series-modal-error" class="hidden mb-4 rounded-md bg-red-50 border border-red-200 px-4 py-3 text-sm text-red-700"></div>

            <div class="grid grid-cols-1 gap-4">
                {{-- Tipo de documento --}}
                <div>
                    <label for="series-doc-type" class="block text-sm font-medium text-gray-700">Tipo de documento <span class="text-red-500">*</span></label>
                    <select id="series-doc-type" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                        <option value="">Seleccionar...</option>
                    </select>
                    <input type="hidden" id="series-doc-type-hidden">
                    <p id="series-doc-type-error" class="mt-1 text-xs text-red-600 hidden"></p>
                </div>

                {{-- Prefijo + Correlativo inicial --}}
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label for="series-prefix" class="block text-sm font-medium text-gray-700">Prefijo <span class="text-red-500">*</span></label>
                        <input type="text" id="series-prefix" maxlength="10" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 font-mono uppercase" placeholder="Ej. IV01">
                        <p id="series-prefix-error" class="mt-1 text-xs text-red-600 hidden"></p>
                    </div>
                    <div>
                        <label for="series-number" class="block text-sm font-medium text-gray-700">Nº correlativo inicial <span class="text-red-500">*</span></label>
                        <input type="number" id="series-number" min="0" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                        <p id="series-number-error" class="mt-1 text-xs text-red-600 hidden"></p>
                    </div>
                </div>

                {{-- Origen del número + Estado --}}
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 items-end">
                    <div>
                        <label for="series-source" class="block text-sm font-medium text-gray-700">Origen del número <span class="text-red-500">*</span></label>
                        <select id="series-source" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                            <option value="LOCAL">LOCAL</option>
                            <option value="API">API</option>
                        </select>
                        <p id="series-source-error" class="mt-1 text-xs text-red-600 hidden"></p>
                    </div>
                    <div class="pb-1">
                        <label class="inline-flex items-center gap-2 text-sm font-medium text-gray-700 cursor-pointer select-none">
                            <input type="checkbox" id="series-status" class="rounded border-gray-300 text-blue-600 shadow-sm focus:ring-blue-500" checked>
                            Serie activa
                        </label>
                    </div>
                </div>
            </div>

            <div class="mt-6 flex gap-2 justify-end">
                <button type="button" id="series-modal-cancel" class="btn btn-secondary">Cancelar</button>
                <button type="button" id="series-modal-save" class="btn btn-primary">Guardar</button>
            </div>
        </div>
    </div>

    {{-- Modal confirmar eliminación --}}
    <div id="series-delete-modal" class="fixed inset-0 bg-gray-900 bg-opacity-50 hidden items-center justify-center z-50 p-4">
        <div class="bg-white rounded-lg shadow-xl max-w-md w-full p-6">
            <h3 class="text-lg font-bold text-gray-800">Eliminar serie</h3>
            <p class="mt-2 text-sm text-gray-600">
                ¿Estás seguro de eliminar la serie <span id="series-delete-name" class="font-semibold text-gray-900 font-mono"></span>?
                Esta acción no se puede deshacer.
            </p>
            <div class="mt-6 flex gap-2 justify-end">
                <button type="button" id="series-delete-cancel" class="btn btn-secondary">Cancelar</button>
                <form id="series-delete-form" method="POST" class="inline">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger">Eliminar</button>
                </form>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
    (function () {
        'use strict';

        const seriesList = @json($seriesData);
        const documentTypes = @json($documentTypesData);
        const establishmentId = {{ $establishment->id }};

        // ===================== Tabla =====================
        new Tabulator('#series-table', {
            layout: 'fitColumns',
            responsiveLayout: 'collapse',
            placeholder: 'No hay series registradas para este establecimiento',
            height: 'auto',
            data: seriesList,
            columns: [
                {
                    title: 'Documento',
                    field: 'document_name',
                    minWidth: 200,
                    formatter: function (cell) {
                        const d = cell.getData();
                        const badge = d.is_electronic
                            ? '<span class="ml-1 inline-flex items-center gap-1 px-2 py-0.5 text-xs font-semibold rounded-full bg-blue-50 text-blue-700"><svg class="h-3 w-3" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>Electrónico</span>'
                            : '<span class="ml-1 inline-flex items-center gap-1 px-2 py-0.5 text-xs font-semibold rounded-full bg-gray-100 text-gray-600"><svg class="h-3 w-3" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>Interno</span>';
                        return `<div class="flex items-center flex-wrap gap-y-1">
                            <span class="text-gray-900 font-medium">${escapeHtml(d.document_name)}</span>
                            <span class="text-xs text-gray-400 font-mono ml-0.5">${escapeHtml(d.code)}</span>
                            ${badge}
                        </div>`;
                    }
                },
                {
                    title: 'Serie Actual',
                    field: 'formatted',
                    minWidth: 150,
                    formatter: function (cell) {
                        const d = cell.getData();
                        if (d.number_source === 'API') {
                            return `<span class="font-mono text-sm font-semibold text-gray-900">${escapeHtml(d.prefix_serie)}</span> <span class="text-xs text-gray-400">· API</span>`;
                        }
                        return `<span class="font-mono text-sm font-semibold text-blue-700">${escapeHtml(d.formatted || d.prefix_serie)}</span>`;
                    }
                },
                {
                    title: 'Origen',
                    field: 'number_source',
                    width: 100,
                    hozAlign: 'center',
                    formatter: function (cell) {
                        return cell.getValue() === 'API'
                            ? '<span class="px-2 py-0.5 text-xs font-semibold rounded-full bg-amber-50 text-amber-700">API</span>'
                            : '<span class="px-2 py-0.5 text-xs font-semibold rounded-full bg-green-50 text-green-700">LOCAL</span>';
                    }
                },
                {
                    title: 'Estado',
                    field: 'status',
                    width: 100,
                    hozAlign: 'center',
                    formatter: function (cell) {
                        return cell.getValue()
                            ? '<span class="px-2 py-0.5 text-xs font-semibold rounded-full bg-green-50 text-green-700">Activa</span>'
                            : '<span class="px-2 py-0.5 text-xs font-semibold rounded-full bg-red-50 text-red-700">Inactiva</span>';
                    }
                },
                {
                    title: 'Acciones',
                    field: 'id',
                    width: 100,
                    hozAlign: 'center',
                    headerHozAlign: 'center',
                    headerSort: false,
                    formatter: function (cell) {
                        const d = cell.getData();
                        return `<div class="flex gap-2 justify-center">
                            <button type="button" class="btn-icon btn-icon-amber series-edit-btn" title="Editar serie" data-id="${d.id}">
                                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                            </button>
                            <button type="button" class="btn-icon btn-icon-red series-delete-btn" title="Eliminar serie" data-id="${d.id}">
                                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                            </button>
                        </div>`;
                    }
                }
            ]
        });

        function escapeHtml(str) {
            return String(str ?? '').replace(/[&<>"']/g, m => ({ '&': '&', '<': '<', '>': '>', '"': '"', "'": '&#039;' }[m]));
        }

        // ===================== Acciones por fila (botones .btn-icon) =====================
        document.addEventListener('click', function (e) {
            const editBtn = e.target.closest('.series-edit-btn');
            if (editBtn) {
                const series = seriesList.find(s => String(s.id) === String(editBtn.dataset.id));
                if (series) openSeriesModal(series);
                return;
            }

            const deleteBtn = e.target.closest('.series-delete-btn');
            if (deleteBtn) {
                const series = seriesList.find(s => String(s.id) === String(deleteBtn.dataset.id));
                if (series) openDeleteModal(series);
            }
        });

        // ===================== Modal crear / editar =====================
        const seriesModal = document.getElementById('series-modal');
        const seriesModalTitle = document.getElementById('series-modal-title');
        const seriesModalError = document.getElementById('series-modal-error');
        const docTypeSelect = document.getElementById('series-doc-type');
        const docTypeHidden = document.getElementById('series-doc-type-hidden');
        const prefixInput = document.getElementById('series-prefix');
        const numberInput = document.getElementById('series-number');
        const sourceSelect = document.getElementById('series-source');
        const statusCheck = document.getElementById('series-status');
        const btnCancel = document.getElementById('series-modal-cancel');
        const btnSave = document.getElementById('series-modal-save');

        let editingSeriesId = null;
        let saving = false;

        function populateDocTypes() {
            docTypeSelect.innerHTML = '<option value="">Seleccionar...</option>';
            documentTypes.forEach(t => {
                const opt = document.createElement('option');
                opt.value = t.id;
                opt.textContent = t.name + ' (' + t.code + ')';
                docTypeSelect.appendChild(opt);
            });
        }

        function setFieldError(id, msg) {
            const el = document.getElementById(id);
            const errEl = document.getElementById(id + '-error');
            if (msg) {
                if (errEl) { errEl.textContent = msg; errEl.classList.remove('hidden'); }
                el.classList.add('border-red-500');
            } else {
                if (errEl) { errEl.textContent = ''; errEl.classList.add('hidden'); }
                el.classList.remove('border-red-500');
            }
        }

        function clearErrors() {
            ['series-doc-type', 'series-prefix', 'series-number', 'series-source'].forEach(id => setFieldError(id, ''));
            seriesModalError.classList.add('hidden');
            seriesModalError.textContent = '';
        }

        function openSeriesModal(series) {
            clearErrors();
            populateDocTypes();

            editingSeriesId = series ? series.id : null;

            if (series) {
                seriesModalTitle.textContent = 'Editar Serie';
                docTypeSelect.value = series.document_type_id;
                docTypeSelect.disabled = true;
                docTypeSelect.classList.add('bg-gray-100');
                docTypeHidden.value = series.document_type_id;
                docTypeSelect.name = '';
                prefixInput.value = series.prefix_serie;
                numberInput.value = series.current_number;
                sourceSelect.value = series.number_source;
                statusCheck.checked = !!series.status;
            } else {
                seriesModalTitle.textContent = 'Nueva Serie';
                docTypeSelect.value = '';
                docTypeSelect.disabled = false;
                docTypeSelect.classList.remove('bg-gray-100');
                docTypeHidden.value = '';
                prefixInput.value = '';
                numberInput.value = '0';
                sourceSelect.value = 'LOCAL';
                statusCheck.checked = true;
            }

            seriesModal.classList.remove('hidden');
            seriesModal.classList.add('flex');
            setTimeout(() => prefixInput.focus(), 50);
        }

        function closeSeriesModal() {
            seriesModal.classList.add('hidden');
            seriesModal.classList.remove('flex');
        }

        btnCancel.addEventListener('click', closeSeriesModal);
        seriesModal.addEventListener('click', function (e) { if (e.target === seriesModal) closeSeriesModal(); });
        document.addEventListener('keydown', function (e) { if (e.key === 'Escape' && !seriesModal.classList.contains('hidden')) closeSeriesModal(); });

        document.getElementById('btn-new-series').addEventListener('click', () => openSeriesModal(null));

        btnSave.addEventListener('click', async function () {
            if (saving) return;
            saving = true;
            try { await doSave(); } finally { saving = false; }
        });

        async function doSave() {
            clearErrors();

            const csrf = document.querySelector('meta[name="csrf-token"]')?.content;
            const isEdit = !!editingSeriesId;
            const url = isEdit
                ? `/establishments/${establishmentId}/series/${editingSeriesId}`
                : `/establishments/${establishmentId}/series`;
            const method = isEdit ? 'PUT' : 'POST';

            const payload = {
                document_type_id: docTypeHidden.value || docTypeSelect.value,
                prefix_serie: prefixInput.value.trim(),
                current_number: numberInput.value,
                number_source: sourceSelect.value,
                status: statusCheck.checked ? 1 : 0,
            };
            if (isEdit) payload._method = 'PUT';

            btnSave.disabled = true;
            btnSave.textContent = 'Guardando...';
            try {
                const res = await fetch(url, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': csrf,
                        'X-HTTP-Method-Override': method,
                    },
                    body: JSON.stringify(payload),
                });

                if (res.status === 422) {
                    const data = await res.json().catch(() => ({}));
                    const errors = data.errors || {};
                    if (errors.document_type_id) setFieldError('series-doc-type', errors.document_type_id[0]);
                    if (errors.prefix_serie) setFieldError('series-prefix', errors.prefix_serie[0]);
                    if (errors.current_number) setFieldError('series-number', errors.current_number[0]);
                    if (errors.number_source) setFieldError('series-source', errors.number_source[0]);
                    const first = Object.values(errors).flat()[0];
                    if (first) {
                        seriesModalError.textContent = first;
                        seriesModalError.classList.remove('hidden');
                    }
                    return;
                }

                if (!res.ok) {
                    seriesModalError.textContent = 'No se pudo guardar la serie. Inténtalo nuevamente.';
                    seriesModalError.classList.remove('hidden');
                    return;
                }

                // Éxito: el backend redirige; recargamos la vista para mostrar el flash
                window.location.reload();
            } catch (e) {
                seriesModalError.textContent = 'Error de conexión al guardar la serie.';
                seriesModalError.classList.remove('hidden');
            } finally {
                btnSave.disabled = false;
                btnSave.textContent = 'Guardar';
            }
        }

        // ===================== Modal eliminar =====================
        const deleteModal = document.getElementById('series-delete-modal');
        const deleteName = document.getElementById('series-delete-name');
        const deleteForm = document.getElementById('series-delete-form');
        const btnDeleteCancel = document.getElementById('series-delete-cancel');

        function openDeleteModal(series) {
            deleteName.textContent = series.prefix_serie;
            deleteForm.action = `/establishments/${establishmentId}/series/${series.id}`;
            deleteModal.classList.remove('hidden');
            deleteModal.classList.add('flex');
        }

        function closeDeleteModal() {
            deleteModal.classList.add('hidden');
            deleteModal.classList.remove('flex');
        }

        btnDeleteCancel.addEventListener('click', closeDeleteModal);
        deleteModal.addEventListener('click', function (e) { if (e.target === deleteModal) closeDeleteModal(); });
        document.addEventListener('keydown', function (e) { if (e.key === 'Escape' && !deleteModal.classList.contains('hidden')) closeDeleteModal(); });
    })();
    </script>
    @endpush
</x-app-layout>