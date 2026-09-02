<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">{{ __('Configuración de Empresa') }}</h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            @if (session('success'))
                <div class="mb-4 px-4 py-3 bg-green-50 border border-green-200 text-green-700 rounded-lg text-sm">{{ session('success') }}</div>
            @endif

            <div class="card overflow-hidden">
                <div class="border-b border-gray-200">
                    <nav class="flex overflow-x-auto" aria-label="Tabs">
                        <button type="button" data-tab="tab-fiscal" class="tab-btn px-4 py-3 text-sm font-semibold border-b-2 border-blue-600 text-blue-600 whitespace-nowrap">Datos Fiscales y Ubigeo</button>
                        <button type="button" data-tab="tab-branding" class="tab-btn px-4 py-3 text-sm font-semibold border-b-2 border-transparent text-gray-500 hover:text-gray-700 whitespace-nowrap">Branding</button>
                        <button type="button" data-tab="tab-detraccion" class="tab-btn px-4 py-3 text-sm font-semibold border-b-2 border-transparent text-gray-500 hover:text-gray-700 whitespace-nowrap">Detracción y Contacto</button>
                        <button type="button" data-tab="tab-integraciones" class="tab-btn px-4 py-3 text-sm font-semibold border-b-2 border-transparent text-gray-500 hover:text-gray-700 whitespace-nowrap">Integraciones</button>
                        <button type="button" data-tab="tab-maintenance" class="tab-btn px-4 py-3 text-sm font-semibold border-b-2 border-transparent text-gray-500 hover:text-gray-700 whitespace-nowrap">Mantenimiento</button>
                        <button type="button" data-tab="tab-reminders" class="tab-btn px-4 py-3 text-sm font-semibold border-b-2 border-transparent text-gray-500 hover:text-gray-700 whitespace-nowrap">Recordatorios</button>
                        <button type="button" data-tab="tab-photos" class="tab-btn px-4 py-3 text-sm font-semibold border-b-2 border-transparent text-gray-500 hover:text-gray-700 whitespace-nowrap">Inventario / Fotos</button>
                    </nav>
                </div>

                <form method="POST" action="{{ route('company-settings.update') }}" enctype="multipart/form-data" class="p-6" id="company-settings-form">
                    @csrf
                    @method('PUT')

                    {{-- Pestaña 1: Datos Fiscales y Ubigeo --}}
                    <div id="tab-fiscal" class="tab-panel">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700">RUC *</label>
                                <input type="text" name="ruc" value="{{ old('ruc', $setting->ruc) }}" maxlength="11" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                @error('ruc') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Razón Social *</label>
                                <input type="text" name="razon_social" value="{{ old('razon_social', $setting->razon_social) }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                @error('razon_social') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Nombre Comercial</label>
                                <input type="text" name="nombre_comercial" value="{{ old('nombre_comercial', $setting->nombre_comercial) }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Dirección</label>
                                <input type="text" name="direccion" value="{{ old('direccion', $setting->direccion) }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                            </div>
                            <div>
                                <label for="cs-departamento" class="block text-sm font-medium text-gray-700">Departamento</label>
                                <select id="cs-departamento" name="cs-departamento" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                    <option value="">Seleccionar...</option>
                                    @foreach ($departamentos as $departamento)
                                        <option value="{{ $departamento }}">{{ $departamento }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <label for="cs-provincia" class="block text-sm font-medium text-gray-700">Provincia</label>
                                <select id="cs-provincia" name="cs-provincia" disabled class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                    <option value="">Seleccionar...</option>
                                </select>
                            </div>
                            <div>
                                <label for="cs-distrito" class="block text-sm font-medium text-gray-700">Distrito</label>
                                <select id="cs-distrito" name="ubigeo_code" disabled class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                    <option value="">Seleccionar...</option>
                                </select>
                                @error('ubigeo_code') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                            </div>
                        </div>
                    </div>

                    {{-- Pestaña 2: Branding --}}
                    <div id="tab-branding" class="tab-panel hidden">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Logo</label>
                                <div class="mt-2 flex items-center gap-4">
                                    <div id="logo-preview" class="h-20 w-20 rounded-lg border border-gray-200 bg-gray-50 flex items-center justify-center overflow-hidden">
                                        @if ($setting->logo_url)
                                            <img src="{{ $setting->logo_url }}" alt="Logo actual" class="h-full w-full object-contain">
                                        @else
                                            <span class="text-xs text-gray-400">Sin logo</span>
                                        @endif
                                    </div>
                                    <input type="file" id="logo-input" name="logo" accept="image/png,image/jpeg,image/webp,image/svg+xml" class="block w-full text-sm text-gray-500 file:mr-3 file:rounded-md file:border-0 file:bg-blue-50 file:px-3 file:py-2 file:text-sm file:font-semibold file:text-blue-700 hover:file:bg-blue-100">
                                </div>
                                @error('logo') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Favicon</label>
                                <div class="mt-2 flex items-center gap-4">
                                    <div id="favicon-preview" class="h-12 w-12 rounded-lg border border-gray-200 bg-gray-50 flex items-center justify-center overflow-hidden">
                                        @if ($setting->favicon_url)
                                            <img src="{{ $setting->favicon_url }}" alt="Favicon actual" class="h-full w-full object-contain">
                                        @else
                                            <span class="text-xs text-gray-400">Sin favicon</span>
                                        @endif
                                    </div>
                                    <input type="file" id="favicon-input" name="favicon" accept="image/png,image/jpeg,image/webp,image/x-icon" class="block w-full text-sm text-gray-500 file:mr-3 file:rounded-md file:border-0 file:bg-blue-50 file:px-3 file:py-2 file:text-sm file:font-semibold file:text-blue-700 hover:file:bg-blue-100">
                                </div>
                                @error('favicon') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                            </div>
                        </div>
                    </div>

                    {{-- Pestaña 3: Detracción y Contacto --}}
                    <div id="tab-detraccion" class="tab-panel hidden">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Cuenta de Detracción</label>
                                <input type="text" name="detraccion_account" value="{{ old('detraccion_account', $setting->detraccion_account) }}" maxlength="20" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Detracción (%) *</label>
                                <input type="number" step="0.0001" min="0" max="1" name="detraccion_rate" value="{{ old('detraccion_rate', $setting->detraccion_rate ?? '0.1200') }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                <p class="mt-1 text-xs text-gray-500">Porcentaje de detracción para servicios (SPOT). Ej: 0.12 = 12%.</p>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700">IGV (%) *</label>
                                <input type="number" step="0.0001" min="0" name="igv_rate" value="{{ old('igv_rate', $setting->igv_rate ?? '0.1800') }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                <p class="mt-1 text-xs text-gray-500">Se copia como valor por defecto a los nuevos establecimientos.</p>
                                @error('igv_rate') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Teléfono</label>
                                <input type="text" name="telefono" value="{{ old('telefono', $setting->telefono) }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Celular</label>
                                <input type="text" name="celular" value="{{ old('celular', $setting->celular) }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Email</label>
                                <input type="email" name="email" value="{{ old('email', $setting->email) }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                @error('email') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                            </div>
                        </div>
                    </div>

                    {{-- Pestaña 4: Integraciones --}}
                    <div id="tab-integraciones" class="tab-panel hidden">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Origen de Números *</label>
                                <select name="default_number_source" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                    <option value="LOCAL" @selected(old('default_number_source', $setting->default_number_source) === 'LOCAL')>LOCAL (numeración interna)</option>
                                    <option value="API" @selected(old('default_number_source', $setting->default_number_source) === 'API')>API (espera al facturador)</option>
                                </select>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Proveedor de Facturación *</label>
                                <select name="facturador_provider" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                    <option value="local" @selected(old('facturador_provider', $setting->facturador_provider) === 'local')>Local</option>
                                    <option value="nubefact" @selected(old('facturador_provider', $setting->facturador_provider) === 'nubefact')>Nubefact</option>
                                    <option value="propio" @selected(old('facturador_provider', $setting->facturador_provider) === 'propio')>Propio</option>
                                </select>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700">API URL (Facturador)</label>
                                <input type="url" name="facturador_api_url" value="{{ old('facturador_api_url', $setting->facturador_api_url) }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                @error('facturador_api_url') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700">API Key (Facturador)</label>
                                <input type="password" name="facturador_api_key" value="{{ old('facturador_api_key', $setting->facturador_api_key) }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Secret (Facturador)</label>
                                <input type="password" name="facturador_secret" value="{{ old('facturador_secret', $setting->facturador_secret) }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700">API URL (WhatsApp)</label>
                                <input type="url" name="whatsapp_api_url" value="{{ old('whatsapp_api_url', $setting->whatsapp_api_url) }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                @error('whatsapp_api_url') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Token (WhatsApp)</label>
                                <input type="password" name="whatsapp_api_token" value="{{ old('whatsapp_api_token', $setting->whatsapp_api_token) }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Instancia (WhatsApp)</label>
                                <input type="text" name="whatsapp_instance_name" value="{{ old('whatsapp_instance_name', $setting->whatsapp_instance_name) }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                            </div>
                            <div class="flex items-center gap-2">
                                <input type="hidden" name="whatsapp_enabled" value="0">
                                <input type="checkbox" id="whatsapp_enabled" name="whatsapp_enabled" value="1"
                                       class="rounded border-gray-300 text-blue-600 shadow-sm focus:ring-blue-500"
                                       @checked(old('whatsapp_enabled', $setting->whatsapp_enabled ?? false))>
                                <label for="whatsapp_enabled" class="text-sm font-medium text-gray-700">Envío de WhatsApp habilitado</label>
                            </div>
                        </div>
                    </div>

                    {{-- Pestaña 5: Mantenimiento preventivo --}}
                    <div id="tab-maintenance" class="tab-panel hidden">
                        <h3 class="text-lg font-semibold text-gray-800 mb-1">Mantenimiento preventivo</h3>
                        <p class="text-sm text-gray-500 mb-4">Reglas del cálculo de la próxima visita preventiva por kilometraje. El panel de Recordatorios y los check-ins de tipo preventivo usan esta configuración.</p>
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Intervalo (km) *</label>
                                <input type="number" name="maintenance_interval_km" value="{{ old('maintenance_interval_km', $setting->maintenance_interval_km) }}" min="500" max="50000" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                <p class="mt-1 text-xs text-gray-500">Cada cuántos km se recomienda el preventivo (default 5000).</p>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Días promedio (default) *</label>
                                <input type="number" name="maintenance_default_days" value="{{ old('maintenance_default_days', $setting->maintenance_default_days) }}" min="15" max="365" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                <p class="mt-1 text-xs text-gray-500">Usado cuando el vehículo no tiene historial suficiente (default 120).</p>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Visitas a usar (historial) *</label>
                                <input type="number" name="maintenance_history_visits" value="{{ old('maintenance_history_visits', $setting->maintenance_history_visits) }}" min="2" max="5" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                <p class="mt-1 text-xs text-gray-500">Últimas 2-3 visitas con kilometraje para proyectar (default 3).</p>
                            </div>
                        </div>
                    </div>

                    {{-- Pestaña 6: Recordatorios automáticos por WhatsApp --}}
                    <div id="tab-reminders" class="tab-panel hidden">
                        <h3 class="text-lg font-semibold text-gray-800 mb-1">Recordatorios automáticos por WhatsApp</h3>
                        <p class="text-sm text-gray-500 mb-4">Define qué recordatorios se envían automáticamente, con cuántos días de anticipación y a qué hora. Usa las credenciales de WhatsApp de la pestaña Integraciones; el envío lo ejecuta el comando programado <code>reminders:process</code> (cada 30 minutos).</p>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-5">
                            <div>
                                <label class="flex items-center gap-2 text-sm font-medium text-gray-700">
                                    <input type="checkbox" name="reminder_enabled" value="1" {{ old('reminder_enabled', $setting->reminder_enabled ?? true) ? 'checked' : '' }} class="rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                                    Recordatorios automáticos activos
                                </label>
                                <p class="mt-1 text-xs text-gray-500">Switch maestro: apaga todos los envíos automáticos de una vez.</p>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Hora de envío</label>
                                <input type="time" name="reminder_hour" value="{{ old('reminder_hour', $setting->reminder_hour ?? '09:00') }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                <p class="mt-1 text-xs text-gray-500">Horario prudente para no molestar (default 09:00).</p>
                            </div>
                        </div>

                        {{-- Revisión técnica --}}
                        <div class="border border-gray-200 rounded-lg p-4 mb-4">
                            <div class="flex items-start justify-between gap-4 mb-3">
                                <div>
                                    <h4 class="text-sm font-semibold text-gray-800">Revisión técnica</h4>
                                    <p class="text-xs text-gray-500">Al cliente (dueño del vehículo) antes del vencimiento de la revisión técnica.</p>
                                </div>
                                <label class="flex items-center gap-2 text-sm font-medium text-gray-700 whitespace-nowrap">
                                    <input type="checkbox" name="reminder_technical_review_enabled" value="1" {{ old('reminder_technical_review_enabled', $setting->reminder_technical_review_enabled ?? true) ? 'checked' : '' }} class="rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                                    Activo
                                </label>
                            </div>
                            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Días de anticipación</label>
                                    <input type="number" name="reminder_technical_review_days" value="{{ old('reminder_technical_review_days', $setting->reminder_technical_review_days ?? 10) }}" min="0" max="90" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                </div>
                            </div>
                        </div>

                        {{-- Mantenimiento preventivo --}}
                        <div class="border border-gray-200 rounded-lg p-4 mb-4">
                            <div class="flex items-start justify-between gap-4 mb-3">
                                <div>
                                    <h4 class="text-sm font-semibold text-gray-800">Mantenimiento preventivo</h4>
                                    <p class="text-xs text-gray-500">Al cliente (dueño del vehículo) antes de la próxima visita preventiva calculada.</p>
                                </div>
                                <label class="flex items-center gap-2 text-sm font-medium text-gray-700 whitespace-nowrap">
                                    <input type="checkbox" name="reminder_maintenance_enabled" value="1" {{ old('reminder_maintenance_enabled', $setting->reminder_maintenance_enabled ?? true) ? 'checked' : '' }} class="rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                                    Activo
                                </label>
                            </div>
                            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Días de anticipación</label>
                                    <input type="number" name="reminder_maintenance_days" value="{{ old('reminder_maintenance_days', $setting->reminder_maintenance_days ?? 7) }}" min="0" max="90" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                </div>
                            </div>
                        </div>

                        {{-- Autopartes de seguro --}}
                        <div class="border border-gray-200 rounded-lg p-4 mb-4">
                            <div class="flex items-start justify-between gap-4 mb-3">
                                <div>
                                    <h4 class="text-sm font-semibold text-gray-800">Autopartes de seguro</h4>
                                    <p class="text-xs text-gray-500">Al asesor (del presupuesto, con respaldo en quien creó el pedido) para hacer seguimiento a los repuestos que traerá la aseguradora.</p>
                                </div>
                                <label class="flex items-center gap-2 text-sm font-medium text-gray-700 whitespace-nowrap">
                                    <input type="checkbox" name="reminder_part_order_enabled" value="1" {{ old('reminder_part_order_enabled', $setting->reminder_part_order_enabled ?? true) ? 'checked' : '' }} class="rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                                    Activo
                                </label>
                            </div>
                            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Hitos (días antes de la entrega)</label>
                                    <input type="text" name="reminder_part_milestones" value="{{ old('reminder_part_milestones', $setting->reminder_part_milestones ?? '25,20,17,15,10,5') }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                    <p class="mt-1 text-xs text-gray-500">Separados por comas. Ej. 25,20,17,15,10,5 (default).</p>
                                </div>
                            </div>
                        </div>

                        {{-- Presupuestos en aprobación --}}
                        <div class="border border-gray-200 rounded-lg p-4 mb-4">
                            <div class="flex items-start justify-between gap-4 mb-3">
                                <div>
                                    <h4 class="text-sm font-semibold text-gray-800">Presupuestos en aprobación</h4>
                                    <p class="text-xs text-gray-500">Al asesor del presupuesto para dar seguimiento a las aprobaciones pendientes (seguro o cliente).</p>
                                </div>
                                <label class="flex items-center gap-2 text-sm font-medium text-gray-700 whitespace-nowrap">
                                    <input type="checkbox" name="reminder_estimate_enabled" value="1" {{ old('reminder_estimate_enabled', $setting->reminder_estimate_enabled ?? true) ? 'checked' : '' }} class="rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                                    Activo
                                </label>
                            </div>
                            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Cada cuántos días</label>
                                    <input type="number" name="reminder_estimate_every_days" value="{{ old('reminder_estimate_every_days', $setting->reminder_estimate_every_days ?? 3) }}" min="1" max="60" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Pestaña 7: Inventario / Fotos (captura de fotos) --}}
                    <div id="tab-photos" class="tab-panel hidden">
                        <h3 class="text-lg font-semibold text-gray-800 mb-1">Inventario y fotos</h3>
                        <p class="text-sm text-gray-500 mb-4">Define cómo se toman las fotos en los inventarios vehiculares (check-in) desde el celular.</p>

                        <div class="max-w-xl">
                            <label class="block text-sm font-medium text-gray-700">Modo de captura de fotos</label>
                            <select name="camera_capture_mode" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                <option value="integrated" {{ old('camera_capture_mode', $setting->camera_capture_mode ?? 'integrated') === 'integrated' ? 'selected' : '' }}>Integrada (recomendada)</option>
                                <option value="native" {{ old('camera_capture_mode', $setting->camera_capture_mode ?? 'integrated') === 'native' ? 'selected' : '' }}>Nativa — una foto a la vez</option>
                            </select>
                            <p class="mt-2 text-xs text-gray-500">Integrada: cámara en la página con lente principal, formatos 3:4 / 9:16 / 1:1 (vertical) y 4:3 / 16:9 / 1:1 (horizontal), zoom, cuadrícula y calidad configurable. Requiere HTTPS. Nativa: abre la app de cámara del celular, toma una foto a la vez y regresa; máxima compatibilidad entre equipos.</p>
                        </div>
                    </div>

                    <div class="mt-6 flex gap-2">
                        <button type="submit" class="btn btn-primary">
                            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                            Guardar Configuración
                        </button>
                        <a href="{{ url()->previous() }}" class="btn btn-secondary">Cancelar</a>
                    </div>
                </form>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
    // Pestañas
    document.querySelectorAll('.tab-btn').forEach(btn => {
        btn.addEventListener('click', function () {
            document.querySelectorAll('.tab-btn').forEach(b => {
                b.classList.remove('border-blue-600', 'text-blue-600');
                b.classList.add('border-transparent', 'text-gray-500');
            });
            this.classList.remove('border-transparent', 'text-gray-500');
            this.classList.add('border-blue-600', 'text-blue-600');

            document.querySelectorAll('.tab-panel').forEach(p => p.classList.add('hidden'));
            document.getElementById(this.dataset.tab).classList.remove('hidden');
        });
    });

    // Previsualización en vivo de logo y favicon
    function setupImagePreview(inputId, previewId) {
        const input = document.getElementById(inputId);
        const preview = document.getElementById(previewId);
        if (!input || !preview) return;

        input.addEventListener('change', function () {
            const file = this.files[0];
            if (!file) return;
            const reader = new FileReader();
            reader.onload = e => {
                preview.innerHTML = `<img src="${e.target.result}" alt="Previsualización" class="h-full w-full object-contain">`;
            };
            reader.readAsDataURL(file);
        });
    }

    setupImagePreview('logo-input', 'logo-preview');
    setupImagePreview('favicon-input', 'favicon-preview');

    // Cascada ubigeo (Departamento → Provincia → Distrito)
    if (window.setupUbigeoCascade) {
        window.setupUbigeoCascade('cs', @json($setting->ubigeo_code ?? ''));
    }
    </script>
    @endpush
    @include('partials.ubigeo-cascade')
</x-app-layout>