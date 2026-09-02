<div class="grid grid-cols-1 gap-6">
    <div>
        <label for="brand-name" class="block text-sm font-medium text-gray-700">Nombre de la marca <span class="text-red-500">*</span></label>
        <input type="text" id="brand-name" name="name" value="{{ old('name', $brand?->name) }}" required maxlength="120" placeholder="EJ. TOYOTA"
               class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm uppercase">
        @error('name') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
    </div>

    <div>
        <div class="flex items-center justify-between mb-2">
            <label class="block text-sm font-medium text-gray-700">Modelos de la marca</label>
            <button type="button" id="add-model" class="inline-flex items-center gap-1 rounded-lg px-3 py-1.5 text-sm font-semibold text-blue-700 bg-blue-50 hover:bg-blue-100 transition focus-visible:ring-2 ring-blue-500 ring-offset-2">
                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/>
                </svg>
                Agregar modelo
            </button>
        </div>

        <div class="overflow-x-auto border border-gray-200 rounded-lg">
            <table class="w-full text-sm">
                <thead>
                    <tr class="border-b border-gray-200 bg-gray-50">
                        <th class="px-3 py-2 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">Modelo</th>
                        <th class="px-3 py-2 w-12"></th>
                    </tr>
                </thead>
                <tbody id="models-body">
                    @if ($models->isEmpty())
                        <tr id="models-empty">
                            <td colspan="2" class="px-3 py-6 text-center text-sm text-gray-500">
                                Sin modelos aún. Usa "Agregar modelo" para incluir los modelos de esta marca.
                            </td>
                        </tr>
                    @else
                        @foreach ($models as $i => $model)
                            <tr class="border-b border-gray-100">
                                <td class="px-3 py-2">
                                    <input type="hidden" name="models[{{ $i }}][id]" value="{{ $model->id }}">
                                    <input type="text" name="models[{{ $i }}][name]" value="{{ $model->name }}" maxlength="120" placeholder="EJ. COROLLA"
                                           class="model-name block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm uppercase">
                                </td>
                                <td class="px-3 py-2 text-right">
                                    @can('eliminar modelos')
                                        <button type="button" class="btn-icon btn-icon-red remove-model" title="Quitar modelo">
                                            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
                                            </svg>
                                        </button>
                                    @endcan
                                </td>
                            </tr>
                        @endforeach
                    @endif
                </tbody>
            </table>
        </div>
        @error('models') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
    </div>
</div>

<template id="model-row-template">
    <tr class="border-b border-gray-100">
        <td class="px-3 py-2">
            <input type="hidden" name="models[__INDEX__][id]" value="">
            <input type="text" name="models[__INDEX__][name]" maxlength="120" placeholder="EJ. COROLLA"
                   class="model-name block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm uppercase">
        </td>
        <td class="px-3 py-2 text-right">
            @can('eliminar modelos')
                <button type="button" class="btn-icon btn-icon-red remove-model" title="Quitar modelo">
                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            @endcan
        </td>
    </tr>
</template>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const tbody = document.getElementById('models-body');
        const template = document.getElementById('model-row-template');
        if (!tbody || !template) return;

        let rowIndex = {{ $models->count() }};

        function upperValue(v) {
            return (v || '').trim().toUpperCase();
        }

        // Marca: transformar a mayúsculas mientras se escribe (verificación visual en vivo)
        const brandName = document.getElementById('brand-name');
        if (brandName) {
            brandName.addEventListener('input', function () {
                if (brandName.value !== brandName.value.toUpperCase()) {
                    brandName.value = brandName.value.toUpperCase();
                }
            });
        }

        // Modelos: mayúsculas en vivo + detectar duplicados dentro de la lista
        function refreshModelInput(el) {
            if (el.value !== el.value.toUpperCase()) {
                el.value = el.value.toUpperCase();
            }
            markDuplicateModels();
        }

        function markDuplicateModels() {
            const inputs = Array.from(tbody.querySelectorAll('input.model-name'));
            const counts = {};
            inputs.forEach(inp => {
                const name = upperValue(inp.value);
                if (name) counts[name] = (counts[name] || 0) + 1;
            });

            inputs.forEach(inp => {
                const name = upperValue(inp.value);
                const duplicated = name !== '' && counts[name] > 1;

                inp.classList.toggle('border-red-500', duplicated);
                inp.classList.toggle('focus:border-red-500', duplicated);

                let note = inp.parentElement.querySelector('.model-dup-note');
                if (duplicated) {
                    if (!note) {
                        note = document.createElement('p');
                        note.className = 'model-dup-note mt-1 text-xs text-red-600';
                        note.textContent = 'Modelo repetido en la lista.';
                        inp.parentElement.appendChild(note);
                    }
                } else if (note) {
                    note.remove();
                }
            });
        }

        function addRow() {
            const frag = template.content.cloneNode(true);
            frag.querySelectorAll('[name*="__INDEX__"]').forEach(el => {
                el.name = el.name.replaceAll('__INDEX__', rowIndex);
            });
            tbody.appendChild(frag);
            rowIndex++;

            const empty = document.getElementById('models-empty');
            if (empty) empty.remove();

            markDuplicateModels();
        }

        document.getElementById('add-model').addEventListener('click', addRow);

        tbody.addEventListener('input', function (e) {
            if (e.target.classList.contains('model-name')) refreshModelInput(e.target);
        });

        tbody.addEventListener('click', function (e) {
            const btn = e.target.closest('.remove-model');
            if (!btn) return;
            btn.closest('tr').remove();
            markDuplicateModels();
        });

        markDuplicateModels();
    });
</script>
@endpush
