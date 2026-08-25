<?php

namespace App\Http\Controllers;

use App\Http\Requests\CatalogRequest;
use App\Models\PartBrand;
use App\Models\PartCategory;
use App\Models\ServiceCategory;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;

class CatalogController extends Controller
{
    protected function catalogs(): array
    {
        return [
            'service-categories' => ['model' => ServiceCategory::class, 'label' => 'Categorías de Servicio'],
            'part-categories' => ['model' => PartCategory::class, 'label' => 'Categorías de Repuesto'],
            'part-brands' => ['model' => PartBrand::class, 'label' => 'Marcas de Repuesto'],
        ];
    }

    /**
     * Clave del catálogo según la ruta actual (ej. "part-brands").
     */
    protected function key(): string
    {
        $name = str_replace('api.', '', request()->route()?->getName() ?? '');

        return (string) str($name)->beforeLast('.');
    }

    protected function modelClass(): string
    {
        return $this->catalogs()[$this->key()]['model'];
    }

    protected function model()
    {
        return new ($this->modelClass());
    }

    protected function label(): string
    {
        return $this->catalogs()[$this->key()]['label'];
    }

    /**
     * Devuelve el id del registro a partir del primer parámetro de ruta
     * (funciona con {part_brand}, {part_category}, {service_category}).
     */
    protected function resolveId(): int
    {
        return (int) collect(request()->route()?->parameters() ?? [])->first();
    }

    public function index(): View
    {
        Gate::authorize('viewAny', $this->model());

        return view('catalogs.index', [
            'title' => $this->label(),
            'routePrefix' => $this->key(),
            'searchUrl' => route('api.' . $this->key() . '.search') . '?active=all',
        ]);
    }

    public function create(): View
    {
        Gate::authorize('create', $this->model());

        return view('catalogs.create', [
            'title' => $this->label(),
            'routePrefix' => $this->key(),
        ]);
    }

    public function store(CatalogRequest $request)
    {
        Gate::authorize('create', $this->model());

        $this->model()::create($request->validated());

        return redirect()->route($this->key() . '.index')
            ->with('success', 'Registro creado correctamente.');
    }

    public function show()
    {
        return redirect()->route($this->key() . '.edit', $this->resolveId());
    }

    public function edit(): View
    {
        $item = $this->model()::findOrFail($this->resolveId());
        Gate::authorize('update', $item);

        return view('catalogs.edit', [
            'title' => $this->label(),
            'item' => $item,
            'routePrefix' => $this->key(),
        ]);
    }

    public function update(CatalogRequest $request)
    {
        $item = $this->model()::findOrFail($this->resolveId());
        Gate::authorize('update', $item);

        $item->update($request->validated());

        return redirect()->route($this->key() . '.index')
            ->with('success', 'Registro actualizado correctamente.');
    }

    public function destroy()
    {
        $item = $this->model()::findOrFail($this->resolveId());
        Gate::authorize('delete', $item);

        $item->delete();

        return redirect()->route($this->key() . '.index')
            ->with('success', 'Registro eliminado correctamente.');
    }

    public function search(Request $request): JsonResponse
    {
        Gate::authorize('viewAny', $this->model());

        $showAll = $request->query('active') === 'all';

        $items = $this->model()::query()
            ->when($request->filled('q'), fn ($q) => $q->where('name', 'like', '%' . $request->query('q') . '%'))
            ->when(! $showAll, fn ($q) => $q->where('is_active', true))
            ->orderBy('name')
            ->limit($request->integer('limit', 100))
            ->get(['id', 'name', 'is_active']);

        return response()->json($items);
    }

    /**
     * Alta rápida desde un autocompletado (retorna JSON con id y name).
     */
    public function quickStore(Request $request): JsonResponse
    {
        Gate::authorize('create', $this->model());

        $request->validate(['name' => ['required', 'string', 'max:100']]);

        $item = $this->model()::firstOrCreate(
            ['name' => $request->string('name')],
            ['is_active' => true],
        );

        return response()->json(['id' => $item->id, 'name' => $item->name], 201);
    }
}