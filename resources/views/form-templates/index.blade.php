<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">{{ __('Plantillas de Formulario') }}</h2>
            <a href="{{ route('form-templates.create') }}" class="btn btn-primary">
                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/>
                </svg>
                Nueva plantilla
            </a>
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

            <div class="mb-4 px-4 py-3 bg-blue-50 border border-blue-200 text-blue-700 rounded-lg text-sm">
                Las plantillas de <strong>Control de calidad</strong> y <strong>Encuesta de satisfacción</strong> se asignan por taller (establecimiento). Si un taller no tiene plantilla propia, usa la <strong>Global</strong>.
            </div>

            <div class="card overflow-hidden">
                <div class="p-4 sm:p-5">
                    <div class="mb-3">
                        <div class="relative max-w-md">
                            <svg class="pointer-events-none absolute left-3 top-1/2 h-4 w-4 -translate-y-1/2 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                            </svg>
                            <input type="text" id="form-template-search" placeholder="Buscar plantilla..." class="search-input">
                        </div>
                    </div>
                    <div id="form-template-table" class="data-table"></div>
                </div>
            </div>
        </div>
    </div>

    {{-- Modal: duplicar plantilla --}}
    <div id="duplicate-modal" class="hidden fixed inset-0 z-50 overflow-y-auto" aria-hidden="true">
        <div class="flex min-h-full items-center justify-center p-4">
            <div class="fixed inset-0 bg-gray-500/75" data-dup-close></div>
            <div class="relative w-full max-w-md rounded-lg bg-white shadow-xl">
                <div class="px-5 py-4 border-b border-gray-200 flex justify-between items-center">
                    <h3 class="text-base font-semibold text-gray-800">Duplicar plantilla</h3>
                    <button type="button" data-dup-close class="text-gray-400 hover:text-gray-600" aria-label="Cerrar">
                        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                </div>
                <form id="duplicate-form" method="POST" class="p-5 space-y-4" data-confirm="¿Duplicar esta plantilla? Se copiarán sus secciones y preguntas.">
                    @csrf
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Nombre de la copia <span class="text-red-500">*</span></label>
                        <input type="text" name="name" id="dup-name" required maxlength="150" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Taller (establecimiento)</label>
                        <select name="establishment_id" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                            <option value="">Global (todos los talleres)</option>
                            @foreach ($establishments as $est)
                                <option value="{{ $est->id }}">{{ $est->name }}</option>
                            @endforeach
                        </select>
                        <p class="mt-1 text-xs text-gray-500">Si dejas "Global", la copia será la plantilla de respaldo de todos los talleres.</p>
                    </div>
                    <div class="flex flex-wrap gap-2 justify-end">
                        <button type="button" data-dup-close class="btn btn-secondary">Cancelar</button>
                        <button type="submit" class="btn btn-primary">Duplicar plantilla</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        const typeBadge = (type) => {
            const colors = {
                'quality_control': 'bg-purple-50 text-purple-700',
                'satisfaction_survey': 'bg-blue-50 text-blue-700'
            };
            const labels = {
                'quality_control': 'Control de calidad',
                'satisfaction_survey': 'Encuesta de satisfacción'
            };
            return `<span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium ${colors[type] || 'bg-gray-100 text-gray-700'}">${labels[type] || type || ''}</span>`;
        };

        const table = new Tabulator('#form-template-table', {
            ajaxURL: "{{ route('api.form-templates.search') }}?limit=100",
            layout: 'fitColumns',
            responsiveLayout: 'collapse',
            placeholder: 'No hay plantillas registradas',
            height: 'auto',
            columns: [
                { title: 'Tipo', field: 'type', width: 180, hozAlign: 'center', formatter: cell => typeBadge(cell.getValue()) },
                { title: 'Nombre', field: 'name', minWidth: 200 },
                { title: 'Taller', field: 'establishment', minWidth: 200 },
                { title: 'Secciones', field: 'sections_count', width: 100, hozAlign: 'center' },
                { title: 'Preguntas', field: 'items_count', width: 100, hozAlign: 'center' },
                { title: 'Activa', field: 'is_active', width: 90, hozAlign: 'center',
                  formatter: cell => cell.getValue()
                    ? '<span class="px-2 py-0.5 rounded-full text-xs font-medium bg-green-50 text-green-700">Sí</span>'
                    : '<span class="px-2 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-500">No</span>' },
                {
                    title: 'Acciones', field: 'id', width: 170, hozAlign: 'center', headerSort: false,
                    formatter: function(cell) {
                        const id = cell.getData().id;
                        const name = cell.getData().name || '';
                        return `<div class="flex gap-2 justify-center">
                            <button type="button" data-duplicate-id="${id}" data-name="${name.replace(/"/g, '&quot;')}" title="Duplicar plantilla" class="btn-icon btn-icon-blue">
                                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M8 7v8a2 2 0 002 2h6M8 7V5a2 2 0 012-2h4.586a1 1 0 01.707.293l4.414 4.414a1 1 0 01.293.707V15a2 2 0 01-2 2h-2M8 7H6a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2v-2"/>
                                </svg>
                            </button>
                            <a href="/form-templates/${id}/edit" title="Editar plantilla" class="btn-icon btn-icon-amber">
                                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                </svg>
                            </a>
                            <form method="POST" action="/form-templates/${id}" class="inline" data-confirm="¿Eliminar esta plantilla? Las OT ya revisadas conservan sus respuestas.">
                                @csrf @method('DELETE')
                                <button type="submit" title="Eliminar plantilla" class="btn-icon btn-icon-red">
                                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                    </svg>
                                </button>
                            </form>
                        </div>`;
                    }
                }
            ]
        });

        document.getElementById('form-template-search').addEventListener('input', function(e) {
            table.setData("{{ route('api.form-templates.search') }}?q=" + encodeURIComponent(e.target.value) + "&limit=100");
        });

        // Modal de duplicar plantilla
        const dupModal = document.getElementById('duplicate-modal');
        const dupForm = document.getElementById('duplicate-form');
        const dupName = document.getElementById('dup-name');

        function openDupModal() {
            dupModal.classList.remove('hidden');
            dupModal.setAttribute('aria-hidden', 'false');
            document.body.style.overflow = 'hidden';
        }

        function closeDupModal() {
            dupModal.classList.add('hidden');
            dupModal.setAttribute('aria-hidden', 'true');
            document.body.style.overflow = '';
        }

        document.querySelectorAll('[data-dup-close]').forEach(function (el) {
            el.addEventListener('click', closeDupModal);
        });
        document.addEventListener('keydown', function (e) {
            if (e.key === 'Escape') closeDupModal();
        });

        document.addEventListener('click', function (e) {
            const btn = e.target.closest('[data-duplicate-id]');
            if (!btn) return;
            dupForm.action = '/form-templates/' + btn.dataset.duplicateId + '/duplicate';
            dupName.value = 'Copia de ' + (btn.dataset.name || '');
            openDupModal();
        });
    </script>
    @endpush
</x-app-layout>

