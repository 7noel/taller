<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">{{ __('Nuevo Establecimiento') }}</h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="card overflow-hidden">
                <div class="p-6 text-gray-900">
                    <form method="POST" action="{{ route('establishments.store') }}" id="establishment-form">
                        @csrf
                        @include('establishments._form')

                        <div class="mt-6 flex flex-wrap gap-2">
                            <button type="submit" class="btn btn-primary">
                                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                                Guardar
                            </button>
                            <a href="{{ route('establishments.index') }}" class="btn btn-secondary">Cancelar</a>
                        </div>
                    </form>

                    <div class="mt-6 px-4 py-3 bg-blue-50 border border-blue-200 text-blue-700 rounded-lg text-sm">
                        Al guardar se generarán automáticamente todas las series de documentos (FTR1, BLT1, IV01, etc.) para este establecimiento.
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>