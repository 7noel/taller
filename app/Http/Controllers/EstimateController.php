<?php

namespace App\Http\Controllers;

use App\Http\Requests\EstimateRequest;
use App\Models\CheckIn;
use App\Models\Estimate;
use App\Models\PartCategory;
use App\Models\Party;
use App\Models\ServiceCategory;
use App\Models\User;
use App\Services\EstimateCalculationService;
use App\Services\EstimateService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;

class EstimateController extends Controller
{
    protected EstimateService $service;
    protected EstimateCalculationService $calculation;

    public function __construct(EstimateService $service, EstimateCalculationService $calculation)
    {
        $this->service = $service;
        $this->calculation = $calculation;
    }

    public function index(): View
    {
        Gate::authorize('viewAny', Estimate::class);

        return view('estimates.index');
    }

    public function create(Request $request): View
    {
        Gate::authorize('create', Estimate::class);

        $checkIn = null;
        if ($request->filled('check_in_id')) {
            $checkIn = CheckIn::with(['vehicle.vehicleModel.brand', 'client', 'insuranceCompany', 'establishment'])
                ->find($request->integer('check_in_id'));
        }

        $advisors = User::role('Asesor')->orderBy('name')->get();
        $establishment = auth()->user()?->establishment;
        $serviceCategories = ServiceCategory::query()->where('is_active', true)->orderBy('name')->get();
        $partCategories = PartCategory::query()->where('is_active', true)->orderBy('name')->get();

        return view('estimates.create', compact('checkIn', 'advisors', 'establishment', 'serviceCategories', 'partCategories'));
    }

    public function store(EstimateRequest $request)
    {
        Gate::authorize('create', Estimate::class);

        $this->service->create($request->validated());

        return redirect()->route('estimates.index')
            ->with('success', 'Presupuesto creado correctamente.');
    }

    public function show(Estimate $estimate): View
    {
        Gate::authorize('view', $estimate);

        $estimate->load([
            'vehicle.vehicleModel.brand',
            'client',
            'insuranceCompany',
            'advisor',
            'establishment',
            'documentSeries.documentType',
            'creator',
            'updater',
            'statusHistory.user',
        ]);

        $grouped = $this->service->getClientGroupedItems($estimate);

        return view('estimates.show', compact('estimate', 'grouped'));
    }

    public function edit(Estimate $estimate): View
    {
        Gate::authorize('update', $estimate);

        $estimate->load([
            'vehicle.vehicleModel.brand',
            'client',
            'insuranceCompany',
            'establishment',
            'items.service',
            'items.part',
        ]);

        $advisors = User::role('Asesor')->orderBy('name')->get();
        $establishment = $estimate->establishment ?? auth()->user()?->establishment;
        $serviceCategories = ServiceCategory::query()->where('is_active', true)->orderBy('name')->get();
        $partCategories = PartCategory::query()->where('is_active', true)->orderBy('name')->get();

        return view('estimates.edit', compact('estimate', 'advisors', 'establishment', 'serviceCategories', 'partCategories'));
    }

    public function update(EstimateRequest $request, Estimate $estimate)
    {
        Gate::authorize('update', $estimate);

        $this->service->update($estimate, $request->validated());

        return redirect()->route('estimates.index')
            ->with('success', 'Presupuesto actualizado correctamente.');
    }

    public function destroy(Estimate $estimate)
    {
        Gate::authorize('delete', $estimate);

        $this->service->delete($estimate);

        return redirect()->route('estimates.index')
            ->with('success', 'Presupuesto eliminado correctamente.');
    }

    public function search(Request $request): JsonResponse
    {
        Gate::authorize('viewAny', Estimate::class);

        return response()->json($this->service->getSearchResults($request->all()));
    }

    /**
     * Calcula totales en vivo sin guardar (vista previa del frontend).
     */
    public function calculate(Request $request): JsonResponse
    {
        Gate::authorize('create', Estimate::class);

        $items = $request->input('items', []);
        $establishmentId = $request->integer('establishment_id') ?: auth()->user()?->establishment_id;

        $result = $this->calculation->preview(
            $items,
            $request->input('global_discount_type'),
            (float) $request->input('global_discount_value', 0),
            $establishmentId ? (int) $establishmentId : null
        );

        return response()->json($result);
    }

