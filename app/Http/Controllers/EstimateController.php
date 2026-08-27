<?php

namespace App\Http\Controllers;

use App\Http\Requests\EstimateRequest;
use App\Jobs\SendWhatsAppMessage;
use App\Models\CheckIn;
use App\Models\Estimate;
use App\Models\PartCategory;
use App\Models\Party;
use App\Models\ServiceCategory;
use App\Models\User;
use App\Services\EstimateCalculationService;
use App\Services\EstimateService;
use App\Services\NotificationService;
use App\Services\WhatsAppService;
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
            'thirdPartyOrders',
        ]);

        $grouped = $this->service->getClientGroupedItems($estimate);

        // Datos para el botón "Enviar por WhatsApp" / "Copiar enlace" del portal.
        $vehicle = $estimate->vehicle;
        $recipient = $vehicle ? $this->service->resolveRecipient($vehicle) : null;
        $publicLink = $vehicle?->public_link;
        $initialMessage = app(NotificationService::class)->buildMessage('estimate_ready', [
            'recipient' => $recipient['contact_name'] ?? 'cliente',
            'plate' => $vehicle?->plate ?? '',
            'sn' => $estimate->document_sn,
            'total' => number_format((float) $estimate->total, 2) . ' ' . ($estimate->currency ?? 'PEN'),
            'link' => $publicLink ?? '',
        ]);
        $recipientsUrl = $vehicle ? route('api.vehicles.recipients', $vehicle) : '';
        $actionUrl = route('estimates.whatsapp', $estimate);

        return view('estimates.show', compact(
            'estimate',
            'grouped',
            'recipient',
            'publicLink',
            'initialMessage',
            'recipientsUrl',
            'actionUrl'
        ));
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
            'thirdPartyOrders',
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
            $establishmentId ? (int) $establishmentId : null,
            $request->input('third_party_orders', []),
            (float) $request->input('franchise_minimum_amount', 0),
            (float) $request->input('franchise_percentage', 0),
            (bool) $request->input('franchise_minimum_includes_tax', false)
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

    public function approveInsurance(Request $request, Estimate $estimate)
    {
        Gate::authorize('approveInsurance', $estimate);
        $request->validate(['date' => ['nullable', 'date']]);
        $this->service->changeStatus($estimate, 'approved_insurance', null, $request->input('date'));

        return back()->with('success', 'Presupuesto aprobado por el seguro.');
    }

    public function rejectInsurance(Request $request, Estimate $estimate)
    {
        Gate::authorize('rejectInsurance', $estimate);
        $request->validate([
            'reason' => ['required', 'string', 'max:500'],
            'date' => ['nullable', 'date'],
        ], ['reason.required' => 'Indica el motivo del rechazo del seguro.']);
        $this->service->changeStatus($estimate, 'rejected_insurance', $request->input('reason'), $request->input('date'));

        return back()->with('success', 'Presupuesto rechazado por el seguro.');
    }

    public function sendToClient(Estimate $estimate)
    {
        Gate::authorize('sendToClient', $estimate);

        if ($estimate->service_type === 'siniestro' && ! in_array($estimate->status, ['approved_insurance', 'rejected_client'], true)) {
            return back()->with('error', 'Para un siniestro, el seguro debe aprobar el presupuesto antes de enviarlo al cliente.');
        }

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

    /**
     * Envía el enlace del portal por WhatsApp (wa.me manual o Evolution API en cola).
     *
     * Graba el snapshot del destinatario (last_sent_to / last_sent_to_phone) en el
     * momento del envío: será quien aparezca como responsable si el cliente aprueba
     * o rechaza el presupuesto desde el portal.
     */
    public function sendWhatsApp(Request $request, Estimate $estimate)
    {
        Gate::authorize('view', $estimate);

        $validated = $request->validate([
            'recipient_name' => ['nullable', 'string', 'max:255'],
            'phone' => ['required', 'string', 'max:30'],
            'message' => ['required', 'string', 'max:1500'],
            'send_method' => ['required', 'in:wa_me,api'],
        ]);

        $estimate->update([
            'last_sent_to' => $validated['recipient_name'] ?: null,
            'last_sent_to_phone' => $validated['phone'],
            'last_sent_at' => now(),
        ]);

        $establishment = $estimate->establishment ?? auth()->user()?->establishment;

        if ($validated['send_method'] === 'api') {
            $whatsapp = app(WhatsAppService::class);
            $credentials = $establishment ? $whatsapp->resolveCredentials($establishment) : [];
            $configured = $establishment
                && ! empty($credentials['api_url'])
                && ! empty($credentials['token'])
                && ! empty($credentials['instance'])
                && $credentials['enabled'];

            if (! $configured) {
                return back()->with('error', 'WhatsApp no está configurado en este establecimiento. Configura API URL, Token, Instancia y habilita el envío (o usa "Abrir WhatsApp").');
            }

            SendWhatsAppMessage::dispatch($establishment, $validated['phone'], $validated['message']);

            return back()->with('success', 'Mensaje encolado para envío por WhatsApp.');
        }

        $waLink = app(WhatsAppService::class)->buildWaLink($validated['phone'], $validated['message']);

        return redirect()->away($waLink);
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