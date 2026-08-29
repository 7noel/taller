<?php

namespace App\Http\Controllers;

use App\Http\Requests\PartOrderRequest;
use App\Models\PartOrder;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;

class PartOrderController extends Controller
{
    public function index(): View
    {
        Gate::authorize('viewAny', PartOrder::class);

        return view('part-orders.index');
    }

    public function store(PartOrderRequest $request)
    {
        Gate::authorize('create', PartOrder::class);

        $data = $request->validated();
        $data['status'] = 'pending';
        $data['created_by'] = auth()->id();
        $data['updated_by'] = auth()->id();

        PartOrder::create($data);

        return redirect()->route('part-orders.index')
            ->with('success', 'Pedido de repuesto registrado correctamente.');
    }

    public function updateStatus(Request $request, PartOrder $partOrder)
    {
        Gate::authorize('update', $partOrder);

        $request->validate([
            'status' => ['required', 'in:pending,ordered,in_transit,received'],
        ]);

        $data = ['status' => $request->status, 'updated_by' => auth()->id()];

        if ($request->status === 'ordered') {
            $data['ordered_at'] = $data['ordered_at'] ?? now()->toDateString();
        }
        if ($request->status === 'received') {
            $data['delivered_at'] = now()->toDateString();
        }

        $partOrder->update($data);

        return redirect()->route('part-orders.index')
            ->with('success', 'Estado del pedido actualizado correctamente.');
    }

    public function destroy(PartOrder $partOrder)
    {
        Gate::authorize('delete', $partOrder);

        $partOrder->delete();

        return redirect()->route('part-orders.index')
            ->with('success', 'Pedido eliminado correctamente.');
    }

    public function search(Request $request): JsonResponse
    {
        Gate::authorize('viewAny', PartOrder::class);

        $query = PartOrder::query()
            ->with(['part', 'estimate', 'provider'])
            ->when($request->filled('status'), fn ($q) => $q->where('status', $request->query('status')))
            ->when($request->filled('q'), function ($q) use ($request) {
                $term = $request->query('q');
                $q->whereHas('part', fn ($p) => $p->where('name', 'like', "%{$term}%")->orWhere('sku', 'like', "%{$term}%"));
            })
            ->orderByDesc('id')
            ->limit($request->integer('limit', 100));

        return response()->json($query->get()->map(fn (PartOrder $po) => [
            'id' => $po->id,
            'part' => $po->part?->name,
            'sku' => $po->part?->sku,
            'quantity' => $po->quantity,
            'uom' => $po->part?->uom,
            'status' => $po->status,
            'status_label' => $po->status_label,
            'estimate_sn' => $po->estimate?->document_sn,
            'provider' => $po->provider?->display_name,
            'ordered_at' => optional($po->ordered_at)->format('d/m/Y'),
            'expected_delivery' => optional($po->expected_delivery)->format('d/m/Y'),
            'delivered_at' => optional($po->delivered_at)->format('d/m/Y'),
            'tracking_number' => $po->tracking_number,
            'notes' => $po->notes,
        ]));
    }
}
