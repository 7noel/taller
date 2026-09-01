<?php

namespace App\Http\Controllers;

use App\Models\CashMovement;
use App\Models\CashRegister;
use App\Models\Estimate;
use App\Services\Facturacion\CashService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Redirect;
use Illuminate\View\View;

class CashController extends Controller
{
    public function __construct(protected CashService $cashService)
    {
    }

    public function index(): View
    {
        Gate::authorize('viewAny', CashRegister::class);

        $register = $this->cashService->currentRegister();
        $registers = CashRegister::query()->with('openedBy')->latest('id')->limit(10)->get();

        return view('cash.index', compact('register', 'registers'));
    }

    public function movements(Request $request): JsonResponse
    {
        Gate::authorize('viewAny', CashRegister::class);

        $query = CashMovement::query()
            ->with(['cashRegister', 'paymentMethod', 'bank'])
            ->latest('id');

        if ($request->filled('cash_register_id')) {
            $query->where('cash_register_id', $request->integer('cash_register_id'));
        }

        return response()->json($query->limit($request->integer('limit', 200))->get()->map(fn (CashMovement $m) => [
            'id' => $m->id,
            'date' => $m->movement_date?->format('d/m/Y'),
            'type' => $m->type,
            'type_label' => $m->type_label,
            'amount' => number_format($m->amount, 2),
            'payment_method' => $m->paymentMethod?->name,
            'bank' => $m->bank?->name,
            'description' => $m->description,
            'reference' => $m->reference,
            'register' => $m->cashRegister?->name,
        ]));
    }

    public function open(Request $request): RedirectResponse
    {
        Gate::authorize('create', CashRegister::class);

        $data = $request->validate([
            'name' => 'nullable|string|max:100',
            'opening_amount' => 'nullable|numeric|min:0',
            'notes' => 'nullable|string|max:1000',
        ]);

        try {
            $this->cashService->open($data);
        } catch (\InvalidArgumentException $e) {
            return back()->withErrors(['general' => $e->getMessage()]);
        }

        return back()->with('success', 'Caja abierta.');
    }

    public function close(Request $request, CashRegister $register): RedirectResponse
    {
        Gate::authorize('update', $register);

        $data = $request->validate([
            'closing_amount' => 'nullable|numeric|min:0',
            'notes' => 'nullable|string|max:1000',
        ]);

        try {
            $this->cashService->close($register, $data);
        } catch (\InvalidArgumentException $e) {
            return back()->withErrors(['general' => $e->getMessage()]);
        }

        return back()->with('success', 'Caja cerrada y arqueada.');
    }

    /**
     * Registra un adelanto sobre un presupuesto: cobro + factura de adelanto.
     */
    public function advance(Request $request, Estimate $estimate): RedirectResponse
    {
        Gate::authorize('create', CashRegister::class);

        $data = $request->validate([
            'amount' => 'required|numeric|min:0.01',
            'party_id' => 'required|exists:parties,id',
            'payment_method_id' => 'nullable|exists:payment_methods,id',
            'bank_id' => 'nullable|exists:banks,id',
            'reference' => 'nullable|string|max:100',
            'payment_date' => 'nullable|date',
            'notes' => 'nullable|string|max:1000',
        ]);

        try {
            $payment = $this->cashService->registerAdvance($estimate, $data);
        } catch (\Exception $e) {
            return back()->withErrors(['general' => 'No se pudo registrar el adelanto: ' . $e->getMessage()]);
        }

        $sn = $payment->invoice?->document_sn;

        return back()->with('success', 'Adelanto registrado' . ($sn ? ' · factura ' . $sn : '') . '.');
    }

    /**
     * Registra un egreso de caja.
     */
    public function expense(Request $request, CashRegister $register): RedirectResponse
    {
        Gate::authorize('create', CashRegister::class);

        $data = $request->validate([
            'amount' => 'required|numeric|min:0.01',
            'description' => 'required|string|max:255',
            'payment_method_id' => 'nullable|exists:payment_methods,id',
            'bank_id' => 'nullable|exists:banks,id',
            'reference' => 'nullable|string|max:100',
            'movement_date' => 'nullable|date',
        ]);

        $this->cashService->registerExpense($register, $data);

        return back()->with('success', 'Egreso registrado.');
    }
}
