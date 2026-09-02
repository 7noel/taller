<?php

namespace App\Http\Controllers;

use App\Http\Requests\BrandRequest;
use App\Models\Brand;
use App\Services\BrandService;
use Illuminate\Database\QueryException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;

class BrandController extends Controller
{
    public function __construct(protected BrandService $brandService)
    {
    }

    public function index(): View
    {
        Gate::authorize('viewAny', Brand::class);

        return view('brands.index', [
            'searchUrl' => route('api.brands.search'),
        ]);
    }

    public function create(): View
    {
        Gate::authorize('create', Brand::class);

        return view('brands.create');
    }

    public function store(BrandRequest $request)
    {
        Gate::authorize('create', Brand::class);

        try {
            $brand = $this->brandService->create($request->validated());
        } catch (QueryException) {
            return back()->withInput()
                ->withErrors(['name' => 'No se pudo guardar. Verifica que el nombre de la marca no esté duplicado.']);
        }

        return redirect()->route('brands.edit', $brand)
            ->with('success', 'Marca creada correctamente. Agrega o edita sus modelos.');
    }

    public function edit(Brand $brand): View
    {
        Gate::authorize('update', $brand);

        return view('brands.edit', [
            'brand' => $brand,
            'models' => $brand->models()->orderBy('name')->get(),
        ]);
    }

    public function update(BrandRequest $request, Brand $brand)
    {
        Gate::authorize('update', $brand);

        try {
            $warnings = $this->brandService->update($brand, $request->validated());
        } catch (QueryException) {
            return back()->withInput()
                ->withErrors(['models' => 'No se pudo guardar: ya existe un modelo con ese nombre en esta marca.']);
        }

        $redirect = redirect()->route('brands.edit', $brand)
            ->with('success', 'Marca actualizada correctamente.');

        if (count($warnings) > 0) {
            $redirect->with('warning', $warnings);
        }

        return $redirect;
    }

    public function destroy(Brand $brand)
    {
        Gate::authorize('delete', $brand);

        if (! $this->brandService->delete($brand)) {
            return back()->with('error', 'No se puede eliminar la marca porque tiene vehículos asociados.');
        }

        return redirect()->route('brands.index')
            ->with('success', 'Marca eliminada correctamente.');
    }

    /**
     * JSON para el listado (Tabulator): marcas con cantidad de modelos y vehículos.
     */
    public function search(Request $request): JsonResponse
    {
        Gate::authorize('viewAny', Brand::class);

        $q = trim((string) $request->query('q', ''));

        $brands = Brand::query()
            ->withCount(['models', 'vehicles'])
            ->when($q !== '', function ($query) use ($q) {
                $query->where('name', 'like', '%'.$q.'%')
                    ->orWhereHas('models', fn ($m) => $m->where('name', 'like', '%'.$q.'%'));
            })
            ->orderBy('name')
            ->limit($request->integer('limit', 100))
            ->get(['id', 'name']);

        return response()->json($brands);
    }
}
