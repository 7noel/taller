<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">{{ __('Editar Establecimiento') }}: {{ $establishment->name }}</h2>
            <a href="{{ route('establishments.index') }}" class="btn btn-secondary">
                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                Volver
            </a>
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
                <div class="p-6 text-gray-900">
                    <form method="POST" action="{{ route('establishments.update', $establishment) }}" id="establishment-form">
                        @csrf
                        @method('PUT')
                        @include('establishments._form')

                        <div class="mt-6 flex flex-wrap gap-2">
                            <button type="submit" class="btn btn-primary">
                                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                                Guardar
                            </button>
                            <a href="{{ route('establishments.index') }}" class="btn btn-secondary">Cancelar</a>
                        </div>
                    </form>

                    {{-- Acciones rápidas --}}
                    <div class="mt-6 pt-6 border-t border-gray-200">
                        <h3 class="text-sm font-semibold text-gray-700 mb-3">Acciones rápidas</h3>
                        <div class="flex flex-wrap gap-2">
                            <form method="POST" action="{{ route('establishments.copy-from-company', $establishment) }}" data-confirm="¿Copiar dirección, teléfono, celular, email y ubigeo desde la configuración de empresa? No se copiará RUC, Razón Social ni Nombre Comercial.">
                                @csrf
                                <button type="submit" class="btn btn-secondary">
                                    <svg class="h-4 w-4 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h9a2 2 0 002-2v-3m2-8V4a1 1 0 00-1-1h-3m4 0v4a1 1 0 01-1 1h-4a1 1 0 01-1-1V4m-4 8l-3 3m0 0l3 3m-3-3h12"/></svg>
                                    Copiar datos de la empresa
                                </button>
                            </form>
                            <a href="{{ route('establishments.series.index', $establishment) }}" class="btn btn-secondary">
                                <svg class="h-4 w-4 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/></svg>
                                Ver series
                            </a>
                            <form method="POST" action="{{ route('establishments.regenerate-series', $establishment) }}" data-confirm="¿Regenerar las series faltantes para este establecimiento? No se modificarán las existentes.">
                                @csrf
                                <button type="submit" class="btn btn-secondary">
                                    <svg class="h-4 w-4 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
                                    Regenerar series
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>