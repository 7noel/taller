<?php

namespace App\Http\Controllers;

use App\Http\Requests\CheckInChecklistItemRequest;
use App\Models\CheckInChecklistItem;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;

class CheckInChecklistItemController extends Controller
{
    public function index(): View
    {
        Gate::authorize('viewAny', CheckInChecklistItem::class);

        return view('checklist-items.index');
    }

    public function create(): View
    {
        Gate::authorize('create', CheckInChecklistItem::class);

        return view('checklist-items.create');
    }

    public function store(CheckInChecklistItemRequest $request)
    {
        Gate::authorize('create', CheckInChecklistItem::class);

        CheckInChecklistItem::create($request->validated());

        return redirect()->route('checklist-items.index')
            ->with('success', 'Ítem del checklist creado correctamente.');
    }

    public function edit(CheckInChecklistItem $checkInChecklistItem): View
    {
        Gate::authorize('update', $checkInChecklistItem);

        return view('checklist-items.edit', compact('checkInChecklistItem'));
    }

    public function update(CheckInChecklistItemRequest $request, CheckInChecklistItem $checkInChecklistItem)
    {
        Gate::authorize('update', $checkInChecklistItem);

        $checkInChecklistItem->update($request->validated());

        return redirect()->route('checklist-items.index')
            ->with('success', 'Ítem del checklist actualizado correctamente.');
    }

    public function destroy(CheckInChecklistItem $checkInChecklistItem)
    {
        Gate::authorize('delete', $checkInChecklistItem);

        $checkInChecklistItem->delete();

        return redirect()->route('checklist-items.index')
            ->with('success', 'Ítem del checklist eliminado correctamente.');
    }

    public function search(Request $request): JsonResponse
    {
        Gate::authorize('viewAny', CheckInChecklistItem::class);

        $query = CheckInChecklistItem::query()
            ->when($request->filled('q'), function ($q) use ($request) {
                $term = $request->query('q');
                $q->where('name', 'like', "%{$term}%")
                    ->orWhere('category', 'like', "%{$term}%");
            })
            ->orderBy('category')
            ->orderBy('order')
            ->orderBy('name');

        $data = $query->limit($request->integer('limit', 200))->get()->map(fn (CheckInChecklistItem $item) => [
            'id' => $item->id,
            'name' => $item->name,
            'category' => $item->category,
            'category_label' => $item->category_label,
            'order' => (int) $item->order,
            'is_active' => $item->is_active,
        ]);

        return response()->json($data);
    }
}
