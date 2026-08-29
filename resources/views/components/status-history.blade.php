@props(['subject'])

@if ($subject->statusHistory->isNotEmpty())
    <div class="card">
        <div class="p-6">
            <h3 class="text-lg font-semibold text-gray-800 mb-4">Historial de estados</h3>
            <ul class="space-y-2 text-sm">
                @foreach ($subject->statusHistory as $history)
                    <li class="flex flex-wrap items-center gap-2">
                        <span class="text-gray-500">{{ $history->created_at?->format('d/m/Y H:i') }}</span>
                        <span class="font-medium">{{ $subject::STATUS_LABELS[$history->from_status] ?? $history->from_status }} → {{ $subject::STATUS_LABELS[$history->to_status] ?? $history->to_status }}</span>
                        @if ($history->user)
                            <span class="text-gray-400">por {{ $history->user->name }}</span>
                        @elseif ($history->actor_type === 'client')
                            <span class="text-gray-400">por el cliente</span>
                        @endif
                        <span class="text-xs px-2 py-0.5 rounded-md {{ $history->actor_type === 'client' ? 'bg-amber-50 text-amber-700' : ($history->actor_type === 'system' ? 'bg-gray-100 text-gray-600' : 'bg-blue-50 text-blue-700') }}">{{ $history->actor_label }}</span>
                        @if ($history->comments)
                            <span class="text-gray-600">— {{ $history->comments }}</span>
                        @endif
                    </li>
                @endforeach
            </ul>
        </div>
    </div>
@endif
