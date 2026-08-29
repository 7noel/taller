<x-public-layout>
    <div class="max-w-3xl mx-auto px-4 py-6 sm:px-6 lg:px-8">

        <div class="flex items-center justify-between gap-3 mb-5">
            <a href="{{ route('public.work-order', [$vehicle->access_token, $workOrder]) }}" class="inline-flex items-center gap-1.5 text-sm font-medium text-blue-600 hover:text-blue-700 transition-colors duration-150">
                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                Volver al detalle
            </a>
            <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold bg-blue-50 text-blue-700">Encuesta de satisfacción</span>
        </div>

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

        @if ($survey)
            <div class="card p-8 text-center">
                <svg class="mx-auto h-12 w-12 text-green-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                <h1 class="mt-3 text-lg font-semibold text-gray-800">¡Gracias por tu respuesta!</h1>
                <p class="mt-1 text-sm text-gray-500">Tu opinión nos ayuda a mejorar el servicio de tu vehículo {{ $vehicle->plate }}.</p>
            </div>
        @else
            <div class="card mb-4">
                <div class="p-6">
                    <h1 class="text-xl font-semibold text-gray-800 mb-1">Encuesta de satisfacción</h1>
                    <p class="text-sm text-gray-500">Cuéntanos cómo fue tu experiencia con la reparación de tu vehículo {{ $vehicle->plate }} (OT {{ $workOrder->document_sn ?? '' }}).</p>
                </div>
            </div>

            <form method="POST" action="{{ route('public.work-order.survey.store', [$vehicle->access_token, $workOrder]) }}" class="space-y-4">
                @csrf

                @foreach ($template->sections as $section)
                    <div class="card">
                        <div class="p-6">
                            <h2 class="text-sm font-semibold text-gray-700 uppercase tracking-wider mb-3">{{ $section->name }}</h2>

                            @foreach ($section->items as $item)
                                @php
                                    $key = $item->key;
                                    $required = $item->is_required;
                                @endphp
                                <div class="mb-4 last:mb-0">
                                    <p class="block text-sm font-medium text-gray-700">
                                        {{ $item->label }}
                                        @if ($required) <span class="text-red-500">*</span> @endif
                                    </p>

                                    @if ($item->type === 'radio')
                                        <div class="mt-2 space-y-1.5">
                                            @foreach ($item->option_list as $opt)
                                                <label class="flex items-center gap-2 text-sm text-gray-700 cursor-pointer">
                                                    <input type="radio" name="answers[{{ $key }}]" value="{{ $opt['value'] }}" @if (old('answers.' . $key) === $opt['value']) checked @endif @if ($required) required @endif class="border-gray-300 text-blue-600 focus:ring-blue-500">
                                                    <span>{{ $opt['label'] }}</span>
                                                </label>
                                            @endforeach
                                        </div>
                                    @elseif ($item->type === 'text')
                                        <input type="text" name="answers[{{ $key }}]" value="{{ old('answers.' . $key) }}" @if ($required) required @endif class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                    @elseif ($item->type === 'textarea')
                                        <textarea name="answers[{{ $key }}]" rows="3" @if ($required) required @endif class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">{{ old('answers.' . $key) }}</textarea>
                                    @elseif ($item->type === 'select')
                                        <select name="answers[{{ $key }}]" @if ($required) required @endif class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                            <option value="">Seleccionar...</option>
                                            @foreach ($item->option_list as $opt)
                                                <option value="{{ $opt['value'] }}" @if (old('answers.' . $key) === $opt['value']) selected @endif>{{ $opt['label'] }}</option>
                                            @endforeach
                                        </select>
                                    @endif

                                    @error('answers.' . $key)
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endforeach

                <div class="flex justify-end">
                    <button type="submit" class="btn btn-primary">Enviar encuesta</button>
                </div>
            </form>
        @endif
    </div>
</x-public-layout>

