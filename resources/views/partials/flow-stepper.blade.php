{{-- Stepper del flujo del taller (portal del cliente).
     Requiere: $activeIndex (0=Ingreso, 1=Presupuesto, 2=Reparación, 3=Entrega). --}}
@php
    $steps = ['Ingreso', 'Presupuesto', 'Reparación', 'Entrega'];
    $active = (int) ($activeIndex ?? 0);
@endphp
<ol class="flex items-center">
    @foreach ($steps as $i => $label)
        @if ($i > 0)
            <div class="h-px flex-1 mx-1 bg-gray-200 {{ $i <= $active ? 'bg-blue-300' : '' }}"></div>
        @endif
        <li class="flex flex-col items-center gap-1.5">
            @if ($i < $active)
                <span class="flex h-7 w-7 items-center justify-center rounded-full bg-blue-600 text-white shadow-sm">
                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                </span>
                <span class="text-[11px] leading-none text-blue-700 font-semibold">{{ $label }}</span>
            @elseif ($i === $active)
                <span class="flex h-7 w-7 items-center justify-center rounded-full bg-blue-600 text-white shadow-sm">{{ $i + 1 }}</span>
                <span class="text-[11px] leading-none text-blue-700 font-semibold">{{ $label }}</span>
            @else
                <span class="flex h-7 w-7 items-center justify-center rounded-full bg-gray-100 text-gray-400">{{ $i + 1 }}</span>
                <span class="text-[11px] leading-none text-gray-400">{{ $label }}</span>
            @endif
        </li>
    @endforeach
</ol>
