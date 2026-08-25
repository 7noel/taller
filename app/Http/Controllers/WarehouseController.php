<?php

namespace App\Http\Controllers;

use App\Http\Requests\WarehouseRequest;
use App\Models\Establishment;
use App\Models\Warehouse;
use App\Services\WarehouseService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;

class WarehouseController extends Controller
{
    protected WarehouseService $service;

    public function __construct(WarehouseService $service)
    {
        $this->service = $service;
    }

    public function index(): View
    {
        Gate::authorize('viewAny', Warehouse::class);

        return view('warehouses.index');
    }

    public function create(): View
    {
        Gate::authorize('create', Warehouse::class);

        $establishments = Establishment::orderBy('name')->get();

        return view('warehouses.create', compact('establishments'));
    }

    public function store(WarehouseRequest $request)
    {
        Gate::authorize('create', Warehouse::class);

        $this->service->create($request->validated());

        return redirect()->route('warehouses.index')
            ->with('success', 'Almacén creado correctamente.');
    }

    public function edit(Warehouse $warehouse): View
    {
        Gate::authorize('update', $warehouse);

        $establishments = Establishment::orderBy('name')->get();

        return view('warehouses.edit', compact('warehouse', 'establishments'));
    }

    public function update(WarehouseRequest $request, Warehouse $warehouse)
    {
        Gate::authorize('update', $warehouse);

        $this->service->update($warehouse, $request->validated());

        return redirect()->route('warehouses.index')
            ->with('success', 'Almacén actualizado correctamente.');
    }

    public function destroy(Warehouse $warehouse)
    {
        Gate::authorize('delete', $warehouse);

        $this->service->delete($warehouse);

        return redirect()->route('warehouses.index')
            ->with('success', 'Almacén eliminado correctamente.');
    }

    public function search(Request $request): JsonResponse
    {
        Gate::authorize('viewAny', Warehouse::class);

        $query = Warehouse::query()
            ->with('establishment')
            ->when($request->filled('q'), function ($q) use ($request) {
                $term = $request->query('q');
                $q->where('name', 'like', "%{$term}%")
                    ->orWhere('code', 'like', "%{$term}%");
            })
            ->when($request->filled('id'), fn ($q) => $q->whereKey($request->query('id')))
            ->orderBy('name')
            ->limit($request->integer('limit', 100));

        return response()->json($query->get()->map(fn (Warehouse $w) => [
            'id' => $w->id,
            'name' => $w->name,
            'code' => $w->code,
            'establishment' => $w->establishment?->name,
            'location' => $w->location,
            'is_active' => $w->is_active,
        ]));
    }
}