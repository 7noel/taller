<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">{{ __($title) }}</h2>
            <div class="flex gap-2">
                <a href="{{ route('cash.index') }}" class="btn btn-secondary">Caja</a>
                @if ($type === 'payment_methods')
                    <a href="{{ route('cash.banks') }}" class="btn btn-secondary">Bancos</a>
                @else
                    <a href="{{ route('cash.payment-methods') }}" class="btn btn-secondary">Métodos de Pago</a>
                @endif
            </div>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8">
            @if (session('success'))
                <div class="mb-4 px-4 py-3 bg-green-50 border border-green-200 text-green-700 rounded-lg text-sm">{{ session('success') }}</div>
            @endif

            <div class="card overflow-hidden">
                <div class="p-4 sm:p-5">
                    <form method="POST"
                        action="{{ $type === 'payment_methods' ? route('cash.payment-methods.store') : route('cash.banks.store') }}"
                        class="mb-4 flex gap-2 max-w-lg">
                        @csrf
                        @if ($type === 'payment_methods')
                            <input type="text" name="code" placeholder="Código (CASH, YAPE...)" required class="block w-40 rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm">
                        @endif
                        <input type="text" name="name" placeholder="Nombre" required class="block flex-1 rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm">
                        <button type="submit" class="btn btn-primary shrink-0">Agregar</button>
                    </form>

                    <table class="min-w-full divide-y divide-gray-200 text-sm">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-3 py-2 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">{{ $type === 'payment_methods' ? 'Código' : 'Nombre' }}</th>
                                @if ($type === 'payment_methods')<th class="px-3 py-2 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">Nombre</th>@endif
                                <th class="px-3 py-2 text-right text-xs font-semibold uppercase tracking-wider text-gray-500">Acciones</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @foreach ($items as $item)
                                <tr>
                                    @if ($type === 'payment_methods')<td class="px-3 py-2 text-gray-800">{{ $item->code }}</td>@endif
                                    <td class="px-3 py-2 text-gray-800">{{ $item->name }}</td>
                                    <td class="px-3 py-2 text-right">
                                        <form method="POST" class="inline"
                                            action="{{ $type === 'payment_methods' ? route('cash.payment-methods.destroy', $item) : route('cash.banks.destroy', $item) }}"
                                            data-confirm="¿Eliminar {{ $item->name }}?">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn-icon btn-icon-red" title="Eliminar">
                                                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
