<?php

namespace App\Http\Controllers;

use App\Http\Requests\InventoryGuideRequest;
use App\Models\InventoryGuide;
use App\Models\InventoryMovementReason;
use App\Models\Warehouse;
use App\Services\InventoryGuideService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;
use InvalidArgumentException;

class InventoryGuideController extends Controller
{
    protected InventoryGuideService $service;

    public function __construct(InventoryGuideService $service)
    {
        $this->service = $service;
    }

    public function index(): View
    {
        Gate::authorize('viewAny', InventoryGuide::class);

        return view('inventory-guides.index');
    }

    public function create(): View
    {
        Gate::authorize('create', InventoryGuide::class);

        $warehouses = Warehouse::orderBy('name')->get();
        $inputReasons = InventoryMovementReason::where('type', 'input')->orderBy('code')->get();
        $outputReasons = InventoryMovementReason::where('type', 'output')->orderBy('code')->get();

        return view('inventory-guides.create', compact('warehouses', 'inputReasons', 'outputReasons'));
    }

    public function store(InventoryGuideRequest $request)
    {
        Gate::authorize('create', InventoryGuide::class);

        $data = $request->validated();

        try {
            $guide = match ($data['guide_type']) {
                'transfer' => $this->service->createTransfer($data),
                'adjustment' => $this->service->createAdjustment($data),
                'output' => $this->service->createOutput($data),
                default => $this->service->createInput($data),
            };
        } catch (InvalidArgumentException $e) {
            return back()->withErrors(['items' => $e->getMessage()])->withInput();
        }

        return redirect()->route('inventory-guides.show', $guide)
            ->with('success', "Guía {$guide->document_sn} emitida correctamente.");
    }

    public function show(InventoryGuide $inventoryGuide): View
    {
        Gate::authorize('view', $inventoryGuide);

        $inventoryGuide->load([
            'movementReason', 'originWarehouse', 'destinationWarehouse',
            'provider', 'workOrder', 'purchaseOrder',
            'movements.part', 'movements.warehouse',
        ]);

        return view('inventory-guides.show', compact('inventoryGuide'));
    }

    public function destroy(InventoryGuide $inventoryGuide)
    {
        Gate::authorize('delete', $inventoryGuide);

        $inventoryGuide->update(['status' => InventoryGuide::STATUS_CANCELLED]);
        $inventoryGuide->delete();

        return redirect()->route('inventory-guides.index')
            ->with('success', 'Guía anulada correctamente.');
    }

    public function search(Request $request): JsonResponse
    {
        Gate::authorize('viewAny', InventoryGuide::class);

        $query = InventoryGuide::query()
            ->with(['movementReason', 'originWarehouse', 'destinationWarehouse', 'provider'])
            ->when($request->filled('q'), fn ($q) => $q->where('document_sn', 'like', '%'.$request->query('q').'%'))
            ->when($request->filled('document_type_code'), fn ($q) => $q->where('document_type_code', $request->query('document_type_code')))
            ->when($request->filled('from'), fn ($q) => $q->whereDate('movement_date', '>=', $request->query('from')))
            ->when($request->filled('to'), fn ($q) => $q->whereDate('movement_date', '<=', $request->query('to')))
            ->orderByDesc('id')
            ->limit($request->integer('limit', 100));

        return response()->json($query->get()->map(fn (InventoryGuide $g) => [
            'id' => $g->id,
            'document_sn' => $g->document_sn,
            'type_code' => $g->document_type_code,
            'type_label' => $g->type_label,
            'reason' => $g->movementReason?->name,
            'reason_code' => $g->movement_reason_code,
            'ref' => $g->workOrder?->document_sn ?? $g->purchaseOrder?->document_sn ?? null,
            'origin' => $g->originWarehouse?->name,
            'destination' => $g->destinationWarehouse?->name,
            'provider' => $g->provider?->display_name,
            'work_order_sn' => $g->workOrder?->document_sn,
            'purchase_order_sn' => $g->purchaseOrder?->document_sn,
            'movement_date' => optional($g->movement_date)->format('d/m/Y'),
            'status' => $g->status,
            'notes' => $g->notes,
        ]));
    }
}
