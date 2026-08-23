<?php

namespace App\Http\Controllers;

use App\Http\Requests\VehicleRequest;
use App\Models\Brand;
use App\Models\Vehicle;
use App\Models\VehicleModel;
use App\Services\BrandService;
use App\Services\VehicleModelService;
use App\Services\VehicleService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\Rule;
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
                'brand_id' => $vehicle->vehicleModel?->brand_id,
                'model' => $vehicle->vehicleModel?->name,
                'model_id' => $vehicle->model_id,
                'year' => $vehicle->year,
                'color' => $vehicle->color,
                'vin' => $vehicle->vin,
                'engine_number' => $vehicle->engine_number,
                'body_type' => $vehicle->body_type,
                'technical_review_date' => $vehicle->technical_review_date?->format('Y-m-d'),
                'owner_name' => $vehicle->relationships->first()?->party?->display_name,
            ]));
    }

    public function quickStore(Request $request): JsonResponse
    {
        Gate::authorize('create', Vehicle::class);

        $request->validate([
            'plate' => ['required', 'string', 'min:3', 'max:7', 'unique:vehicles,plate'],
            'brand_id' => ['required', 'exists:brands,id'],
            'model_id' => ['required', 'exists:models,id'],
            'color' => ['nullable', 'string', 'max:50'],
            'vin' => ['nullable', 'string', 'max:20'],
            'engine_number' => ['nullable', 'string', 'max:30'],
            'year' => ['nullable', 'integer', 'min:1900', 'max:2100'],
            'body_type' => ['nullable', 'string', 'max:50'],
            'technical_review_date' => ['nullable', 'date'],
        ], [
            'plate.required' => 'La placa es obligatoria.',
            'plate.unique' => 'Esa placa ya está registrada.',
            'brand_id.required' => 'Seleccione la marca.',
            'model_id.required' => 'Seleccione el modelo.',
        ]);

        $vehicle = $this->vehicleService->create($request->all());

        return response()->json([
            'id' => $vehicle->id,
            'plate' => $vehicle->plate,
            'brand' => $vehicle->vehicleModel?->brand?->name,
            'model' => $vehicle->vehicleModel?->name,
            'year' => $vehicle->year,
            'color' => $vehicle->color,
            'vin' => $vehicle->vin,
            'engine_number' => $vehicle->engine_number,
            'body_type' => $vehicle->body_type,
            'technical_review_date' => $vehicle->technical_review_date?->format('Y-m-d'),
        ], 201);
    }

    public function quickUpdate(Request $request, Vehicle $vehicle): JsonResponse
    {
        Gate::authorize('update', $vehicle);

        $request->validate([
            'plate' => ['required', 'string', 'min:3', 'max:7', Rule::unique('vehicles', 'plate')->ignore($vehicle->id)],
            'brand_id' => ['required', 'exists:brands,id'],
            'model_id' => ['required', 'exists:models,id'],
            'color' => ['nullable', 'string', 'max:50'],
            'vin' => ['nullable', 'string', 'max:20'],
            'engine_number' => ['nullable', 'string', 'max:30'],
            'year' => ['nullable', 'integer', 'min:1900', 'max:2100'],
            'body_type' => ['nullable', 'string', 'max:50'],
            'technical_review_date' => ['nullable', 'date'],
        ], [
            'plate.required' => 'La placa es obligatoria.',
            'plate.unique' => 'Esa placa ya está registrada.',
            'brand_id.required' => 'Seleccione la marca.',
            'model_id.required' => 'Seleccione el modelo.',
        ]);

        $this->vehicleService->update($vehicle, $request->all());

        return response()->json([
            'id' => $vehicle->fresh()->id,
            'plate' => $vehicle->fresh()->plate,
            'brand' => $vehicle->fresh()->vehicleModel?->brand?->name,
            'brand_id' => $vehicle->fresh()->vehicleModel?->brand_id,
            'model' => $vehicle->fresh()->vehicleModel?->name,
            'model_id' => $vehicle->fresh()->model_id,
            'year' => $vehicle->fresh()->year,
            'color' => $vehicle->fresh()->color,
            'vin' => $vehicle->fresh()->vin,
            'engine_number' => $vehicle->fresh()->engine_number,
            'body_type' => $vehicle->fresh()->body_type,
            'technical_review_date' => $vehicle->fresh()->technical_review_date?->format('Y-m-d'),
        ]);
    }

    public function brands(Request $request): JsonResponse
    {
        return response()->json(Brand::orderBy('name')->get(['id', 'name']));
    }

    public function models(Request $request): JsonResponse
    {
        return response()->json(
            VehicleModel::where('brand_id', $request->integer('brand_id'))
                ->orderBy('name')
                ->get(['id', 'name'])
        );
    }

    public function findOrCreateBrand(Request $request, BrandService $brandService): JsonResponse
    {
        Gate::authorize('create', Brand::class);

        $request->validate(['name' => ['required', 'string', 'max:100']]);

        $brand = $brandService->findOrCreateBrand($request->string('name'));

        return response()->json(['id' => $brand->id, 'name' => $brand->name]);
    }

    public function findOrCreateModel(Request $request, VehicleModelService $modelService): JsonResponse
    {
        Gate::authorize('create', VehicleModel::class);

        $request->validate([
            'brand_id' => ['required', 'exists:brands,id'],
            'name' => ['required', 'string', 'max:100'],
        ]);

        $model = $modelService->findOrCreateModel($request->integer('brand_id'), $request->string('name'));

        return response()->json(['id' => $model->id, 'name' => $model->name]);
    }
}
