{{-- =============================================================
     Panel izquierdo reutilizable del login split-screen (taller).
     Temática: taller mecánico (azul petróleo/gris acero, engranajes,
     líneas tipo blueprint). Se usa con <x-guest-layout variant="split">.
     En móvil se oculta (solo el formulario es visible).
     ============================================================= --}}
<aside class="hidden lg:flex relative overflow-hidden lg:h-screen lg:flex-col lg:justify-between bg-slate-900">
    {{-- Gradiente azul petróleo --}}
    <div class="absolute inset-0 bg-gradient-to-br from-[#0c3a40] via-[#0a2f34] to-[#071f26]"></div>

    {{-- Patrón decorativo: engranajes dispersos (taller) --}}
    <div class="absolute inset-0 text-white opacity-[0.08]" aria-hidden="true">
        <svg class="absolute -top-10 -left-10 h-64 w-64" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="0.75"><path d="M12 2v3m0 14v3M2 12h3m14 0h3M4.93 4.93l2.12 2.12m9.9 9.9l2.12 2.12m0-14.14l-2.12 2.12M7.05 16.95l-2.12 2.12"/><circle cx="12" cy="12" r="5"/><circle cx="12" cy="12" r="1.6"/></svg>
        <svg class="absolute top-1/3 right-8 h-40 w-40" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="0.75"><path d="M12 2v3m0 14v3M2 12h3m14 0h3M4.93 4.93l2.12 2.12m9.9 9.9l2.12 2.12m0-14.14l-2.12 2.12M7.05 16.95l-2.12 2.12"/><circle cx="12" cy="12" r="5"/><circle cx="12" cy="12" r="1.6"/></svg>
        <svg class="absolute bottom-16 -left-6 h-56 w-56" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="0.75"><path d="M12 2v3m0 14v3M2 12h3m14 0h3M4.93 4.93l2.12 2.12m9.9 9.9l2.12 2.12m0-14.14l-2.12 2.12M7.05 16.95l-2.12 2.12"/><circle cx="12" cy="12" r="5"/><circle cx="12" cy="12" r="1.6"/></svg>
    </div>

    {{-- Línea de retícula tipo taller --}}
    <div class="absolute inset-x-0 top-0 h-px bg-gradient-to-r from-transparent via-teal-400/30 to-transparent" aria-hidden="true"></div>

    <div class="relative z-10 flex h-full flex-col items-center justify-between p-12 text-center">
        {{-- Logo + nombre --}}
        <div>
            <div class="mx-auto flex h-20 w-20 items-center justify-center rounded-2xl bg-white/10 ring-1 ring-white/15 shadow-lg shadow-black/20">
                <svg class="h-10 w-10 text-teal-300" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 2v4m0 12v4m10-10h-4M6 12H2m17.07-7.07l-2.83 2.83M9.17 14.83l-2.83 2.83m14.14 0l-2.83-2.83M9.17 9.17L6.34 6.34"/>
                    <circle cx="12" cy="12" r="4.5"/>
                    <circle cx="12" cy="12" r="1.5" fill="currentColor" stroke="none"/>
                </svg>
            </div>
            <h1 class="mt-6 text-2xl font-bold tracking-tight text-white">{{ config('app.name', 'Taller Mecánico') }}</h1>
            <p class="mt-2 text-sm text-teal-100/80">Control total del taller, del ingreso a la entrega.</p>
        </div>

        {{-- Etapas del flujo --}}
        <div class="w-full max-w-sm">
            <p class="text-xs font-semibold uppercase tracking-widest text-teal-300/80">El flujo del vehículo</p>
            <ul class="mt-4 space-y-3">
                @foreach ([
                    ['icon' => 'M9 12l2 2 4-4', 'label' => 'Inventario con checklist y daños'],
                    ['icon' => 'M9 17a2 2 0 11-4 0 2 2 0 014 0zm10 0a2 2 0 11-4 0 2 2 0 014 0z', 'label' => 'Presupuestos y aprobaciones'],
                    ['icon' => 'M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z', 'label' => 'Órdenes de trabajo por etapas'],
                    ['icon' => 'M4 6h16M4 10h16M4 14h16M4 18h16', 'label' => 'Control de calidad y entrega'],
                ] as $feature)
                    <li class="flex items-center gap-3 text-left text-sm text-slate-200">
                        <span class="flex h-7 w-7 shrink-0 items-center justify-center rounded-md bg-white/10 ring-1 ring-white/15">
                            <svg class="h-3.5 w-3.5 text-teal-300" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="{{ $feature['icon'] }}"/></svg>
                        </span>
                        {{ $feature['label'] }}
                    </li>
                @endforeach
            </ul>
        </div>

        {{-- Footer --}}
        <p class="text-xs text-white/50">&copy; {{ date('Y') }} {{ config('app.name', 'Taller Mecánico') }}</p>
    </div>
</aside>