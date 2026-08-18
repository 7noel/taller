<?php

namespace App\Http\Controllers;

use App\Http\Requests\ClientRequest;
use App\Models\Client;
use App\Models\Establishment;
use App\Models\Ubigeo;
use App\Services\ClientService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;

class ClientController extends Controller
{
    protected ClientService $clientService;

    public function __construct(ClientService $clientService)
    {
        $this->clientService = $clientService;
    }

    /**
     * Display a listing of the resource.
     */
    public function index(): View
    {
        Gate::authorize('viewAny', Client::class);

        return view('clients.index');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        Gate::authorize('create', Client::class);

        $establishments = Establishment::all();
        $departamentos = Ubigeo::select('departamento')->distinct()->orderBy('departamento')->pluck('departamento');

        return view('clients.create', compact('establishments', 'departamentos'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(ClientRequest $request)
    {
        Gate::authorize('create', Client::class);

        $this->clientService->create($request->validated());

        return redirect()->route('clients.index')
            ->with('success', 'Cliente creado correctamente.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Client $client): View
    {
        Gate::authorize('view', $client);

        $client->load(['vehicles', 'ubigeo', 'establishment']);

        return view('clients.show', compact('client'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Client $client): View
    {
        Gate::authorize('update', $client);

        $establishments = Establishment::all();
        $departamentos = Ubigeo::select('departamento')->distinct()->orderBy('departamento')->pluck('departamento');

        return view('clients.edit', compact('client', 'establishments', 'departamentos'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(ClientRequest $request, Client $client)
    {
        Gate::authorize('update', $client);

        $this->clientService->update($client, $request->validated());

        return redirect()->route('clients.index')
            ->with('success', 'Cliente actualizado correctamente.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Client $client)
    {
        Gate::authorize('delete', $client);

        $this->clientService->delete($client);

        return redirect()->route('clients.index')
            ->with('success', 'Cliente eliminado correctamente.');
    }

    /**
     * AJAX search for clients (Tom Select / Tabulator).
     */
    public function search(Request $request): JsonResponse
    {
        Gate::authorize('viewAny', Client::class);

        $query = Client::query()
            ->with('vehicles')
            ->when($request->filled('q'), function ($q) use ($request) {
                $term = $request->query('q');
                $q->where('business_name', 'like', "%{$term}%")
                    ->orWhere('document_number', 'like', "%{$term}%");
            })
            ->when($request->filled('id'), function ($q) use ($request) {
                $q->whereKey($request->query('id'));
            })
            ->orderBy('business_name')
            ->limit($request->integer('limit', 20));

        return response()->json($query->get()
            ->map(fn (Client $client) => [
                'id' => $client->id,
                'business_name' => $client->business_name,
                'document_number' => $client->document_number,
                'document_type' => $client->document_type,
                'phone' => $client->phone,
                'email' => $client->email,
                'vehicles_count' => $client->vehicles_count ?? $client->vehicles->count(),
            ]));
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