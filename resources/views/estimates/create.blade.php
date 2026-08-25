<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">{{ __('Nuevo Presupuesto') }}</h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <form method="POST" action="{{ route('estimates.store') }}">
                @csrf
                @include('estimates._form', ['estimate' => null, 'checkIn' => $checkIn ?? null, 'advisors' => $advisors, 'establishment' => $establishment])

                <div class="mt-6 flex gap-2">
                    <button type="submit" class="btn btn-primary">Guardar Presupuesto</button>
                    <a href="{{ route('estimates.index') }}" class="btn btn-secondary">Cancelar</a>
                </div>
            </form>
        </div>
    </div>

    @include('partials.contact-modal')
    @include('estimates._form-scripts', ['estimate' => null, 'checkIn' => $checkIn ?? null, 'establishment' => $establishment])
</x-app-layout>