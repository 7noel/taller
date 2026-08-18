<?php

namespace App\Http\Controllers;

use App\Http\Requests\VehicleRequest;
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

    /**
     * Display a listing of the resource.
     */
    public function index(): View
    {
        Gate::authorize('viewAny', Vehicle::class);

        return view('vehicles.index');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        Gate::authorize('create', Vehicle::class);

        return view('vehicles.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(VehicleRequest $request)
    {
        Gate::authorize('create', Vehicle::class);

        $this->vehicleService->create($request->validated());

        return redirect()->route('vehicles.index')
            ->with('success', 'Vehículo creado correctamente.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Vehicle $vehicle): View
    {
        Gate::authorize('view', $vehicle);

        $vehicle->load(['relationships.party.ubigeo']);

        return view('vehicles.show', compact('vehicle'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Vehicle $vehicle): View
    {
        Gate::authorize('update', $vehicle);

        $vehicle->load('relationships.party');

        return view('vehicles.edit', compact('vehicle'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(VehicleRequest $request, Vehicle $vehicle)
    {
        Gate::authorize('update', $vehicle);

        $this->vehicleService->update($vehicle, $request->validated());

        return redirect()->route('vehicles.index')
            ->with('success', 'Vehículo actualizado correctamente.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Vehicle $vehicle)
    {
        Gate::authorize('delete', $vehicle);

        $this->vehicleService->delete($vehicle);

        return redirect()->route('vehicles.index')
            ->with('success', 'Vehículo eliminado correctamente.');
    }

    /**
     * AJAX search for vehicles (Tom Select / Tabulator).
     */
    public function search(Request $request): JsonResponse
    {
        Gate::authorize('viewAny', Vehicle::class);

        $query = Vehicle::query()
            ->with(['relationships' => fn ($q) => $q->where('role', 'owner')->with('party')])
            ->when($request->filled('q'), function ($q) use ($request) {
                $term = $request->query('q');
                $q->where('plate', 'like', "%{$term}%")
                    ->orWhere('brand', 'like', "%{$term}%")
                    ->orWhere('model', 'like', "%{$term}%")
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
                'brand' => $vehicle->brand,
                'model' => $vehicle->model,
                'year' => $vehicle->year,
                'owner_name' => $vehicle->relationships->first()?->party?->display_name,
            ]));
    }
}