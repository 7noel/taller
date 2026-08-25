<?php

namespace App\Http\Controllers;

use App\Http\Requests\RepairServiceRequest;
use App\Models\RepairService;
use App\Services\RepairServiceService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;

class RepairServiceController extends Controller
{
    protected RepairServiceService $service;

    public function __construct(RepairServiceService $service)
    {
        $this->service = $service;
    }

    public function index(): View
    {
        Gate::authorize('viewAny', RepairService::class);

        return view('repair-services.index');
    }

    public function create(): View
    {
        Gate::authorize('create', RepairService::class);

        return view('repair-services.create');
    }

    public function store(RepairServiceRequest $request)
    {
        Gate::authorize('create', RepairService::class);

        $this->service->create($request->validated());

        return redirect()->route('repair-services.index')
            ->with('success', 'Servicio creado correctamente.');
    }

    public function edit(RepairService $repairService): View
    {
        Gate::authorize('update', $repairService);

        return view('repair-services.edit', compact('repairService'));
    }

    public function update(RepairServiceRequest $request, RepairService $repairService)
    {
        Gate::authorize('update', $repairService);

        $this->service->update($repairService, $request->validated());

        return redirect()->route('repair-services.index')
            ->with('success', 'Servicio actualizado correctamente.');
    }

    public function destroy(RepairService $repairService)
    {
        Gate::authorize('delete', $repairService);

        $this->service->delete($repairService);

        return redirect()->route('repair-services.index')
            ->with('success', 'Servicio eliminado correctamente.');
    }

    public function search(Request $request): JsonResponse
    {
        Gate::authorize('viewAny', RepairService::class);

        $query = RepairService::query()
            ->when($request->filled('q'), function ($q) use ($request) {
                $term = $request->query('q');
                $q->where('name', 'like', "%{$term}%")
                    ->orWhereHas('category', fn ($c) => $c->where('name', 'like', "%{$term}%"));
            })
            ->when($request->filled('id'), fn ($q) => $q->whereKey($request->query('id')))
            ->orderBy('name')
            ->limit($request->integer('limit', 20));

        return response()->json($query->with(['provider', 'category'])->get()->map(fn (RepairService $s) => [
            'id' => $s->id,
            'name' => $s->name,
            'category' => $s->category?->name,
            'pricing_type' => $s->pricing_type,
            'sell_price' => $s->sell_price,
            'cost_price' => $s->cost_price,
            'currency' => $s->currency,
            'cost_currency' => $s->cost_currency,
            'provider_name' => $s->provider?->display_name,
            'is_active' => $s->is_active,
        ]));
    }
}