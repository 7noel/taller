<?php

namespace App\Http\Controllers;

use App\Http\Requests\StockMovementRequest;
use App\Models\Part;
use App\Models\StockMovement;
use App\Models\Warehouse;
use App\Models\WarehouseStock;
use App\Services\StockService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;
use InvalidArgumentException;

class StockController extends Controller
{
    protected StockService $stockService;

    public function __construct(StockService $stockService)
    {
        $this->stockService = $stockService;
    }

    public function index(): View
    {
        Gate::authorize('viewAny', WarehouseStock::class);

        return view('stock.index');
    }

    public function store(StockMovementRequest $request)
    {
        Gate::authorize('create', WarehouseStock::class);

        $validated = $request->validated();

        try {
            $this->stockService->registerMovement(
                partId: (int) $validated['part_id'],
                warehouseId: (int) $validated['warehouse_id'],
                type: $validated['type'],
                quantity: (float) $validated['quantity'],
                unitCost: (float) $validated['unit_cost'],
                currency: $validated['currency'],
                exchangeRate: isset($validated['exchange_rate']) ? (float) $validated['exchange_rate'] : null,
                documentType: $validated['document_type'] ?? null,
                documentId: isset($validated['document_id']) ? (int) $validated['document_id'] : null,
                reference: $validated['reference'] ?? null,
            );
        } catch (InvalidArgumentException $e) {
            return back()->withErrors(['quantity' => $e->getMessage()])->withInput();
        }

        return redirect()->route('stock.index')
            ->with('success', 'Movimiento registrado correctamente.');
    }

    public function search(Request $request): JsonResponse
    {
        Gate::authorize('viewAny', WarehouseStock::class);

        $query = WarehouseStock::query()
            ->with(['part', 'warehouse'])
            ->when($request->filled('q'), function ($q) use ($request) {
                $term = $request->query('q');
                $q->whereHas('part', fn ($p) => $p->where('name', 'like', "%{$term}%")->orWhere('sku', 'like', "%{$term}%"))
                    ->orWhereHas('warehouse', fn ($w) => $w->where('name', 'like', "%{$term}%"));
            })
            ->when($request->filled('warehouse_id'), fn ($q) => $q->where('warehouse_id', $request->query('warehouse_id')))
            ->limit($request->integer('limit', 200));

        return response()->json($query->get()->map(fn (WarehouseStock $s) => [
            'id' => $s->id,
            'part' => $s->part?->name,
            'sku' => $s->part?->sku,
            'warehouse' => $s->warehouse?->name,
            'quantity' => $s->quantity,
            'average_cost' => $s->average_cost,
            'total_value' => round($s->quantity * $s->average_cost, 2),
        ]));
    }
}