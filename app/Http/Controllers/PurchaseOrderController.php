<?php

namespace App\Http\Controllers;

use App\Http\Requests\PurchaseOrderRequest;
use App\Models\PurchaseOrder;
use App\Models\Warehouse;
use App\Services\PurchaseOrderService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;
use RuntimeException;

class PurchaseOrderController extends Controller
{
    protected PurchaseOrderService $service;

    public function __construct(PurchaseOrderService $service)
    {
        $this->service = $service;
    }

    public function index(): View
    {
        Gate::authorize('viewAny', PurchaseOrder::class);

        return view('purchase-orders.index');
    }

    public function create(): View
    {
        Gate::authorize('create', PurchaseOrder::class);

        $warehouses = Warehouse::orderBy('name')->get();

        return view('purchase-orders.create', compact('warehouses'));
    }

    public function store(PurchaseOrderRequest $request)
    {
        Gate::authorize('create', PurchaseOrder::class);

        $this->service->create($request->validated());

        return redirect()->route('purchase-orders.index')
            ->with('success', 'Orden de compra creada correctamente.');
    }

    public function show(PurchaseOrder $purchaseOrder): View
    {
        Gate::authorize('view', $purchaseOrder);

        $purchaseOrder->load(['provider', 'warehouse', 'items.part', 'documentSeries', 'inventoryGuides.movementReason']);
        $warehouses = Warehouse::orderBy('name')->get();

        return view('purchase-orders.show', compact('purchaseOrder', 'warehouses'));
    }

    public function edit(PurchaseOrder $purchaseOrder): View
    {
        Gate::authorize('update', $purchaseOrder);

        $purchaseOrder->load(['provider', 'warehouse', 'items.part']);
        $warehouses = Warehouse::orderBy('name')->get();

        return view('purchase-orders.edit', compact('purchaseOrder', 'warehouses'));
    }

    public function update(PurchaseOrderRequest $request, PurchaseOrder $purchaseOrder)
    {
        Gate::authorize('update', $purchaseOrder);

        try {
            $this->service->update($purchaseOrder, $request->validated());
        } catch (RuntimeException $e) {
            return back()->withErrors(['status' => $e->getMessage()])->withInput();
        }

        return redirect()->route('purchase-orders.show', $purchaseOrder)
            ->with('success', 'Orden de compra actualizada correctamente.');
    }

    public function destroy(PurchaseOrder $purchaseOrder)
    {
        Gate::authorize('delete', $purchaseOrder);

        try {
            $this->service->delete($purchaseOrder);
        } catch (RuntimeException $e) {
            return back()->withErrors(['status' => $e->getMessage()]);
        }

        return redirect()->route('purchase-orders.index')
            ->with('success', 'Orden de compra eliminada correctamente.');
    }

    /**
     * Recepción de mercadería: genera la NIA1 (motivo 02) + entradas de stock.
     */
    public function receive(Request $request, PurchaseOrder $purchaseOrder)
    {
        Gate::authorize('receive', $purchaseOrder);

        $data = $request->validate([
            'warehouse_id' => ['required', 'exists:warehouses,id'],
            'provider_invoice' => ['nullable', 'string', 'max:30'],
            'provider_guide' => ['nullable', 'string', 'max:30'],
            'received_at' => ['nullable', 'date'],
        ]);

        try {
            $this->service->receive($purchaseOrder, $data);
        } catch (RuntimeException $e) {
            return back()->withErrors(['status' => $e->getMessage()]);
        }

        return redirect()->route('purchase-orders.show', $purchaseOrder)
            ->with('success', 'Recepción registrada: el stock ingresó al almacén (NIA1, motivo 02).');
    }

    public function cancel(PurchaseOrder $purchaseOrder)
    {
        Gate::authorize('update', $purchaseOrder);

        try {
            $this->service->cancel($purchaseOrder);
        } catch (RuntimeException $e) {
            return back()->withErrors(['status' => $e->getMessage()]);
        }

        return redirect()->route('purchase-orders.show', $purchaseOrder)
            ->with('success', 'Orden de compra anulada correctamente.');
    }

    public function search(Request $request): JsonResponse
    {
        Gate::authorize('viewAny', PurchaseOrder::class);

        $query = PurchaseOrder::query()
            ->with(['provider', 'warehouse'])
            ->when($request->filled('q'), function ($q) use ($request) {
                $term = $request->query('q');
                $q->where('document_sn', 'like', "%{$term}%")
                    ->orWhereHas('provider', fn ($p) => $p->where('name', 'like', "%{$term}%"));
            })
            ->when($request->filled('status'), fn ($q) => $q->where('status', $request->query('status')))
            ->orderByDesc('id')
            ->limit($request->integer('limit', 100));

        return response()->json($query->get()->map(fn (PurchaseOrder $po) => [
            'id' => $po->id,
            'document_sn' => $po->document_sn,
            'provider' => $po->provider?->display_name,
            'warehouse' => $po->warehouse?->name,
            'order_date' => optional($po->order_date)->format('d/m/Y'),
            'expected_delivery' => optional($po->expected_delivery)->format('d/m/Y'),
            'status' => $po->status,
            'status_label' => $po->status_label,
            'total' => $po->total,
            'currency' => $po->currency,
        ]));
    }
}
