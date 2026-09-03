<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">{{ __('Nuevo Presupuesto') }}</h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            @if (!empty($warrantyOf))
                <div class="mb-4 px-4 py-3 bg-amber-50 border border-amber-200 text-amber-800 rounded-lg text-sm">
                    <strong>Registrando garantía</strong> del presupuesto
                    <a href="{{ route('estimates.show', $warrantyOf) }}" class="font-semibold underline">{{ $warrantyOf->document_sn }}</a>
                    ({{ $warrantyOf->vehicle?->plate }} · {{ $warrantyOf->client?->display_name }}).
                    El documento nacerá en <strong>reparación</strong>, será <strong>no facturable</strong> y el gasto lo asume el taller.
                </div>
            @endif
            <form method="POST" action="{{ route('estimates.store') }}">
                @csrf
                @if (!empty($warrantyOf))
                    <input type="hidden" name="warranty_of_estimate_id" value="{{ $warrantyOf->id }}">
                @endif
                @if (!empty($workOrderId))
                    <input type="hidden" name="work_order_id" value="{{ $workOrderId }}">
                @endif
                @include('estimates._form', ['estimate' => null, 'checkIn' => $checkIn ?? null, 'parentEstimate' => $parentEstimate ?? null, 'warrantyOf' => $warrantyOf ?? null, 'advisors' => $advisors, 'technicians' => $technicians, 'establishment' => $establishment])

                <div class="mt-6 flex gap-2">
                    <div class="flex flex-wrap justify-end gap-2">
                        <button type="submit" class="btn btn-primary">{{ !empty($warrantyOf) ? 'Guardar Garantía' : 'Guardar Presupuesto' }}</button>
                        <a href="{{ route('estimates.index') }}" class="btn btn-secondary">Cancelar</a>
                    </div>
                </div>
            </form>
        </div>
    </div>

    @include('partials.contact-modal')
    @include('estimates._item-modal')
    @include('estimates._third-party-order-modal')
    @include('check-ins._vehicle_modal')
    @include('estimates._form-scripts', ['estimate' => null, 'checkIn' => $checkIn ?? null, 'parentEstimate' => $parentEstimate ?? null, 'warrantyOf' => $warrantyOf ?? null, 'technicians' => $technicians, 'establishment' => $establishment])
</x-app-layout>