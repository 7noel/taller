@php
    $active = request()->routeIs($item['active']);
    $linkClasses = $active
        ? 'bg-blue-50 text-blue-700'
        : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900';
@endphp
<a href="{{ route($item['route']) }}" title="{{ $item['label'] }}"
   class="nav-item relative flex items-center gap-3 rounded-md px-3 py-2 text-sm font-medium transition-colors duration-150 ease-out {{ !empty($submenu) ? 'pl-11' : '' }} {{ $linkClasses }}">
    @if ($active)
        <span class="absolute left-0 top-1/2 h-5 w-0.5 -translate-y-1/2 rounded-full bg-blue-600"></span>
    @endif
    @if (!empty($icon))
        <svg class="h-5 w-5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
            <path stroke-linecap="round" stroke-linejoin="round" d="{{ $icon }}"/>
        </svg>
    @endif
    <span class="nav-label min-w-0 flex-1 truncate">{{ $item['label'] }}</span>
</a>