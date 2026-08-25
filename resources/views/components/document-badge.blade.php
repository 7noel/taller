@props([
    'sn' => null,
    'label' => null,
])

@if ($sn || $label)
    <span {{ $attributes->merge(['class' => 'inline-flex items-center gap-1.5 rounded-md border border-gray-200 bg-gray-50 px-2.5 py-1 font-mono text-sm']) }}
          title="{{ $label ?? $sn }}">
        <svg class="h-4 w-4 shrink-0 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" aria-hidden="true">
            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
        </svg>
        <span class="font-semibold text-gray-800">{{ $sn }}</span>
    </span>
@endif
