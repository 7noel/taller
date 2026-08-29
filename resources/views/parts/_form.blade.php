@php
    $p = $part ?? null;
@endphp

<div class="grid grid-cols-1 md:grid-cols-2 gap-4">
    <div>
        <label class="block text-sm font-medium text-gray-700">Nombre <span class="text-red-500">*</span></label>
        <input type="text" name="name" value="{{ old('name', $p->name ?? '') }}" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
        @error('name') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
    </div>
    <div>
        <label class="block text-sm font-medium text-gray-700">SKU (código interno) <span class="text-red-500">*</span></label>
        <input type="text" name="sku" value="{{ old('sku', $p->sku ?? '') }}" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
        @error('sku') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
    </div>
    <div>
        <div class="flex items-center justify-between">
            <label class="mb-1 block text-sm font-medium text-gray-700">Marca</label>
            <button type="button" data-catalog-create="part-brands" data-target="part_brand_id" data-label="marca"
                    class="inline-flex items-center gap-1 text-xs font-medium text-blue-600 hover:underline">
                <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg>
                Nueva marca
            </button>
        </div>
        <select id="part_brand_id" name="part_brand_id" placeholder="Buscar marca..."></select>
    </div>
    <div>
        <div class="flex items-center justify-between">
            <label class="mb-1 block text-sm font-medium text-gray-700">Categoría</label>
            <button type="button" data-catalog-create="part-categories" data-target="part_category_id" data-label="categoría"
                    class="inline-flex items-center gap-1 text-xs font-medium text-blue-600 hover:underline">
                <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg>
                Nueva categoría
            </button>
        </div>
        <select id="part_category_id" name="part_category_id" placeholder="Buscar categoría..."></select>
    </div>
    <div>
        <label class="block text-sm font-medium text-gray-700">Código de fabricante</label>
        <input type="text" name="manufacturer_code" value="{{ old('manufacturer_code', $p->manufacturer_code ?? '') }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
    </div>
    <div>
        <label class="block text-sm font-medium text-gray-700">Código de barras</label>
        <input type="text" name="barcode" value="{{ old('barcode', $p->barcode ?? '') }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
        @error('barcode') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
    </div>
    <div>
        <label class="block text-sm font-medium text-gray-700">Unidad de medida</label>
        <select name="uom" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
            @foreach ($unitMeasures as $u)
                <option value="{{ $u->code }}" @selected(old('uom', $p->uom ?? 'NIU') === $u->code)>{{ $u->code }} · {{ $u->name }}</option>
            @endforeach
        </select>
    </div>
    <div>
        <label class="block text-sm font-medium text-gray-700">Stock mínimo</label>
        <input type="number" step="1" min="0" name="min_stock" value="{{ old('min_stock', $p->min_stock ?? '0') }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
    </div>
    <div>
        <label class="block text-sm font-medium text-gray-700">Costo (neto) <span class="text-red-500">*</span></label>
        <input type="number" step="0.01" min="0" name="cost_price" value="{{ old('cost_price', $p->cost_price ?? '') }}" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
    </div>
    <div>
        <label class="block text-sm font-medium text-gray-700">Moneda de costo <span class="text-red-500">*</span></label>
        <select name="cost_currency" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
            <option value="PEN" @selected(old('cost_currency', $p->cost_currency ?? 'PEN') === 'PEN')>Soles (PEN)</option>
            <option value="USD" @selected(old('cost_currency', $p->cost_currency ?? 'PEN') === 'USD')>Dólares (USD)</option>
        </select>
    </div>
    <div>
        <label class="block text-sm font-medium text-gray-700">Precio de venta (neto) <span class="text-red-500">*</span></label>
        <input type="number" step="0.01" min="0" name="sell_price" value="{{ old('sell_price', $p->sell_price ?? '') }}" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
    </div>
    <div>
        <label class="block text-sm font-medium text-gray-700">Moneda de venta <span class="text-red-500">*</span></label>
        <select name="currency" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
            <option value="PEN" @selected(old('currency', $p->currency ?? 'PEN') === 'PEN')>Soles (PEN)</option>
            <option value="USD" @selected(old('currency', $p->currency ?? 'PEN') === 'USD')>Dólares (USD)</option>
        </select>
    </div>
    <div class="md:col-span-2">
        <label class="block text-sm font-medium text-gray-700">Descripción</label>
        <textarea name="description" rows="2" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">{{ old('description', $p->description ?? '') }}</textarea>
    </div>
    <div class="flex items-center gap-2">
        <input type="hidden" name="is_active" value="0">
        <input type="checkbox" id="is_active" name="is_active" value="1" class="rounded border-gray-300 text-blue-600 shadow-sm focus:ring-blue-500" @checked(old('is_active', $p->is_active ?? true))>
        <label for="is_active" class="text-sm font-medium text-gray-700">Activo</label>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    if (!window.TomSelect) return;

    // Almacena las instancias globales por id para el modal de alta rápida
    window.catalogInstances = window.catalogInstances || {};

    function initTomSelect(selector, searchUrl, value, label) {
        const ts = new TomSelect(selector, {
            valueField: 'id',
            labelField: 'name',
            searchField: ['name'],
            maxItems: 1,
            closeAfterSelect: true,
            create: false,
            copyClassesToDropdown: false,
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

    window.catalogInstances['part_brand_id'] = initTomSelect('#part_brand_id',
        "{{ route('api.part-brands.search') }}",
        @json($p->part_brand_id ?? null), @json($p->brand?->name ?? ''));

    window.catalogInstances['part_category_id'] = initTomSelect('#part_category_id',
        "{{ route('api.part-categories.search') }}",
        @json($p->part_category_id ?? null), @json($p->category?->name ?? ''));

    // Los botones "Nueva marca/categoría" son manejados por partials.catalog-quick-create
});
</script>
@endpush