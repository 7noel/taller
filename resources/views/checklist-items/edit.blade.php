<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">{{ __('Editar Ítem del Checklist') }}</h2>
            <a href="{{ route('checklist-items.index') }}" class="btn btn-secondary">Volver</a>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8">
            @if ($errors->any())
                <div class="mb-4 px-4 py-3 bg-red-50 border border-red-200 text-red-700 rounded-lg text-sm">{{ $errors->first() }}</div>
            @endif

            <form method="POST" action="{{ route('checklist-items.update', $checkInChecklistItem) }}" class="card">
                @csrf @method('PUT')
                <div class="p-6">
                    @include('checklist-items._form', ['checkInChecklistItem' => $checkInChecklistItem])
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
