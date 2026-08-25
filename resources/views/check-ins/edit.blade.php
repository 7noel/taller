<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center gap-3 flex-wrap">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">{{ __('Editar Inventario') }}</h2>
            @if($checkIn->document_sn)
                <x-document-badge :sn="$checkIn->document_sn" />
            @endif
            @if ($checkIn->vehicle?->plate)
                <span class="text-sm text-gray-500">{{ $checkIn->vehicle->plate }}</span>
            @endif
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <form method="POST" action="{{ route('check-ins.update', $checkIn) }}" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')
                        @include('check-ins._form', ['checkIn' => $checkIn, 'checklistItems' => $checklistItems])

                        <div class="mt-6 flex gap-2">
                            <button type="submit" class="inline-flex items-center px-4 py-2 bg-blue-600 rounded-md font-semibold text-xs text-white uppercase hover:bg-blue-700">Actualizar Inventario</button>
                            <a href="{{ route('check-ins.index') }}" class="inline-flex items-center px-4 py-2 bg-gray-200 rounded-md font-semibold text-xs text-gray-700 uppercase hover:bg-gray-300">Cancelar</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>