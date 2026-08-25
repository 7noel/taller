<?php

namespace App\Http\Controllers;

use App\Http\Requests\PartRequest;
use App\Models\Part;
use App\Services\PartService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;

class PartController extends Controller
{
    protected PartService $service;

    public function __construct(PartService $service)
    {
        $this->service = $service;
    }

    public function index(): View
    {
        Gate::authorize('viewAny', Part::class);

        return view('parts.index');
    }

    public function create(): View
    {
        Gate::authorize('create', Part::class);

        return view('parts.create');
    }

    public function store(PartRequest $request)
    {
        Gate::authorize('create', Part::class);

        $this->service->create($request->validated());

        return redirect()->route('parts.index')
            ->with('success', 'Repuesto creado correctamente.');
    }

    public function edit(Part $part): View
    {
        Gate::authorize('update', $part);

        return view('parts.edit', compact('part'));
    }

    public function update(PartRequest $request, Part $part)
    {
        Gate::authorize('update', $part);

        $this->service->update($part, $request->validated());

        return redirect()->route('parts.index')
            ->with('success', 'Repuesto actualizado correctamente.');
    }

    public function destroy(Part $part)
    {
        Gate::authorize('delete', $part);

        $this->service->delete($part);

        return redirect()->route('parts.index')
            ->with('success', 'Repuesto eliminado correctamente.');
    }

    public function search(Request $request): JsonResponse
    {
        Gate::authorize('viewAny', Part::class);

        $query = Part::query()
            ->withSum('stocks', 'quantity')
            ->when($request->filled('q'), function ($q) use ($request) {
                $term = $request->query('q');
                $q->where('name', 'like', "%{$term}%")
                    ->orWhere('sku', 'like', "%{$term}%")
                    ->orWhere('barcode', 'like', "%{$term}%")
                    ->orWhereHas('brand', fn ($b) => $b->where('name', 'like', "%{$term}%"))
                    ->orWhereHas('category', fn ($c) => $c->where('name', 'like', "%{$term}%"));
            })
            ->when($request->filled('id'), fn ($q) => $q->whereKey($request->query('id')))
            ->orderBy('name')
            ->limit($request->integer('limit', 100));

        return response()->json($query->with(['brand', 'category'])->get()->map(fn (Part $p) => [
            'id' => $p->id,
            'name' => $p->name,
            'sku' => $p->sku,
            'brand' => $p->brand?->name,
            'category' => $p->category?->name,
            'sell_price' => $p->sell_price,
            'cost_price' => $p->cost_price,
            'currency' => $p->currency,
            'cost_currency' => $p->cost_currency,
            'stock' => (float) $p->stocks_sum_quantity,
            'is_active' => $p->is_active,
        ]));
    }
}