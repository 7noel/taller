<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-wrap justify-between items-center gap-3">
            <div class="flex flex-wrap items-center gap-3">
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">{{ __('Editar Plantilla') }}</h2>
                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $template->type === 'quality_control' ? 'bg-purple-100 text-purple-800' : 'bg-blue-100 text-blue-800' }}">
                    {{ $template->type_label }}
                </span>
            </div>
            <a href="{{ route('form-templates.index') }}" class="btn btn-secondary">Volver</a>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            @if (session('success'))
                <div class="mb-4 px-4 py-3 bg-green-50 border border-green-200 text-green-700 rounded-lg text-sm">{{ session('success') }}</div>
            @endif
            @if ($errors->any())
                <div class="mb-4 px-4 py-3 bg-red-50 border border-red-200 text-red-700 rounded-lg text-sm">
                    @foreach ($errors->all() as $error)
                        <p>{{ $error }}</p>
                    @endforeach
                </div>
            @endif

            <div class="mb-4 px-4 py-3 bg-blue-50 border border-blue-200 text-blue-700 rounded-lg text-sm">
                Arma las <strong>secciones</strong> y <strong>preguntas</strong> de la plantilla. Para listas desplegables (select) u opciones únicas (radio), escribe las opciones una por línea con el formato <code>valor|etiqueta</code> (ej. <code>half|1/2</code>). Deja el identificador vacío para generarlo automáticamente desde el texto.
            </div>

            <form method="POST" action="{{ route('form-templates.update', $template) }}" id="template-form">
                @csrf @method('PUT')

                <div class="card mb-4">
                    <div class="p-4 sm:p-5 grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div class="md:col-span-1">
                            <label class="block text-sm font-medium text-gray-700">Nombre <span class="text-red-500">*</span></label>
                            <input type="text" name="name" value="{{ old('name', $template->name) }}" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                            @error('name') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Taller (establecimiento)</label>
                            <select name="establishment_id" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                <option value="">Global (todos los talleres)</option>
                                @foreach ($establishments as $est)
                                    <option value="{{ $est->id }}" @selected(old('establishment_id', $template->establishment_id) == $est->id)>{{ $est->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="flex items-end gap-2 pb-1">
                            <input type="hidden" name="is_active" value="0">
                            <input type="checkbox" id="is_active" name="is_active" value="1" @checked(old('is_active', $template->is_active)) class="rounded border-gray-300 text-blue-600 shadow-sm focus:ring-blue-500">
                            <label for="is_active" class="text-sm font-medium text-gray-700">Plantilla activa</label>
                        </div>
                    </div>
                </div>

                <div id="sections-container" class="space-y-4">
                    @forelse ($template->sections as $sIdx => $section)
                        <div class="card section-card">
                            <div class="p-4 sm:p-5">
                                <div class="flex flex-wrap items-center justify-between gap-3 mb-3">
                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-3 flex-1">
                                        <input type="hidden" name="sections[{{ $sIdx }}][id]" value="{{ $section->id }}">
                                        <div>
                                            <label class="block text-xs font-medium text-gray-700">Nombre de la sección <span class="text-red-500">*</span></label>
                                            <input type="text" name="sections[{{ $sIdx }}][name]" value="{{ $section->name }}" required class="mt-1 w-full rounded-md border-gray-300 text-sm">
                                        </div>
                                        <div>
                                            <label class="block text-xs font-medium text-gray-700">Orden</label>
                                            <input type="number" name="sections[{{ $sIdx }}][order]" min="0" value="{{ $section->order }}" class="mt-1 w-28 rounded-md border-gray-300 text-sm">
                                        </div>
                                    </div>
                                    <button type="button" class="remove-section-btn btn-icon btn-icon-red" title="Eliminar sección">
                                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                    </button>
                                </div>

                                <div class="space-y-3">
                                    @foreach ($section->items as $iIdx => $item)
                                        @php $itemName = "sections[{$sIdx}][items][{$iIdx}]"; @endphp
                                        <div class="item-card rounded-lg border border-gray-200 p-3">
                                            <input type="hidden" name="{{ $itemName }}[id]" value="{{ $item->id }}">
                                            <div class="grid grid-cols-1 md:grid-cols-12 gap-3 items-end">
                                                <div class="md:col-span-2">
                                                    <label class="block text-xs font-medium text-gray-700">Tipo <span class="text-red-500">*</span></label>
                                                    <select name="{{ $itemName }}[type]" class="item-type-select mt-1 w-full rounded-md border-gray-300 text-sm">
                                                        @foreach ($itemTypes as $typeValue => $typeLabel)
                                                            <option value="{{ $typeValue }}" @selected($item->type === $typeValue)>{{ $typeLabel }}</option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                                <div class="md:col-span-2">
                                                    <label class="block text-xs font-medium text-gray-700">Identificador</label>
                                                    <input type="text" name="{{ $itemName }}[key]" value="{{ $item->key }}" placeholder="auto" class="mt-1 w-full rounded-md border-gray-300 text-sm font-mono">
                                                </div>
                                                <div class="md:col-span-5">
                                                    <label class="block text-xs font-medium text-gray-700">Texto de la pregunta <span class="text-red-500">*</span></label>
                                                    <input type="text" name="{{ $itemName }}[label]" value="{{ $item->label }}" required class="mt-1 w-full rounded-md border-gray-300 text-sm">
                                                </div>
                                                <div class="md:col-span-1 flex items-center gap-1.5 pb-1">
                                                    <input type="hidden" name="{{ $itemName }}[is_required]" value="0">
                                                    <input type="checkbox" name="{{ $itemName }}[is_required]" value="1" @checked($item->is_required) class="rounded border-gray-300 text-blue-600 text-sm">
                                                    <label class="text-xs font-medium text-gray-700">Oblig.</label>
                                                </div>
                                                <div class="md:col-span-1 flex justify-end pb-1">
                                                    <button type="button" class="remove-item-btn btn-icon btn-icon-red" title="Eliminar pregunta">
                                                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                                                    </button>
                                                </div>
                                                <div class="md:col-span-1">
                                                    <label class="block text-xs font-medium text-gray-700">Orden</label>
                                                    <input type="number" name="{{ $itemName }}[order]" min="0" value="{{ $item->order }}" class="mt-1 w-full rounded-md border-gray-300 text-sm">
                                                </div>
                                                <div class="md:col-span-3">
                                                    <label class="block text-xs font-medium text-gray-700">Mover a sección</label>
                                                    <select name="{{ $itemName }}[move_to_section_id]" class="mt-1 w-full rounded-md border-gray-300 text-sm">
                                                        <option value="">Mantener en esta sección</option>
                                                        @foreach ($template->sections as $target)
                                                            <option value="{{ $target->id }}" @selected($item->form_template_section_id === $target->id)>{{ $target->name }}</option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                                <div class="md:col-span-9 item-options {{ in_array($item->type, ['select', 'radio'], true) ? '' : 'hidden' }}">
                                                    <label class="block text-xs font-medium text-gray-700">Opciones (valor|etiqueta, una por línea)</label>
                                                    <textarea name="{{ $itemName }}[options]" rows="3" class="mt-1 w-full rounded-md border-gray-300 text-sm font-mono">{{ old($itemName . '.options', $item->options_text) }}</textarea>
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>

                                <div class="mt-3">
                                    <button type="button" class="add-item-btn btn btn-secondary">+ Agregar pregunta</button>
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="card">
                            <div class="p-6 text-center text-sm text-gray-500">
                                Aún no hay secciones. Usa "+ Agregar sección" para empezar a armar el formulario.
                            </div>
                        </div>
                    @endforelse
                </div>

                <div class="mt-4 flex flex-wrap gap-2 justify-between">
                    <button type="button" id="add-section-btn" class="btn btn-secondary">+ Agregar sección</button>
                    <button type="submit" class="btn btn-primary">Guardar plantilla</button>
                </div>
            </form>
        </div>
    </div>

@php
    $sectionsData = $template->sections->map(fn ($s) => ['id' => $s->id, 'name' => $s->name])->values();
@endphp

    @push('scripts')
    <script>
        (function () {
            'use strict';

            const container = document.getElementById('sections-container');
            const itemTypes = @json($itemTypes);
            const sectionsList = @json($sectionsData);

            function sectionsOptionsMarkup() {
                return '<option value="">Mantener en esta sección</option>'
                    + sectionsList.map(s => `<option value="${s.id}">${s.name}</option>`).join('');
            }

            function typeOptionsMarkup() {
                return Object.entries(itemTypes)
                    .map(([value, label]) => `<option value="${value}">${label}</option>`)
                    .join('');
            }

            function itemMarkup(sIdx, iIdx, type) {
                type = type || 'text';
                const showOptions = (type === 'select' || type === 'radio') ? '' : 'hidden';
                return `<div class="item-card rounded-lg border border-gray-200 p-3">
                    <div class="grid grid-cols-1 md:grid-cols-12 gap-3 items-end">
                        <div class="md:col-span-2">
                            <label class="block text-xs font-medium text-gray-700">Tipo <span class="text-red-500">*</span></label>
                            <select name="sections[${sIdx}][items][${iIdx}][type]" class="item-type-select mt-1 w-full rounded-md border-gray-300 text-sm">${typeOptionsMarkup()}</select>
                        </div>
                        <div class="md:col-span-2">
                            <label class="block text-xs font-medium text-gray-700">Identificador</label>
                            <input type="text" name="sections[${sIdx}][items][${iIdx}][key]" placeholder="auto" class="mt-1 w-full rounded-md border-gray-300 text-sm font-mono">
                        </div>
                        <div class="md:col-span-5">
                            <label class="block text-xs font-medium text-gray-700">Texto de la pregunta <span class="text-red-500">*</span></label>
                            <input type="text" name="sections[${sIdx}][items][${iIdx}][label]" required class="mt-1 w-full rounded-md border-gray-300 text-sm">
                        </div>
                        <div class="md:col-span-1 flex items-center gap-1.5 pb-1">
                            <input type="hidden" name="sections[${sIdx}][items][${iIdx}][is_required]" value="0">
                            <input type="checkbox" name="sections[${sIdx}][items][${iIdx}][is_required]" value="1" class="rounded border-gray-300 text-blue-600 text-sm">
                            <label class="text-xs font-medium text-gray-700">Oblig.</label>
                        </div>
                        <div class="md:col-span-1 flex justify-end pb-1">
                            <button type="button" class="remove-item-btn btn-icon btn-icon-red" title="Eliminar pregunta">
                                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                            </button>
                        </div>
                        <div class="md:col-span-1">
                            <label class="block text-xs font-medium text-gray-700">Orden</label>
                            <input type="number" name="sections[${sIdx}][items][${iIdx}][order]" min="0" value="0" class="mt-1 w-full rounded-md border-gray-300 text-sm">
                        </div>
                        <div class="md:col-span-3">
                            <label class="block text-xs font-medium text-gray-700">Mover a sección</label>
                            <select name="sections[${sIdx}][items][${iIdx}][move_to_section_id]" class="mt-1 w-full rounded-md border-gray-300 text-sm">${sectionsOptionsMarkup()}</select>
                        </div>
                        <div class="md:col-span-9 item-options ${showOptions}">
                            <label class="block text-xs font-medium text-gray-700">Opciones (valor|etiqueta, una por línea)</label>
                            <textarea name="sections[${sIdx}][items][${iIdx}][options]" rows="3" class="mt-1 w-full rounded-md border-gray-300 text-sm font-mono"></textarea>
                        </div>
                    </div>
                </div>`;
            }


            function sectionMarkup(sIdx) {
                return `<div class="card section-card">
                    <div class="p-4 sm:p-5">
                        <div class="flex flex-wrap items-center justify-between gap-3 mb-3">
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-3 flex-1">
                                <div>
                                    <label class="block text-xs font-medium text-gray-700">Nombre de la sección <span class="text-red-500">*</span></label>
                                    <input type="text" name="sections[${sIdx}][name]" required class="mt-1 w-full rounded-md border-gray-300 text-sm" placeholder="Ej. Datos generales">
                                </div>
                                <div>
                                    <label class="block text-xs font-medium text-gray-700">Orden</label>
                                    <input type="number" name="sections[${sIdx}][order]" min="0" value="${sIdx}" class="mt-1 w-28 rounded-md border-gray-300 text-sm">
                                </div>
                            </div>
                            <button type="button" class="remove-section-btn btn-icon btn-icon-red" title="Eliminar sección">
                                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                            </button>
                        </div>
                        <div class="space-y-3"></div>
                        <div class="mt-3">
                            <button type="button" class="add-item-btn btn btn-secondary">+ Agregar pregunta</button>
                        </div>
                    </div>
                </div>`;
            }

            function emptyState() {
                return `<div class="card"><div class="p-6 text-center text-sm text-gray-500">Aún no hay secciones. Usa "+ Agregar sección" para empezar a armar el formulario.</div></div>`;
            }

            function refreshEmptyState() {
                const empty = container.querySelector(':scope > .card:not(.section-card)');
                if (container.querySelectorAll('.section-card').length === 0 && !empty) {
                    container.insertAdjacentHTML('beforeend', emptyState());
                } else if (empty && container.querySelectorAll('.section-card').length > 0) {
                    empty.remove();
                }
            }


            document.getElementById('add-section-btn').addEventListener('click', function () {
                const sIdx = container.querySelectorAll('.section-card').length;
                container.querySelector('.card:not(.section-card)')?.remove();
                container.insertAdjacentHTML('beforeend', sectionMarkup(sIdx));
            });

            container.addEventListener('click', function (e) {
                const removeSection = e.target.closest('.remove-section-btn');
                if (removeSection) {
                    removeSection.closest('.section-card').remove();
                    refreshEmptyState();
                    return;
                }

                const addItem = e.target.closest('.add-item-btn');
                if (addItem) {
                    const section = addItem.closest('.section-card');
                    const sIdx = Array.from(container.querySelectorAll('.section-card')).indexOf(section);
                    const iIdx = section.querySelectorAll('.item-card').length;
                    section.querySelector('.space-y-3').insertAdjacentHTML('beforeend', itemMarkup(sIdx, iIdx, 'text'));
                    return;
                }

                const removeItem = e.target.closest('.remove-item-btn');
                if (removeItem) {
                    removeItem.closest('.item-card').remove();
                }
            });

            container.addEventListener('change', function (e) {
                const select = e.target.closest('.item-type-select');
                if (!select) return;
                const options = select.closest('.item-card').querySelector('.item-options');
                if (!options) return;
                const type = select.value;
                options.classList.toggle('hidden', !(type === 'select' || type === 'radio'));
            });
        })();
    </script>
    @endpush
</x-app-layout>

