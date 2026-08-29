<?php

namespace App\Http\Controllers;

use App\Http\Requests\FormTemplateRequest;
use App\Models\Establishment;
use App\Models\FormTemplate;
use App\Models\FormTemplateItem;
use App\Services\FormTemplateService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;

class FormTemplateController extends Controller
{
    protected FormTemplateService $service;

    public function __construct(FormTemplateService $service)
    {
        $this->service = $service;
    }

    public function index(): View
    {
        Gate::authorize('viewAny', FormTemplate::class);

        return view('form-templates.index', [
            'establishments' => Establishment::query()->orderBy('name')->get(['id', 'name']),
        ]);
    }

    public function create(): View
    {
        Gate::authorize('create', FormTemplate::class);

        return view('form-templates.create', [
            'types' => FormTemplate::TYPES,
            'establishments' => Establishment::query()->orderBy('name')->get(['id', 'name']),
        ]);
    }

    public function store(FormTemplateRequest $request)
    {
        Gate::authorize('create', FormTemplate::class);

        $template = $this->service->create($request->validated());

        return redirect()->route('form-templates.edit', $template)
            ->with('success', 'Plantilla creada. Ahora agrega las secciones y preguntas.');
    }

    public function edit(FormTemplate $formTemplate): View
    {
        Gate::authorize('update', $formTemplate);

        $formTemplate->load('sections.items');

        return view('form-templates.edit', [
            'template' => $formTemplate,
            'establishments' => Establishment::query()->orderBy('name')->get(['id', 'name']),
            'itemTypes' => FormTemplateItem::TYPE_OPTIONS,
        ]);
    }

    public function update(FormTemplateRequest $request, FormTemplate $formTemplate)
    {
        Gate::authorize('update', $formTemplate);

        try {
            $this->service->update($formTemplate, $request->validated());
        } catch (\RuntimeException $e) {
            return back()->withErrors(['structure' => $e->getMessage()]);
        }

        return back()->with('success', 'Plantilla actualizada correctamente.');
    }

    public function destroy(FormTemplate $formTemplate)
    {
        Gate::authorize('delete', $formTemplate);

        $this->service->delete($formTemplate);

        return redirect()->route('form-templates.index')
            ->with('success', 'Plantilla eliminada correctamente.');
    }

    /**
     * Duplica una plantilla (secciones e ítems) hacia el mismo u otro taller.
     */
    public function duplicate(Request $request, FormTemplate $formTemplate)
    {
        Gate::authorize('create', FormTemplate::class);

        $validated = $request->validate([
            'name' => ['nullable', 'string', 'max:150'],
            'establishment_id' => ['nullable', 'integer', 'exists:establishments,id'],
        ]);

        $clone = $this->service->duplicate($formTemplate, $validated);

        return redirect()->route('form-templates.edit', $clone)
            ->with('success', 'Plantilla duplicada correctamente.');
    }

    public function search(Request $request): JsonResponse
    {
        Gate::authorize('viewAny', FormTemplate::class);

        $query = FormTemplate::query()
            ->withCount(['sections as sections_count', 'items as items_count'])
            ->with('establishment')
            ->when($request->filled('q'), function ($q) use ($request) {
                $term = $request->query('q');
                $q->where('name', 'like', "%{$term}%")
                    ->orWhere('type', 'like', "%{$term}%");
            })
            ->orderBy('type')
            ->orderByDesc('id');

        $data = $query->limit($request->integer('limit', 100))->get()->map(fn (FormTemplate $t) => [
            'id' => $t->id,
            'name' => $t->name,
            'type' => $t->type,
            'type_label' => $t->type_label,
            'establishment' => $t->establishment?->name ?? 'Global (todos los talleres)',
            'establishment_id' => $t->establishment_id,
            'is_active' => $t->is_active,
            'sections_count' => (int) $t->sections_count,
            'items_count' => (int) $t->items_count,
        ]);

        return response()->json($data);
    }
}