    /**
     * Devuelve los datos de un inventario para precargar el formulario.
     */
    public function fromCheckIn(CheckIn $checkIn): JsonResponse
    {
        Gate::authorize('create', Estimate::class);

        $checkIn->load(['vehicle.vehicleModel.brand', 'client', 'insuranceCompany', 'establishment']);

        $rates = $this->service->resolveRates($checkIn->insuranceCompany, $checkIn->establishment);

        return response()->json([
            'check_in_id' => $checkIn->id,
            'vehicle_id' => $checkIn->vehicle_id,
            'vehicle' => $checkIn->vehicle ? [
                'id' => $checkIn->vehicle->id,
                'plate' => $checkIn->vehicle->plate,
                'brand' => $checkIn->vehicle->vehicleModel?->brand?->name,
                'model' => $checkIn->vehicle->vehicleModel?->name,
            ] : null,
            'client_id' => $checkIn->client_id,
            'client' => $checkIn->client ? [
                'id' => $checkIn->client->id,
                'display_name' => $checkIn->client->display_name,
                'document_number' => $checkIn->client->document_number,
            ] : null,
            'insurance_company_id' => $checkIn->insurance_company_id,
            'insurance_company' => $checkIn->insuranceCompany ? [
                'id' => $checkIn->insuranceCompany->id,
                'business_name' => $checkIn->insuranceCompany->business_name,
                'document_number' => $checkIn->insuranceCompany->document_number,
            ] : null,
            'claim_number' => $checkIn->claim_number,
            'service_type' => $checkIn->service_type,
            'establishment_id' => $checkIn->establishment_id,
            'currency' => $checkIn->establishment?->base_currency ?? 'PEN',
            'exchange_rate' => 1,
            'hourly_rate' => $rates['hourly_rate'],
            'panel_rate' => $rates['panel_rate'],
        ]);
    }

    // =====================================================
    // Acciones de estado
    // =====================================================

    public function sendToInsurance(Estimate $estimate)
    {
        Gate::authorize('sendToInsurance', $estimate);
        $this->service->changeStatus($estimate, 'sent_insurance');

        return back()->with('success', 'Presupuesto enviado a aprobación del seguro.');
    }

    public function approveInsurance(Estimate $estimate)
    {
        Gate::authorize('approveInsurance', $estimate);
        $this->service->changeStatus($estimate, 'approved_insurance');

        return back()->with('success', 'Presupuesto aprobado por el seguro.');
    }

    public function rejectInsurance(Request $request, Estimate $estimate)
    {
        Gate::authorize('rejectInsurance', $estimate);
        $request->validate(['reason' => ['nullable', 'string', 'max:500']]);
        $this->service->changeStatus($estimate, 'rejected_insurance', $request->input('reason'));

        return back()->with('success', 'Presupuesto rechazado por el seguro.');
    }

    public function sendToClient(Estimate $estimate)
    {
        Gate::authorize('sendToClient', $estimate);
        $this->service->changeStatus($estimate, 'sent_client');

        return back()->with('success', 'Presupuesto enviado a aprobación del cliente.');
    }

    public function approveClient(Estimate $estimate)
    {
        Gate::authorize('approveClient', $estimate);
        $this->service->changeStatus($estimate, 'approved_client');

        return back()->with('success', 'Presupuesto aprobado por el cliente.');
    }

    public function rejectClient(Request $request, Estimate $estimate)
    {
        Gate::authorize('rejectClient', $estimate);
        $request->validate(['reason' => ['nullable', 'string', 'max:500']]);
        $this->service->changeStatus($estimate, 'rejected_client', $request->input('reason'));

        return back()->with('success', 'Presupuesto rechazado por el cliente.');
    }

    public function startRepair(Estimate $estimate)
    {
        Gate::authorize('startRepair', $estimate);
        $this->service->changeStatus($estimate, 'in_repair');

        return back()->with('success', 'Presupuesto movido a reparación.');
    }

    public function finalize(Estimate $estimate)
    {
        Gate::authorize('finalize', $estimate);
        $this->service->changeStatus($estimate, 'finalized');

        return back()->with('success', 'Presupuesto finalizado.');
    }

    public function returnToDraft(Estimate $estimate)
    {
        Gate::authorize('returnToDraft', $estimate);
        $this->service->changeStatus($estimate, 'draft');

        return back()->with('success', 'Presupuesto reabierto para edición.');
    }
}