<?php

namespace App\Http\Controllers;

use App\Http\Requests\VehicleRequest;
use App\Models\Brand;
use App\Models\Vehicle;
use App\Services\VehicleService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;

class VehicleController extends Controller
{
    protected VehicleService $vehicleService;

    public function __construct(VehicleService $vehicleService)
    {
        $this->vehicleService = $vehicleService;
    }

    public function index(): View
    {
        Gate::authorize('viewAny', Vehicle::class);

        return view('vehicles.index');
    }

    public function create(): View
    {
        Gate::authorize('create', Vehicle::class);

        $brands = Brand::with('models')->orderBy('name')->get();

        return view('vehicles.create', compact('brands'));
    }

    public function store(VehicleRequest $request)
    {
        Gate::authorize('create', Vehicle::class);

        $this->vehicleService->create($request->validated());

        return redirect()->route('vehicles.index')
            ->with('success', 'Vehículo creado correctamente.');
    }

    public function show(Vehicle $vehicle): View
    {
        Gate::authorize('view', $vehicle);

        $vehicle->load(['vehicleModel.brand', 'relationships.party.ubigeo']);

        return view('vehicles.show', compact('vehicle'));
    }

    public function edit(Vehicle $vehicle): View
    {
        Gate::authorize('update', $vehicle);

        $brands = Brand::with('models')->orderBy('name')->get();
        $vehicle->load('relationships.party');

        return view('vehicles.edit', compact('vehicle', 'brands'));
    }

    public function update(VehicleRequest $request, Vehicle $vehicle)
    {
        Gate::authorize('update', $vehicle);

        $this->vehicleService->update($vehicle, $request->validated());

        return redirect()->route('vehicles.index')
            ->with('success', 'Vehículo actualizado correctamente.');
    }

    public function destroy(Vehicle $vehicle)
    {
        Gate::authorize('delete', $vehicle);

        $this->vehicleService->delete($vehicle);

        return redirect()->route('vehicles.index')
            ->with('success', 'Vehículo eliminado correctamente.');
    }

    public function search(Request $request): JsonResponse
    {
        Gate::authorize('viewAny', Vehicle::class);

        $query = Vehicle::query()
            ->with(['vehicleModel.brand', 'relationships' => fn ($q) => $q->where('role', 'owner')->with('party')])
            ->when($request->filled('q'), function ($q) use ($request) {
                $term = $request->query('q');
                $q->where('plate', 'like', "%{$term}%")
                    ->orWhereHas('vehicleModel.brand', fn ($b) => $b->where('name', 'like', "%{$term}%"))
                    ->orWhereHas('vehicleModel', fn ($m) => $m->where('name', 'like', "%{$term}%"))
                    ->orWhereHas('relationships.party', fn ($p) => $p
                        ->where('business_name', 'like', "%{$term}%")
                        ->orWhere('first_name', 'like', "%{$term}%")
                        ->orWhere('last_name', 'like', "%{$term}%"));
            })
            ->when($request->filled('id'), function ($q) use ($request) {
                $q->whereKey($request->query('id'));
            })
            ->orderBy('plate')
            ->limit($request->integer('limit', 100));

        return response()->json($query->get()
            ->map(fn (Vehicle $vehicle) => [
                'id' => $vehicle->id,
                'plate' => $vehicle->plate,
                'brand' => $vehicle->vehicleModel?->brand?->name,
                'model' => $vehicle->vehicleModel?->name,
                'year' => $vehicle->year,
                'owner_name' => $vehicle->relationships->first()?->party?->display_name,
            ]));
    }

    public function brands(Request $request): JsonResponse
    {
        return response()->json(Brand::orderBy('name')->get(['id', 'name']));
    }

    public function models(Request $request): JsonResponse
    {
        return response()->json(
            \App\Models\VehicleModel::where('brand_id', $request->integer('brand_id'))
                ->orderBy('name')
                ->get(['id', 'name'])
        );
    }
}