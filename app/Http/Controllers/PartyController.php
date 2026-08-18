<?php

namespace App\Http\Controllers;

use App\Http\Requests\PartyRequest;
use App\Models\Party;
use App\Models\Ubigeo;
use App\Services\PartyService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;

class PartyController extends Controller
{
    protected PartyService $partyService;

    public function __construct(PartyService $partyService)
    {
        $this->partyService = $partyService;
    }

    /**
     * Display a listing of the resource.
     */
    public function index(): View
    {
        Gate::authorize('viewAny', Party::class);

        return view('parties.index');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        Gate::authorize('create', Party::class);

        $departamentos = Ubigeo::select('departamento')->distinct()->orderBy('departamento')->pluck('departamento');

        return view('parties.create', compact('departamentos'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(PartyRequest $request)
    {
        Gate::authorize('create', Party::class);

        $this->partyService->create($request->validated());

        return redirect()->route('parties.index')
            ->with('success', 'Party creada correctamente.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Party $party): View
    {
        Gate::authorize('view', $party);

        $party->load(['ubigeo', 'vehicles']);

        return view('parties.show', compact('party'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Party $party): View
    {
        Gate::authorize('update', $party);

        $departamentos = Ubigeo::select('departamento')->distinct()->orderBy('departamento')->pluck('departamento');

        return view('parties.edit', compact('party', 'departamentos'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(PartyRequest $request, Party $party)
    {
        Gate::authorize('update', $party);

        $this->partyService->update($party, $request->validated());

        return redirect()->route('parties.index')
            ->with('success', 'Party actualizada correctamente.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Party $party)
    {
        Gate::authorize('delete', $party);

        $this->partyService->delete($party);

        return redirect()->route('parties.index')
            ->with('success', 'Party eliminada correctamente.');
    }

    /**
     * AJAX search for parties (Tom Select / Tabulator).
     */
    public function search(Request $request): JsonResponse
    {
        Gate::authorize('viewAny', Party::class);

        $query = Party::query()
            ->when($request->filled('q'), function ($q) use ($request) {
                $term = $request->query('q');
                $q->where('business_name', 'like', "%{$term}%")
                    ->orWhere('document_number', 'like', "%{$term}%")
                    ->orWhere('first_name', 'like', "%{$term}%")
                    ->orWhere('last_name', 'like', "%{$term}%");
            })
            ->when($request->filled('id'), function ($q) use ($request) {
                $q->whereKey($request->query('id'));
            })
            ->orderByRaw("COALESCE(business_name, CONCAT(first_name, ' ', last_name))")
            ->limit($request->integer('limit', 20));

        return response()->json($query->get()
            ->map(fn (Party $party) => [
                'id' => $party->id,
                'display_name' => $party->display_name,
                'type' => $party->type,
                'document_type' => $party->document_type,
                'document_number' => $party->document_number,
                'phone' => $party->mobile ?: $party->phone,
                'email' => $party->email,
                'is_insurance_company' => $party->is_insurance_company,
            ]));
    }

    /**
     * Create a party quickly from the vehicle form (AJAX).
     */
    public function quickStore(Request $request): JsonResponse
    {
        Gate::authorize('create', Party::class);

        $validated = $request->validate([
            'type' => ['required', 'in:person,company'],
            'document_type' => ['required', 'in:DNI,RUC,PAS,CEX'],
            'document_number' => ['required', 'string', 'max:20', 'unique:parties,document_number'],
            'first_name' => ['nullable', 'string', 'max:255'],
            'last_name' => ['nullable', 'string', 'max:255'],
            'business_name' => ['nullable', 'string', 'max:255'],
            'email' => ['nullable', 'email', 'max:255'],
            'phone' => ['nullable', 'string', 'max:20'],
            'mobile' => ['nullable', 'string', 'max:20'],
        ]);

        $party = $this->partyService->create($validated);

        return response()->json([
            'id' => $party->id,
            'display_name' => $party->display_name,
            'type' => $party->type,
            'document_type' => $party->document_type,
            'document_number' => $party->document_number,
        ], 201);
    }

    /**
     * AJAX for ubigeo cascading selects.
     */
    public function provincias(Request $request): JsonResponse
    {
        $departamento = $request->query('departamento');

        $provincias = Ubigeo::where('departamento', $departamento)
            ->select('provincia')
            ->distinct()
            ->orderBy('provincia')
            ->pluck('provincia');

        return response()->json($provincias);
    }

    /**
     * AJAX for ubigeo cascading selects.
     */
    public function distritos(Request $request): JsonResponse
    {
        $departamento = $request->query('departamento');
        $provincia = $request->query('provincia');

        $distritos = Ubigeo::where('departamento', $departamento)
            ->where('provincia', $provincia)
            ->select('distrito', 'code')
            ->orderBy('distrito')
            ->get();

        return response()->json($distritos);
    }
}