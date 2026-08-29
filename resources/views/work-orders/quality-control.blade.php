<x-app-layout>
    @php
        $statusColors = [
            'open' => 'bg-gray-100 text-gray-800',
            'in_progress' => 'bg-blue-100 text-blue-800',
            'waiting_parts' => 'bg-yellow-100 text-yellow-800',
            'quality_control' => 'bg-purple-100 text-purple-800',
            'ready_for_delivery' => 'bg-teal-100 text-teal-800',
            'delivered' => 'bg-green-100 text-green-800',
            'delivered_pending' => 'bg-orange-100 text-orange-800',
            'closed' => 'bg-gray-100 text-gray-500',
        ];
    @endphp

    <x-slot name="header">
        <div class="flex flex-wrap justify-between items-center gap-3">
            <div class="flex flex-wrap items-center gap-3">
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">{{ __('Control de Calidad') }}</h2>
                @if ($workOrder->document_sn)
                    <x-document-badge :sn="$workOrder->document_sn" />
                @endif
                @if ($workOrder->vehicle?->plate)
                    <span class="text-sm text-gray-500">{{ $workOrder->vehicle->plate }} · {{ $workOrder->vehicle?->vehicleModel?->brand?->name }} {{ $workOrder->vehicle?->vehicleModel?->name }}</span>
                @endif
                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $statusColors[$workOrder->status] ?? 'bg-gray-100 text-gray-800' }}">
                    {{ $workOrder->status_label }}
                </span>
            </div>
            <a href="{{ route('work-orders.show', $workOrder) }}" class="btn btn-secondary">Volver a la OT</a>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            @if (session('success'))
                <div class="mb-4 px-4 py-3 bg-green-50 border border-green-200 text-green-700 rounded-lg text-sm">{{ session('success') }}</div>
            @endif
            @if ($errors->any())
                <div class="mb-4 px-4 py-3 bg-red-50 border border-red-200 text-red-700 rounded-lg text-sm">
                    @foreach ($errors->all() as $error)
                        <p>{{ $error }}</p>
                    @endforeach
                </div>
            @endif

            <div class="mb-4 px-4 py-3 bg-blue-50 border border-blue-200 text-blue-700 rounded-lg text-sm">
                <strong>Próxima acción:</strong> {{ $workOrder->next_action }} Revisa el formulario completo y decide si el vehículo cumple con los estándares de calidad.
            </div>

            @if ($pendingAssignments > 0)
                <div class="mb-4 px-4 py-3 bg-amber-50 border border-amber-200 text-amber-700 rounded-lg text-sm">
                    <strong>Asignaciones sin terminar:</strong> hay {{ $pendingAssignments }} asignación(es) pendiente(s) o en progreso.
                    @if ($qcGuardRequired)
                        No se podrá aprobar el control de calidad hasta completarlas.
                    @else
                        (Solo advertencia: la empresa permite aprobar con asignaciones pendientes.)
                    @endif
                </div>
            @endif

            <form method="POST" action="{{ route('work-orders.quality-control.store', $workOrder) }}" data-confirm="¿Confirmar la acción de control de calidad?" id="qc-form">
                @csrf
                <input type="hidden" name="result" id="qc-result" value="">
                <input type="hidden" name="rejection_reason" id="qc-rejection-reason" value="">
                <input type="hidden" name="rejection_details" id="qc-rejection-details" value="">

                @foreach ($template->sections as $section)
                    <div class="card mb-4">
                        <div class="p-4 sm:p-5">
                            <h3 class="font-semibold text-sm text-gray-800 uppercase tracking-wider mb-3">{{ $section->name }}</h3>

                            @foreach ($section->items as $item)
                                @php
                                    $key = $item->key;
                                    $required = $item->is_required;
                                @endphp
                                <div class="mb-4 last:mb-0">
                                    <label class="block text-sm font-medium text-gray-700">
                                        {{ $item->label }}
                                        @if ($required) <span class="text-red-500">*</span> @endif
                                    </label>

                                    @if ($item->type === 'select')
                                        <select name="answers[{{ $key }}]" @if ($required) required @endif class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                            <option value="">Seleccionar...</option>
                                            @foreach ($item->option_list as $opt)
                                                <option value="{{ $opt['value'] }}" @if (old('answers.' . $key) === $opt['value']) selected @endif>{{ $opt['label'] }}</option>
                                            @endforeach
                                        </select>
                                    @elseif ($item->type === 'number')
                                        <input type="number" name="answers[{{ $key }}]" value="{{ old('answers.' . $key) }}" min="0" step="1" @if ($required) required @endif class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                    @elseif ($item->type === 'checkbox')
                                        <label class="mt-2 flex items-start gap-2 text-sm text-gray-700 cursor-pointer">
                                            <input type="checkbox" name="answers[{{ $key }}]" value="1" @if (old('answers.' . $key)) checked @endif class="mt-0.5 rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                                            <span>{{ $item->label }}</span>
                                        </label>
                                    @elseif ($item->type === 'radio')
                                        <div class="mt-2 space-y-1.5">
                                            @foreach ($item->option_list as $opt)
                                                <label class="flex items-center gap-2 text-sm text-gray-700 cursor-pointer">
                                                    <input type="radio" name="answers[{{ $key }}]" value="{{ $opt['value'] }}" @if (old('answers.' . $key) === $opt['value']) checked @endif @if ($required) required @endif class="border-gray-300 text-blue-600 focus:ring-blue-500">
                                                    <span>{{ $opt['label'] }}</span>
                                                </label>
                                            @endforeach
                                        </div>
                                    @elseif ($item->type === 'textarea')
                                        <textarea name="answers[{{ $key }}]" rows="3" @if ($required) required @endif class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">{{ old('answers.' . $key) }}</textarea>
                                    @else
                                        <input type="text" name="answers[{{ $key }}]" value="{{ old('answers.' . $key) }}" @if ($required) required @endif class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                    @endif

                                    @error('answers.' . $key)
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endforeach

                <div class="card mb-4">
                    <div class="p-4 sm:p-5 flex flex-wrap gap-2 justify-end">
                        <button type="button" id="qc-reject-btn" class="btn btn-danger">
                            <svg class="h-4 w-4 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                            Rechazar control de calidad
                        </button>
                        <button type="button" id="qc-approve-btn" class="btn btn-primary" @if ($qcGuardRequired && $pendingAssignments > 0) disabled @endif>
                            <svg class="h-4 w-4 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                            Aprobar control de calidad
                        </button>
                    </div>
                    @if ($qcGuardRequired && $pendingAssignments > 0)
                        <p class="px-4 pb-4 text-xs text-amber-600">Completa las asignaciones pendientes para habilitar la aprobación.</p>
                    @endif
                </div>
            </form>
        </div>
    </div>

    {{-- Modal: causa de rechazo --}}
    <div id="qc-reject-modal" class="hidden fixed inset-0 z-50 overflow-y-auto" aria-hidden="true">
        <div class="flex min-h-full items-center justify-center p-4">
            <div class="fixed inset-0 bg-gray-500/75" data-qc-reject-close></div>
            <div class="relative w-full max-w-lg rounded-lg bg-white shadow-xl">
                <div class="px-5 py-4 border-b border-gray-200 flex justify-between items-center">
                    <h3 class="text-base font-semibold text-gray-800">Rechazar control de calidad</h3>
                    <button type="button" data-qc-reject-close class="text-gray-400 hover:text-gray-600" aria-label="Cerrar">
                        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                </div>
                <div class="p-5">
                    <p class="text-sm text-gray-600 mb-3">Indica la causa por la que el vehículo no aprueba el control de calidad:</p>
                    <div class="space-y-2">
                        @foreach ($rejectionReasons as $reasonKey => $reasonLabel)
                            <label class="flex items-start gap-2 text-sm text-gray-700 cursor-pointer">
                                <input type="radio" name="rejection_reason_radio" value="{{ $reasonKey }}" class="mt-0.5 border-gray-300 text-red-600 focus:ring-red-500">
                                <span>{{ $reasonLabel }}</span>
                            </label>
                        @endforeach
                    </div>
                    <div class="mt-4">
                        <label class="block text-sm font-medium text-gray-700">Detalle (opcional)</label>
                        <textarea id="qc-rejection-details-input" rows="3" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"></textarea>
                    </div>
                    <div class="mt-5 flex flex-wrap gap-2 justify-end">
                        <button type="button" data-qc-reject-close class="btn btn-secondary">Cancelar</button>
                        <button type="button" id="qc-reject-confirm" class="btn btn-danger">Confirmar rechazo</button>
                    </div>
                    <p id="qc-reject-error" class="hidden mt-3 text-sm text-red-600">Debe indicar la causa del rechazo.</p>
                </div>
            </div>
        </div>
    </div>


    @push('scripts')
    <script>
        (function () {
            'use strict';

            const form = document.getElementById('qc-form');
            const result = document.getElementById('qc-result');
            const rejectReason = document.getElementById('qc-rejection-reason');
            const rejectDetails = document.getElementById('qc-rejection-details');

            function setConfirmMessage(message) {
                form.setAttribute('data-confirm', message);
            }

            // Aprobar: solo confirmación.
            const approveBtn = document.getElementById('qc-approve-btn');
            if (approveBtn) {
                approveBtn.addEventListener('click', function () {
                    if (approveBtn.disabled) return;
                    result.value = 'approved';
                    rejectReason.value = '';
                    rejectDetails.value = '';
                    setConfirmMessage('¿Aprobar el control de calidad? La OT pasará a "Lista para entrega" y podrás avisar al cliente.');
                    form.requestSubmit();
                });
            }

            // Rechazar: primero la causa (modal), luego la confirmación global.
            const rejectModal = document.getElementById('qc-reject-modal');
            const detailsInput = document.getElementById('qc-rejection-details-input');

            function openRejectModal() {
                rejectModal.classList.remove('hidden');
                rejectModal.setAttribute('aria-hidden', 'false');
                document.body.style.overflow = 'hidden';
            }

            function closeRejectModal() {
                rejectModal.classList.add('hidden');
                rejectModal.setAttribute('aria-hidden', 'true');
                document.body.style.overflow = '';
            }

            document.querySelectorAll('[data-qc-reject-close]').forEach(function (el) {
                el.addEventListener('click', closeRejectModal);
            });
            document.addEventListener('keydown', function (e) {
                if (e.key === 'Escape') closeRejectModal();
            });

            document.getElementById('qc-reject-btn').addEventListener('click', function () {
                document.querySelectorAll('input[name="rejection_reason_radio"]').forEach(function (r) { r.checked = false; });
                detailsInput.value = '';
                errorEl.classList.add('hidden');
                openRejectModal();
            });

            const errorEl = document.getElementById('qc-reject-error');

            document.getElementById('qc-reject-confirm').addEventListener('click', function () {
                const selected = document.querySelector('input[name="rejection_reason_radio"]:checked');
                if (!selected) {
                    errorEl.classList.remove('hidden');
                    return;
                }
                result.value = 'rejected';
                rejectReason.value = selected.value;
                rejectDetails.value = detailsInput.value.trim();
                closeRejectModal();
                setConfirmMessage('¿Rechazar el control de calidad? La OT volverá a reparación con la causa indicada.');
                form.requestSubmit();
            });
        })();
    </script>
    @endpush
</x-app-layout>

