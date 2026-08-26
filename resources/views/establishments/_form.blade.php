<div class="grid grid-cols-1 md:grid-cols-2 gap-4">
    <div>
        <label class="block text-sm font-medium text-gray-700">Nombre *</label>
        <input type="text" name="name" value="{{ old('name', $establishment->name ?? '') }}" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
        @error('name') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
    </div>
    <div>
        <label class="block text-sm font-medium text-gray-700">Código *</label>
        <input type="text" name="code" value="{{ old('code', $establishment->code ?? '') }}" required maxlength="10" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 uppercase">
        @error('code') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
    </div>
    <div class="md:col-span-2">
        <label class="block text-sm font-medium text-gray-700">Dirección</label>
        <input type="text" name="address" value="{{ old('address', $establishment->address ?? '') }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
    </div>
    <div>
        <label for="est-departamento" class="block text-sm font-medium text-gray-700">Departamento</label>
        <select id="est-departamento" name="est-departamento" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
            <option value="">Seleccionar...</option>
            @foreach ($departamentos as $departamento)
                <option value="{{ $departamento }}">{{ $departamento }}</option>
            @endforeach
        </select>
    </div>
    <div>
        <label for="est-provincia" class="block text-sm font-medium text-gray-700">Provincia</label>
        <select id="est-provincia" name="est-provincia" disabled class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
            <option value="">Seleccionar...</option>
        </select>
    </div>
    <div>
        <label for="est-distrito" class="block text-sm font-medium text-gray-700">Distrito</label>
        <select id="est-distrito" name="ubigeo_code" disabled class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
            <option value="">Seleccionar...</option>
        </select>
        @error('ubigeo_code') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
    </div>
    <div>
        <label class="block text-sm font-medium text-gray-700">Teléfono</label>
        <input type="text" name="phone" value="{{ old('phone', $establishment->phone ?? '') }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
    </div>
    <div>
        <label class="block text-sm font-medium text-gray-700">Celular</label>
        <input type="text" name="celular" value="{{ old('celular', $establishment->celular ?? '') }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
    </div>
    <div>
        <label class="block text-sm font-medium text-gray-700">Email</label>
        <input type="email" name="email" value="{{ old('email', $establishment->email ?? '') }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
        @error('email') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
    </div>
    <div>
        <label class="block text-sm font-medium text-gray-700">IGV (%)</label>
        <input type="number" step="0.0001" min="0" name="igv_rate" value="{{ old('igv_rate', $establishment->igv_rate ?? '0.1800') }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
        <p class="mt-1 text-xs text-gray-500">Por defecto se copia de la configuración de empresa.</p>
        @error('igv_rate') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
    </div>
    <div>
        <label class="block text-sm font-medium text-gray-700">Moneda base</label>
        <select name="base_currency" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
            <option value="PEN" @selected(old('base_currency', $establishment->base_currency ?? 'PEN') === 'PEN')>Soles (PEN)</option>
            <option value="USD" @selected(old('base_currency', $establishment->base_currency ?? 'PEN') === 'USD')>Dólares (USD)</option>
        </select>
    </div>
    <div class="flex items-center gap-2">
        <input type="hidden" name="prices_include_tax" value="0">
        <input type="checkbox" id="prices_include_tax" name="prices_include_tax" value="1"
               class="rounded border-gray-300 text-blue-600 shadow-sm focus:ring-blue-500"
               @checked(old('prices_include_tax', $establishment->prices_include_tax ?? true))>
        <label for="prices_include_tax" class="text-sm font-medium text-gray-700">Los precios incluyen IGV</label>
    </div>
    <div>
        <label class="block text-sm font-medium text-gray-700">Tarifa hora por defecto</label>
        <input type="number" step="0.01" min="0" name="default_hourly_rate" value="{{ old('default_hourly_rate', $establishment->default_hourly_rate ?? '') }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
    </div>
    <div>
        <label class="block text-sm font-medium text-gray-700">Tarifa paño por defecto</label>
        <input type="number" step="0.01" min="0" name="default_panel_rate" value="{{ old('default_panel_rate', $establishment->default_panel_rate ?? '') }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
    </div>

    {{-- Credenciales Evolution API (WhatsApp) por establecimiento --}}
    <div class="md:col-span-2 mt-4 pt-4 border-t border-gray-200">
        <h3 class="text-sm font-semibold text-gray-700">Evolution API (WhatsApp)</h3>
        <p class="text-xs text-gray-500 mt-1">Se copian desde la configuración de empresa al crear el establecimiento.</p>
    </div>
    <div>
        <label class="block text-sm font-medium text-gray-700">API URL</label>
        <input type="url" name="whatsapp_api_url" value="{{ old('whatsapp_api_url', $establishment->whatsapp_api_url ?? '') }}" placeholder="https://tuevolution.example.com" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
        @error('whatsapp_api_url') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
    </div>
    <div>
        <label class="block text-sm font-medium text-gray-700">Token API</label>
        <input type="password" name="whatsapp_api_token" value="{{ old('whatsapp_api_token', $establishment->whatsapp_api_token ?? '') }}" autocomplete="off" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
        @error('whatsapp_api_token') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
    </div>
    <div>
        <label class="block text-sm font-medium text-gray-700">Nombre de instancia</label>
        <input type="text" name="whatsapp_instance_name" value="{{ old('whatsapp_instance_name', $establishment->whatsapp_instance_name ?? '') }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
        @error('whatsapp_instance_name') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
    </div>
    <div class="flex items-center gap-2">
        <input type="hidden" name="whatsapp_enabled" value="0">
        <input type="checkbox" id="whatsapp_enabled" name="whatsapp_enabled" value="1"
               class="rounded border-gray-300 text-blue-600 shadow-sm focus:ring-blue-500"
               @checked(old('whatsapp_enabled', $establishment->whatsapp_enabled ?? false))>
        <label for="whatsapp_enabled" class="text-sm font-medium text-gray-700">Envío de WhatsApp habilitado</label>
    </div>
</div>

@push('scripts')
<script>
// Cascada ubigeo (Departamento → Provincia → Distrito)
if (window.setupUbigeoCascade) {
    window.setupUbigeoCascade('est', @json($establishment->ubigeo_code ?? ''));
}
</script>
@endpush
@include('partials.ubigeo-cascade')