<?php

namespace App\Http\Controllers;

use App\Models\Bank;
use App\Models\PaymentMethod;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;

/**
 * Catálogos del módulo de caja: métodos de pago y bancos.
 */
class CashCatalogController extends Controller
{
    public function paymentMethods(): View
    {
        Gate::authorize('viewAny', PaymentMethod::class);

        return view('cash.catalogs', [
            'type' => 'payment_methods',
            'title' => 'Métodos de Pago',
            'items' => PaymentMethod::query()->orderBy('name')->get(),
        ]);
    }

    public function storePaymentMethod(Request $request): RedirectResponse
    {
        Gate::authorize('create', PaymentMethod::class);

        $data = $request->validate([
            'code' => 'required|string|max:20|unique:payment_methods,code',
            'name' => 'required|string|max:100',
        ]);

        PaymentMethod::create($data + ['is_active' => true]);

        return back()->with('success', 'Método de pago creado.');
    }

    public function destroyPaymentMethod(PaymentMethod $paymentMethod): RedirectResponse
    {
        Gate::authorize('delete', PaymentMethod::class);

        $paymentMethod->delete();

        return back()->with('success', 'Método de pago eliminado.');
    }

    public function banks(): View
    {
        Gate::authorize('viewAny', Bank::class);

        return view('cash.catalogs', [
            'type' => 'banks',
            'title' => 'Bancos',
            'items' => Bank::query()->orderBy('name')->get(),
        ]);
    }

    public function storeBank(Request $request): RedirectResponse
    {
        Gate::authorize('create', Bank::class);

        $data = $request->validate([
            'name' => 'required|string|max:150',
        ]);

        Bank::create($data + ['is_active' => true]);

        return back()->with('success', 'Banco creado.');
    }

    public function destroyBank(Bank $bank): RedirectResponse
    {
        Gate::authorize('delete', Bank::class);

        $bank->delete();

        return back()->with('success', 'Banco eliminado.');
    }
}
