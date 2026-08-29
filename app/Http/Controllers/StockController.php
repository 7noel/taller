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
use Illuminate\Support\Facades\DB;
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

    /**
     * Kardex / historial de movimientos.
     */
    public function movements(): View
    {
        Gate::authorize('viewAny', WarehouseStock::class);

        $warehouses = Warehouse::orderBy('name')->get();

        return view('stock.movements', compact('warehouses'));
    }

    public function movementsJson(Request $request): JsonResponse
    {
        Gate::authorize('viewAny', WarehouseStock::class);

        $partId = $request->integer('part_id') ?: null;
        $warehouseId = $request->integer('warehouse_id') ?: null;
        $type = $request->query('type');
        $from = $request->query('from');
        $to = $request->query('to');

        // Modo kardex: repuesto + almacén definidos → saldo inicial / movimientos / saldo final.
        if ($partId && $warehouseId) {
            $kardex = $this->stockService->getKardex($partId, $warehouseId, $from, $to);
            $balance = $kardex['opening']['quantity'];
            $rows = [];

            foreach ($kardex['movements'] as $m) {
                $delta = in_array($m->type, ['entry', 'adjustment']) && $m->quantity > 0 ? $m->quantity : -abs($m->quantity);
                $balance += $delta;
                $rows[] = $this->movementRow($m, round($balance, 2));
            }

            return response()->json([
                'kardex' => true,
                'opening' => $kardex['opening'],
                'closing' => $kardex['closing'],
                'movements' => $rows,
            ]);
        }

        // Modo historial: listado con filtros y saldo actual por repuesto+almacén.
        $movements = StockMovement::query()
            ->with(['part', 'warehouse', 'movementReason', 'inventoryGuide', 'purchaseOrder', 'workOrder'])
            ->when($partId, fn ($q) => $q->where('part_id', $partId))
            ->when($warehouseId, fn ($q) => $q->where('warehouse_id', $warehouseId))
            ->when($type, fn ($q) => $q->where('type', $type))
            ->when($from, fn ($q) => $q->where('created_at', '>=', $from.' 00:00:00'))
            ->when($to, fn ($q) => $q->where('created_at', '<=', $to.' 23:59:59'))
            ->when($request->filled('q'), function ($q) use ($request) {
                $term = $request->query('q');
                $q->whereHas('part', fn ($p) => $p->where('name', 'like', "%{$term}%")->orWhere('sku', 'like', "%{$term}%"));
            })
            ->orderBy('created_at')
            ->orderBy('id')
            ->limit(500)
            ->get();

        $currentBalances = WarehouseStock::whereIn('part_id', $movements->pluck('part_id')->unique()->all())
            ->get()
            ->keyBy(fn ($s) => $s->part_id.'-'.$s->warehouse_id);

        return response()->json([
            'kardex' => false,
            'movements' => $movements->map(function (StockMovement $m) use ($currentBalances) {
                $row = $this->movementRow($m);
                $row['current_balance'] = $currentBalances[$m->part_id.'-'.$m->warehouse_id]?->quantity ?? 0;

                return $row;
            })->values(),
        ]);
    }

    protected function movementRow(StockMovement $m, ?float $balance = null): array
    {
        $delta = in_array($m->type, ['entry', 'adjustment']) && $m->quantity > 0 ? $m->quantity : -abs($m->quantity);

        return [
            'id' => $m->id,
            'date' => optional($m->created_at)->format('d/m/Y H:i'),
            'type' => $m->type,
            'type_label' => $m->type_label,
            'part' => $m->part?->name,
            'sku' => $m->part?->sku,
            'warehouse' => $m->warehouse?->name,
            'quantity' => $m->quantity,
            'signed_quantity' => $delta,
            'unit_cost' => $m->unit_cost_pen,
            'total_cost' => $m->total_cost_pen,
            'reason' => $m->movementReason?->name,
            'reason_code' => $m->movement_reason_code,
            'document_sn' => $m->document_sn,
            'reference' => $m->reference,
            'notes' => $m->notes,
            'balance' => $balance,
        ];
    }

    /**
     * Repuestos con stock <= stock mínimo (alertas).
     */
    public function alerts(Request $request): JsonResponse
    {
        Gate::authorize('viewAny', WarehouseStock::class);

        $warehouseId = $request->integer('warehouse_id') ?: null;

        $parts = Part::query()
            ->with(['category', 'stocks.warehouse'])
            ->withSum(['stocks' => fn ($q) => $q->when($warehouseId, fn ($w) => $w->where('warehouse_id', $warehouseId))], 'quantity')
            ->where('is_active', true)
            ->having('stocks_sum_quantity', '<=', DB::raw('parts.min_stock'))
            ->orderByRaw('stocks_sum_quantity - parts.min_stock')
            ->limit(50)
            ->get();

        return response()->json($parts->map(fn (Part $p) => [
            'id' => $p->id,
            'name' => $p->name,
            'sku' => $p->sku,
            'min_stock' => $p->min_stock,
            'stock' => round((float) $p->stocks_sum_quantity, 2),
            'missing' => max(0, $p->min_stock - (float) $p->stocks_sum_quantity),
            'uom' => $p->uom,
            'warehouses' => $p->stocks->map(fn ($s) => [
                'warehouse' => $s->warehouse?->name,
                'quantity' => $s->quantity,
            ])->values(),
        ]));
    }
}