@php
    $rs = $repairService ?? null;
@endphp

<div class="grid grid-cols-1 md:grid-cols-2 gap-4">
    <div>
        <label class="block text-sm font-medium text-gray-700">Nombre <span class="text-red-500">*</span></label>
        <input type="text" name="name" value="{{ old('name', $rs->name ?? '') }}" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
        @error('name') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
    </div>
    <div>
        <div class="flex items-center justify-between">
            <label class="mb-1 block text-sm font-medium text-gray-700">Categoría</label>
            <button type="button" data-catalog-create="service-categories" data-target="service_category_id" data-label="categoría"
                    class="inline-flex items-center gap-1 text-xs font-medium text-blue-600 hover:underline">
                <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg>
                Nueva categoría
            </button>
        </div>
        <select id="service_category_id" name="service_category_id" placeholder="Buscar categoría..."></select>
    </div>
    <div>
        <label class="block text-sm font-medium text-gray-700">Tipo de cobro <span class="text-red-500">*</span></label>
        <select name="pricing_type" id="pricing_type" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
            <option value="fixed" @selected(old('pricing_type', $rs->pricing_type ?? 'fixed') === 'fixed')>Precio fijo</option>
            <option value="time_based" @selected(old('pricing_type', $rs->pricing_type ?? 'fixed') === 'time_based')>Por horas</option>
        </select>
        @error('pricing_type') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
    </div>
    <div>
        <label class="block text-sm font-medium text-gray-700">Moneda de venta <span class="text-red-500">*</span></label>
        <select name="currency" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
            <option value="PEN" @selected(old('currency', $rs->currency ?? 'PEN') === 'PEN')>Soles (PEN)</option>
            <option value="USD" @selected(old('currency', $rs->currency ?? 'PEN') === 'USD')>Dólares (USD)</option>
        </select>
    </div>
    <div>
        <label class="block text-sm font-medium text-gray-700">Precio de venta (neto) <span class="text-red-500">*</span></label>
        <input type="number" step="0.01" min="0" name="sell_price" value="{{ old('sell_price', $rs->sell_price ?? '') }}" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
        <p id="sell-price-hint" class="mt-1 text-xs text-gray-500 hidden">Tarifa por hora en modo por horas.</p>
        @error('sell_price') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
    </div>
    <div>
        <label class="block text-sm font-medium text-gray-700">Costo (neto) <span class="text-red-500">*</span></label>
        <input type="number" step="0.01" min="0" name="cost_price" value="{{ old('cost_price', $rs->cost_price ?? '') }}" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
        <p id="cost-price-hint" class="mt-1 text-xs text-gray-500 hidden">Tarifa de costo por hora.</p>
        @error('cost_price') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
    </div>
    <div>
        <label class="block text-sm font-medium text-gray-700">Moneda de costo <span class="text-red-500">*</span></label>
        <select name="cost_currency" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
            <option value="PEN" @selected(old('cost_currency', $rs->cost_currency ?? 'PEN') === 'PEN')>Soles (PEN)</option>
            <option value="USD" @selected(old('cost_currency', $rs->cost_currency ?? 'PEN') === 'USD')>Dólares (USD)</option>
        </select>
    </div>
    <div>
        <label class="block text-sm font-medium text-gray-700">Horas estimadas</label>
        <input type="number" step="0.01" min="0" name="estimated_hours" value="{{ old('estimated_hours', $rs->estimated_hours ?? '') }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
    </div>
    <div>
        <label class="block text-sm font-medium text-gray-700">Horas mínimas</label>
        <input type="number" step="0.01" min="0" name="min_hours" value="{{ old('min_hours', $rs->min_hours ?? '') }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
    </div>
    <div>
        <label class="block text-sm font-medium text-gray-700">Proveedor por defecto</label>
        <select id="default_provider_id" name="default_provider_id" placeholder="Buscar proveedor..."></select>
    </div>
    <div>
        <label class="block text-sm font-medium text-gray-700">Descripción</label>
        <textarea name="description" rows="2" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">{{ old('description', $rs->description ?? '') }}</textarea>
    </div>
    <div class="flex items-center gap-2">
        <input type="hidden" name="is_outsourced" value="0">
        <input type="checkbox" id="is_outsourced" name="is_outsourced" value="1" class="rounded border-gray-300 text-blue-600 shadow-sm focus:ring-blue-500" @checked(old('is_outsourced', $rs->is_outsourced ?? false))>
        <label for="is_outsourced" class="text-sm font-medium text-gray-700">Es tercerizado</label>
    </div>
    <div class="flex items-center gap-2">
        <input type="hidden" name="is_active" value="0">
        <input type="checkbox" id="is_active" name="is_active" value="1" class="rounded border-gray-300 text-blue-600 shadow-sm focus:ring-blue-500" @checked(old('is_active', $rs->is_active ?? true))>
        <label for="is_active" class="text-sm font-medium text-gray-700">Activo</label>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    if (!window.TomSelect) return;

    // Almacena las instancias globales por id para el modal de alta rápida
    window.catalogInstances = window.catalogInstances || {};

    function initCategory(selector, searchUrl, value, label) {
        const ts = new TomSelect(selector, {
            valueField: 'id',
            labelField: 'name',
            searchField: ['name'],
            maxItems: 1, closeAfterSelect: true, create: false, copyClassesToDropdown: false,
            dropdownParent: 'body',
            plugins: ['clear_button'],
            load: function (query, callback) {
                fetch(`${searchUrl}?q=${encodeURIComponent(query)}`)
                    .then(r => r.json()).then(callback).catch(() => callback());
            }
        });
        if (value) {
            ts.addOption({ id: value, name: label });
            ts.addItem(value);
        }
        return ts;
    }

    window.catalogInstances['service_category_id'] = initCategory('#service_category_id',
        "{{ route('api.service-categories.search') }}",
        @json($rs?->service_category_id), @json($rs?->category?->name));

    const provider = new TomSelect('#default_provider_id', {
        valueField: 'id',
        labelField: 'display_name',
        searchField: ['display_name', 'document_number'],
        maxItems: 1, closeAfterSelect: true, create: false, copyClassesToDropdown: false,
        dropdownParent: 'body',
        plugins: ['clear_button'],
        load: function (query, callback) {
            fetch(`{{ route('api.parties.suppliers') }}?q=${encodeURIComponent(query)}`)
                .then(r => r.json()).then(callback).catch(() => callback());
        }
    });
    @if ($rs?->provider)
        provider.addOption({ id: '{{ $rs->provider->id }}', display_name: '{{ $rs->provider->display_name }}' });
        provider.addItem('{{ $rs->provider->id }}');
    @endif
    provider.on('item_add', function () { provider.blur(); });

    // El botón "Nueva categoría" es manejado por partials.catalog-quick-create

    const pricingType = document.getElementById('pricing_type');
    const sellHint = document.getElementById('sell-price-hint');
    const costHint = document.getElementById('cost-price-hint');
    function toggleHints() {
        const isHourly = pricingType.value === 'time_based';
        sellHint.classList.toggle('hidden', !isHourly);
        costHint.classList.toggle('hidden', !isHourly);
    }
    pricingType.addEventListener('change', toggleHints);
    toggleHints();
});
</script>
@endpush