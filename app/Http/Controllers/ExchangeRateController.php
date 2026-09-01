<?php

namespace App\Http\Controllers;

use App\Models\ExchangeRate;
use App\Services\ExchangeRateService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;

class ExchangeRateController extends Controller
{
    public function __construct(protected ExchangeRateService $service)
    {
    }

    public function index(): View
    {
        Gate::authorize('viewAny', ExchangeRate::class);

        $rates = ExchangeRate::query()
            ->orderByDesc('date')
            ->orderByDesc('id')
            ->limit(200)
            ->get();

        return view('exchange-rates.index', compact('rates'));
    }

    public function store(Request $request): RedirectResponse
    {
        Gate::authorize('create', ExchangeRate::class);

        $data = $request->validate([
            'date' => ['required', 'date'],
            'currency' => ['required', 'string', 'in:PEN,USD'],
            'buy_rate' => ['required', 'numeric', 'min:0'],
            'sell_rate' => ['required', 'numeric', 'min:0'],
            'source' => ['nullable', 'string', 'max:100'],
        ]);

        ExchangeRate::updateOrCreate(
            ['date' => $data['date'], 'currency' => $data['currency']],
            [
                'buy_rate' => $data['buy_rate'],
                'sell_rate' => $data['sell_rate'],
                'source' => $data['source'] ?? null,
            ]
        );

        return back()->with('success', 'Tipo de cambio guardado correctamente.');
    }

    public function destroy(ExchangeRate $exchangeRate): RedirectResponse
    {
        Gate::authorize('delete', $exchangeRate);

        $exchangeRate->delete();

        return back()->with('success', 'Tipo de cambio eliminado.');
    }

    /**
     * API: último tipo de cambio (venta) para sugerir en los formularios.
     */
    public function latest(Request $request): JsonResponse
    {
        $currency = strtoupper((string) $request->query('currency', 'USD'));

        if (!in_array($currency, ['PEN', 'USD'], true)) {
            return response()->json(['rate' => 1.0]);
        }

        // Bajo demanda: garantiza el T.C. del día (BD → SUNAT → último registrado).
        if ($currency !== 'PEN') {
            $this->service->ensureRateForDate(now()->toDateString(), $currency);
        }

        return response()->json(['rate' => $this->service->suggestRate($currency)]);
    }
}
