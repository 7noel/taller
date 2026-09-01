<?php

namespace App\Http\Controllers;

use App\Http\Requests\FollowUpRequest;
use App\Models\FollowUp;
use App\Services\FollowUpService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;

class FollowUpController extends Controller
{
    protected FollowUpService $followUpService;

    public function __construct(FollowUpService $followUpService)
    {
        $this->followUpService = $followUpService;
    }

    public function index(Request $request): View
    {
        Gate::authorize('viewAny', FollowUp::class);

        $pendingOnly = $request->boolean('pending');

        return view('follow-ups.index', compact('pendingOnly'));
    }

    public function store(FollowUpRequest $request): RedirectResponse
    {
        Gate::authorize('create', FollowUp::class);

        $this->followUpService->create($request->validated());

        return back()->with('success', 'Seguimiento registrado correctamente.');
    }

    public function update(FollowUpRequest $request, FollowUp $followUp): RedirectResponse
    {
        Gate::authorize('update', $followUp);

        $this->followUpService->update($followUp, $request->validated());

        return back()->with('success', 'Seguimiento actualizado.');
    }

    public function destroy(FollowUp $followUp): RedirectResponse
    {
        Gate::authorize('delete', $followUp);

        $this->followUpService->delete($followUp);

        return back()->with('success', 'Seguimiento eliminado.');
    }

    public function markDone(FollowUp $followUp): RedirectResponse
    {
        Gate::authorize('update', $followUp);

        $this->followUpService->markDone($followUp);

        return back()->with('success', 'Seguimiento marcado como realizado.');
    }

    /**
     * Listado plano para Tabulator (sin paginación remota, patrón api.parties.search).
     */
    public function search(Request $request): JsonResponse
    {
        Gate::authorize('viewAny', FollowUp::class);

        $query = FollowUp::query()
            ->with(['party', 'vehicle.vehicleModel.brand', 'estimate', 'creator']);

        if ($request->boolean('pending')) {
            $query->where('done', false);
        }

        if ($request->filled('q')) {
            $term = $request->input('q');
            $query->where(function ($q) use ($term) {
                $q->where('notes', 'like', "%{$term}%")
                    ->orWhereHas('vehicle', fn ($v) => $v->where('plate', 'like', "%{$term}%"))
                    ->orWhereHas('party', fn ($p) => $p
                        ->where('business_name', 'like', "%{$term}%")
                        ->orWhere('first_name', 'like', "%{$term}%")
                        ->orWhere('last_name', 'like', "%{$term}%"));
            });
        }

        $rows = $query
            ->orderByDesc('date')
            ->orderByDesc('id')
            ->limit($request->integer('limit', 100))
            ->get();

        return response()->json($rows->map(function (FollowUp $f) {
            return [
                'id' => $f->id,
                'date' => $f->date?->format('d/m/Y'),
                'type' => $f->type,
                'type_label' => $f->type_label,
                'notes' => $f->notes,
                'next_action_date' => $f->next_action_date?->format('d/m/Y'),
                'done' => (bool) $f->done,
                'party_name' => $f->party?->display_name,
                'plate' => $f->vehicle?->plate,
                'vehicle_label' => $f->vehicle
                    ? trim(($f->vehicle->vehicleModel?->brand?->name ?? '') . ' ' . ($f->vehicle->vehicleModel?->name ?? ''))
                    : '',
                'estimate_sn' => $f->estimate?->document_sn,
                'estimate_id' => $f->estimate_id,
            ];
        }));
    }
}
