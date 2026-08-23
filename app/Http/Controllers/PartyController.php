<?php

namespace App\Http\Controllers;

use App\Http\Requests\PartyRequest;
use App\Models\Party;
use App\Models\Ubigeo;
use App\Services\PartyService;
use App\Services\ReniecSunatService;
use App\Services\SunatExchangeService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class PartyController extends Controller
{
    protected PartyService $partyService;

    public function __construct(PartyService $partyService)
    {
        $this->partyService = $partyService;
    }

    public function index(): View
    {
        Gate::authorize('viewAny', Party::class);

        return view('parties.index');
    }

    public function create(): View
    {
        Gate::authorize('create', Party::class);

        $departamentos = Ubigeo::select('departamento')->distinct()->orderBy('departamento')->pluck('departamento');

        return view('parties.create', compact('departamentos'));
    }

    public function store(PartyRequest $request)
    {
        Gate::authorize('create', Party::class);

        $this->partyService->create($request->validated());

        return redirect()->route('parties.index')
            ->with('success', 'Party creada correctamente.');
    }

    public function show(Party $party): View
    {
        Gate::authorize('view', $party);

        $party->load(['ubigeo', 'vehicles.vehicleModel.brand']);

        return view('parties.show', compact('party'));
    }

    public function edit(Party $party): View
    {
        Gate::authorize('update', $party);

        $departamentos = Ubigeo::select('departamento')->distinct()->orderBy('departamento')->pluck('departamento');

        return view('parties.edit', compact('party', 'departamentos'));
    }

    public function update(PartyRequest $request, Party $party)
    {
        Gate::authorize('update', $party);

        $this->partyService->update($party, $request->validated());

        return redirect()->route('parties.index')
            ->with('success', 'Party actualizada correctamente.');
    }

    public function destroy(Party $party)
    {
        Gate::authorize('delete', $party);

        $this->partyService->delete($party);

        return redirect()->route('parties.index')
            ->with('success', 'Party eliminada correctamente.');
    }

    public function search(Request $request): JsonResponse
    {
        Gate::authorize('viewAny', Party::class);

        $query = Party::query()
            ->when($request->filled('q'), function ($q) use ($request) {
                $term = $request->query('q');
                $q->where(function ($sub) use ($term) {
                    $sub->where('business_name', 'like', "%{$term}%")
                        ->orWhere('document_number', 'like', "%{$term}%")
                        ->orWhere('first_name', 'like', "%{$term}%")
                        ->orWhere('last_name', 'like', "%{$term}%");
                });
            })
            ->when($request->filled('document_type') && $request->filled('document_number'), function ($q) use ($request) {
                $q->where('document_type', $request->query('document_type'))
                    ->where('document_number', $request->query('document_number'));
            })
            ->when($request->filled('id'), function ($q) use ($request) {
                $q->whereKey($request->query('id'));
            })
            ->when($request->boolean('is_insurance_company'), function ($q) {
                $q->where('is_insurance_company', true);
            })
            ->orderByRaw("COALESCE(business_name, CONCAT(last_name, ' ', first_name))")
            ->limit($request->integer('limit', 20));

        return response()->json($query->get()
            ->map(fn (Party $party) => [
                'id' => $party->id,
                'display_name' => $party->display_name,
                'document_type' => $party->document_type,
                'document_number' => $party->document_number,
                'first_name' => $party->first_name,
                'last_name' => $party->last_name,
                'business_name' => $party->business_name,
                'phone' => $party->phone,
                'mobile' => $party->mobile,
                'display_phone' => $party->mobile ?: $party->phone,
                'email' => $party->email,
                'is_insurance_company' => $party->is_insurance_company,
                'ubigeo_code' => $party->ubigeo_code,
                'address' => $party->address,
            ]));
    }

    public function quickStore(Request $request): JsonResponse
    {
        Gate::authorize('create', Party::class);

        $roles = ['owner', 'driver', 'approver', 'operator', 'billing', 'insurance_company', 'emergency_contact', 'other'];

        $docRequiredRoles = ['owner', 'billing'];

        $validated = $request->validate([
            'role' => ['nullable', Rule::in($roles)],
            'document_type' => ['required', 'in:1,6,4,7,A'],
            'document_number' => [Rule::requiredIf(fn () => in_array($request->input('role'), $docRequiredRoles, true)), 'nullable', 'string', 'max:20', Rule::unique('parties')->where('document_type', $request->input('document_type'))->ignore($request->input('id'))],
            'first_name' => [$request->input('document_type') === '6' ? 'nullable' : 'required_without:last_name', 'string', 'max:255'],
            'last_name' => [$request->input('document_type') === '6' ? 'nullable' : 'required_without:first_name', 'string', 'max:255'],
            'business_name' => [$request->input('document_type') === '6' ? 'required' : 'nullable', 'string', 'max:255'],
            'email' => ['nullable', 'email', 'max:255'],
            'phone' => ['nullable', 'string', 'max:20'],
            'mobile' => [$request->has('role') ? 'required' : 'nullable', 'string', 'max:20'],
            'ubigeo_code' => ['nullable', 'string', 'size:6', 'exists:ubigeos,code'],
            'address' => ['nullable', 'string', 'max:255'],
            'is_insurance_company' => ['nullable', 'boolean'],
        ]);

        $party = $this->partyService->create($validated);

        return response()->json([
            'id' => $party->id,
            'display_name' => $party->display_name,
            'document_type' => $party->document_type,
            'document_number' => $party->document_number,
        ], 201);
    }

    public function resolveUbigeo(Request $request): JsonResponse
    {
        $request->validate(['code' => ['required', 'string', 'size:6']]);

        $ubigeo = Ubigeo::where('code', $request->string('code'))->first();

        if (!$ubigeo) {
            return response()->json(['error' => 'Código de ubigeo no encontrado.'], 404);
        }

        return response()->json([
            'code' => $ubigeo->code,
            'departamento' => $ubigeo->departamento,
            'provincia' => $ubigeo->provincia,
            'distrito' => $ubigeo->distrito,
        ]);
    }

    public function departamentos(): JsonResponse
    {
        return response()->json(
            Ubigeo::select('departamento')
                ->distinct()
                ->orderBy('departamento')
                ->pluck('departamento')
        );
    }

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

    public function quickUpdate(Request $request, Party $party): JsonResponse
    {
        Gate::authorize('update', $party);

        $validated = $request->validate([
            'document_type' => ['nullable', 'string', 'max:10'],
            'document_number' => ['nullable', 'string', 'max:20'],
            'first_name' => ['nullable', 'string', 'max:150'],
            'last_name' => ['nullable', 'string', 'max:150'],
            'business_name' => ['nullable', 'string', 'max:200'],
            'email' => ['nullable', 'email', 'max:150'],
            'phone' => ['nullable', 'string', 'max:30'],
            'mobile' => ['nullable', 'string', 'max:30'],
            'ubigeo_code' => ['nullable', 'string', 'size:6', 'exists:ubigeos,code'],
            'address' => ['nullable', 'string', 'max:255'],
        ]);

        $party->update(array_merge($validated, ['updated_by' => auth()->id()]));

        return response()->json([
            'id' => $party->id,
            'display_name' => $party->display_name,
            'document_type' => $party->document_type,
            'document_number' => $party->document_number,
            'first_name' => $party->first_name,
            'last_name' => $party->last_name,
            'business_name' => $party->business_name,
            'email' => $party->email,
            'phone' => $party->phone,
            'mobile' => $party->mobile,
            'ubigeo_code' => $party->ubigeo_code,
            'address' => $party->address,
        ]);
    }

    public function searchByDocument(Request $request, ReniecSunatService $reniecSunat): JsonResponse
    {
        Gate::authorize('create', Party::class);

        $request->validate([
            'document_type' => ['required', 'string', 'in:1,6'],
            'document_number' => ['required', 'string', 'max:11'],
        ]);

        $result = $reniecSunat->searchByDocument(
            $request->string('document_type'),
            $request->string('document_number')
        );

        if ($result === null) {
            return response()->json(['error' => 'No se encontraron datos para el documento ingresado.'], 404);
        }

        return response()->json($result);
    }

    public function tipoCambio(Request $request, SunatExchangeService $exchange): JsonResponse
    {
        Gate::authorize('viewAny', Party::class);

        $request->validate([
            'fecha' => ['nullable', 'date', 'date_format:Y-m-d'],
            'year' => ['nullable', 'integer', 'min:2000', 'max:' . (date('Y') + 1)],
            'month' => ['nullable', 'integer', 'min:1', 'max:12'],
        ]);

        if ($request->filled('fecha')) {
            $result = $exchange->getTipoCambio($request->string('fecha'));

            if ($result === null) {
                return response()->json(['error' => 'No se encontró tipo de cambio para la fecha indicada.'], 404);
            }

            return response()->json($result);
        }

        if ($request->filled('year') && $request->filled('month')) {
            $result = $exchange->getTipoCambioMes(
                $request->integer('year'),
                $request->integer('month')
            );

            if ($result === null) {
                return response()->json(['error' => 'No se encontraron tipos de cambio para el mes indicado.'], 404);
            }

            return response()->json($result);
        }

        return response()->json(['error' => 'Debe indicar "fecha" o "year" + "month".'], 422);
    }
}