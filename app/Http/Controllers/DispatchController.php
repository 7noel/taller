<?php

namespace App\Http\Controllers;

use App\Jobs\EmitDispatchJob;
use App\Models\Dispatch;
use App\Models\Party;
use App\Services\Facturacion\DispatchService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Redirect;
use Illuminate\View\View;

class DispatchController extends Controller
{
    public function __construct(protected DispatchService $dispatchService)
    {
    }

    public function index(): View
    {
        Gate::authorize('viewAny', Dispatch::class);

        return view('dispatches.index');
    }

    public function create(Request $request): View
    {
        Gate::authorize('create', Dispatch::class);

        return view('dispatches.create');
    }

    public function store(Request $request): RedirectResponse
    {
        Gate::authorize('create', Dispatch::class);

        $data = $request->validate([
            'party_id' => 'nullable|exists:parties,id',
            'vehicle_id' => 'nullable|exists:vehicles,id',
            'invoice_id' => 'nullable|exists:invoices,id',
            'dispatch_type' => 'required|in:remitente,transportista',
            'motivo_traslado' => 'required|string|max:5',
            'descripcion_motivo_traslado' => 'nullable|string|max:250',
            'modo_transporte' => 'required|in:01,02',
            'fecha_de_traslado' => 'required|date',
            'fecha_de_entrega' => 'nullable|date',
            'peso_total' => 'nullable|numeric|min:0',
            'unidad_peso' => 'nullable|string|max:5',
            'numero_de_bultos' => 'nullable|integer|min:0',
            'punto_partida_ubigeo' => 'nullable|string|max:6',
            'punto_partida_direccion' => 'nullable|string|max:250',
            'punto_llegada_ubigeo' => 'nullable|string|max:6',
            'punto_llegada_direccion' => 'nullable|string|max:250',
            'transportista_documento_tipo' => 'nullable|string|max:5',
            'transportista_documento_numero' => 'nullable|string|max:15',
            'transportista_denominacion' => 'nullable|string|max:100',
            'conductor_documento_tipo' => 'nullable|string|max:5',
            'conductor_documento_numero' => 'nullable|string|max:15',
            'conductor_nombre' => 'nullable|string|max:100',
            'conductor_apellidos' => 'nullable|string|max:100',
            'conductor_numero_licencia' => 'nullable|string|max:20',
            'vehiculo_placa' => 'nullable|string|max:20',
            'vehiculo_marca' => 'nullable|string|max:100',
            'vehiculo_modelo' => 'nullable|string|max:100',
            'observations' => 'nullable|string|max:1000',
            'items' => 'required|array|min:1',
            'items.*.description' => 'required|string|max:250',
            'items.*.quantity' => 'required|numeric|min:0.01',
            'items.*.uom' => 'nullable|string|max:5',
            'items.*.codigo_interno' => 'nullable|string|max:250',
        ]);

        $dispatch = $this->dispatchService->create($data, $data['items']);

        return Redirect::route('dispatches.show', $dispatch)
            ->with('success', 'Guía creada como borrador.');
    }

    public function show(Dispatch $dispatch): View
    {
        Gate::authorize('view', $dispatch);

        $dispatch->load(['establishment', 'party', 'vehicle.vehicleModel.brand', 'items', 'invoice', 'creator']);

        return view('dispatches.show', compact('dispatch'));
    }

    public function emit(Dispatch $dispatch): RedirectResponse
    {
        Gate::authorize('update', $dispatch);

        if (! in_array($dispatch->status, [Dispatch::STATUS_DRAFT, Dispatch::STATUS_REJECTED], true)) {
            return back()->withErrors(['general' => 'La guía ya fue emitida.']);
        }

        try {
            EmitDispatchJob::dispatch($dispatch->loadMissing(['party', 'items', 'invoice']));
        } catch (\Exception $e) {
            return back()->withErrors(['general' => 'No se pudo encolar la emisión: ' . $e->getMessage()]);
        }

        return Redirect::route('dispatches.show', $dispatch)
            ->with('success', 'Emisión en proceso. La guía se actualizará en segundos.');
    }

    public function search(Request $request): JsonResponse
    {
        Gate::authorize('viewAny', Dispatch::class);

        $query = Dispatch::query()->with(['party', 'invoice'])->latest('id');

        if ($request->filled('q')) {
            $term = $request->input('q');
            $query->where(function ($q) use ($term) {
                $q->where('document_sn', 'like', "%{$term}%")
                    ->orWhereHas('party', fn ($p) => $p
                        ->where('business_name', 'like', "%{$term}%")
                        ->orWhere('first_name', 'like', "%{$term}%")
                        ->orWhere('last_name', 'like', "%{$term}%"));
            });
        }

        return response()->json($query->limit($request->integer('limit', 100))->get()->map(fn (Dispatch $d) => [
            'id' => $d->id,
            'document_sn' => $d->document_sn,
            'type_label' => $d->type_label,
            'party_name' => $d->party?->display_name,
            'motivo' => $d->motivo_traslado_label,
            'fecha' => $d->fecha_de_traslado?->format('d/m/Y'),
            'invoice_sn' => $d->invoice?->document_sn,
            'status' => $d->status,
            'status_label' => $d->status_label,
        ]));
    }
}
