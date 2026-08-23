@php $results = $isEdit ? $checkIn->checklistResults->keyBy('checklist_item_id') : collect(); @endphp
<div class="border-b border-gray-200 pb-6 mb-6">
    <div class="flex flex-wrap items-center justify-between gap-3 mb-4">
        <h3 class="text-lg font-semibold text-gray-800">✅ Checklist del vehículo</h3>
        <label class="inline-flex items-center gap-2 text-sm font-medium text-gray-700 cursor-pointer select-none">
            <input type="checkbox" id="only-issues" class="rounded border-gray-300 text-blue-600 shadow-sm focus:ring-blue-500">
            Ver solo regulares y malos
        </label>
    </div>

    <div class="flex flex-wrap gap-4 mb-4 p-3 bg-gray-50 rounded-lg border border-gray-200 text-xs text-gray-600">
        <span class="inline-flex items-center gap-1.5"><span class="inline-flex w-6 h-6 rounded-full bg-green-600 text-white items-center justify-center text-sm font-bold">✓</span> Bueno</span>
        <span class="inline-flex items-center gap-1.5"><span class="inline-flex w-6 h-6 rounded-full bg-amber-500 text-white items-center justify-center text-sm font-bold">▲</span> Regular</span>
        <span class="inline-flex items-center gap-1.5"><span class="inline-flex w-6 h-6 rounded-full bg-red-600 text-white items-center justify-center text-sm font-bold">✕</span> Malo</span>
        <span class="inline-flex items-center gap-1.5"><span class="inline-flex w-6 h-6 rounded-full bg-gray-700 text-white items-center justify-center text-sm font-bold">●</span> No aplica</span>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-4" id="checklist-grid">
        @foreach ($checklistItems as $item)
            @php $status = old("checklist.{$item->id}.status", $results->get($item->id)?->status ?? ''); $obs = old("checklist.{$item->id}.observations", $results->get($item->id)?->observations ?? ''); @endphp
            <div class="checklist-card bg-white border border-gray-200 rounded-lg p-4 {{ in_array($status, ['regular','bad']) ? 'has-issue' : '' }}">
                <div class="flex items-start justify-between gap-2">
                    <p class="font-medium text-gray-800 text-sm leading-snug flex-1">{{ $item->name }}</p>
                    <div class="flex items-center gap-1 shrink-0">
                        @foreach (['good'=>['✓','green'],'regular'=>['▲','amber'],'bad'=>['✕','red'],'not_applicable'=>['●','gray']] as $state => [$sym, $color])
                        <button type="button" class="checklist-btn w-8 h-8 rounded-full border-2 flex items-center justify-center text-sm font-bold transition-colors {{ $status === $state ? "bg-{$color}-600 text-white border-transparent" : "border-{$color}-500 text-{$color}-500 hover:bg-{$color}-50" }}" data-state="{{ $state }}" data-card="{{ $loop->parent->index }}">{{ $sym }}</button>
                        @endforeach
                    </div>
                </div>
                <div class="mt-3">
                    <input type="hidden" name="checklist[{{ $item->id }}][status]" value="{{ $status }}" class="checklist-status-input">
                    <input type="text" name="checklist[{{ $item->id }}][observations]" value="{{ $obs }}" maxlength="500" class="checklist-obs w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm" placeholder="Nota...">
                </div>
            </div>
        @endforeach
    </div>
</div>