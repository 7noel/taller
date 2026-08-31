<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">{{ __('Editar Cita') }}</h2>
            <a href="{{ route('appointments.show', $appointment) }}" class="btn btn-secondary">Volver</a>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="card p-6">
                @include('appointments._form')
            </div>
        </div>
    </div>
</x-app-layout>
