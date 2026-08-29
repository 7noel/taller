<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Requests\SurveyRequest;
use App\Models\CheckIn;
use App\Models\Estimate;
use App\Models\FormTemplate;
use App\Models\Vehicle;
use App\Models\WorkOrder;
use App\Services\CheckInService;
use App\Services\EstimateService;
use App\Services\FormAnswerService;
use Illuminate\Http\Request;
use Illuminate\View\View;
use RuntimeException;

/**
 * Portal público del cliente.
 *
 * Un único enlace por vehículo (/c/{token}) que muestra todo lo pendiente de
 * aprobación (inventarios y presupuestos), el estado del caso activo y el
 * histórico de servicios. No requiere autenticación: la seguridad se basa en
 * el token aleatorio del vehículo + validación de pertenencia e idempotencia.
 */
class PortalController extends Controller
{
    protected CheckInService $checkInService;
    protected EstimateService $estimateService;

    public function __construct(CheckInService $checkInService, EstimateService $estimateService)
    {
        $this->checkInService = $checkInService;
        $this->estimateService = $estimateService;
    }

    /**
     * Pantalla principal: todo lo pendiente de aprobación + estado + histórico.
     */
    public function show(string $token): View
    {
        $vehicle = $this->resolveVehicle($token);

        $vehicle->load(['vehicleModel.brand', 'relationships.party']);

        $pendingCheckIns = CheckIn::query()
            ->where('vehicle_id', $vehicle->id)
            ->where('status', 'pending_approval')
            ->with(['establishment', 'client'])
            ->orderByDesc('created_at')
            ->get();

        $pendingEstimates = Estimate::query()
            ->where('vehicle_id', $vehicle->id)
            ->where('status', 'sent_client')
            ->with(['establishment', 'client'])
            ->orderByDesc('created_at')
            ->get();

        // Caso activo más reciente (para el banner de estado).
        $activeCheckIn = CheckIn::query()
            ->where('vehicle_id', $vehicle->id)
            ->whereIn('status', ['pending_approval', 'approved', 'rejected'])
            ->latest('created_at')
            ->first();

        // Presupuesto activo más reciente (para el banner de estado del presupuesto).
        $activeEstimate = Estimate::query()
            ->where('vehicle_id', $vehicle->id)
            ->whereIn('status', ['sent_client', 'approved_insurance', 'approved_client', 'rejected_insurance', 'rejected_client', 'in_repair', 'finalized'])
            ->latest('created_at')
            ->first();

        $history = CheckIn::query()
            ->where('vehicle_id', $vehicle->id)
            ->with(['client', 'establishment', 'estimates'])
            ->orderByDesc('created_at')
            ->get();

        return view('public.portal', compact('vehicle', 'pendingCheckIns', 'pendingEstimates', 'activeCheckIn', 'activeEstimate', 'history'));
    }

    public function showCheckIn(string $token, CheckIn $checkIn): View
    {
        $vehicle = $this->resolveVehicle($token);
        $this->assertOwnership($vehicle, $checkIn);

        $checkIn->load([
            'vehicle.vehicleModel.brand',
            'client',
            'insuranceCompany',
            'establishment',
            'checklistResults.checklistItem',
            'damages',
            'photos',
        ]);

        // Daños con coordenadas para pintar sobre la silueta (misma lógica que la vista admin).
        $damagesWithCoords = $checkIn->damages
            ->filter(fn ($d) => $d->pos_x !== null && $d->pos_y !== null)
            ->map(fn ($d) => [
                'damage_type' => $d->damage_type,
                'pos_x' => $d->pos_x,
                'pos_y' => $d->pos_y,
            ])
            ->values();

        return view('public.checkin', compact('vehicle', 'checkIn', 'damagesWithCoords'));
    }

    public function showEstimate(string $token, Estimate $estimate): View
    {
        $vehicle = $this->resolveVehicle($token);
        $this->assertOwnership($vehicle, $estimate);

        $estimate->load([
            'vehicle.vehicleModel.brand',
            'client',
            'insuranceCompany',
            'establishment',
            'items.service.category',
            'items.part.category',
            'items.serviceCategory',
            'items.partCategory',
            'thirdPartyOrders',
        ]);

        $grouped = $this->estimateService->getClientGroupedItems($estimate);

        return view('public.estimate', compact('vehicle', 'estimate', 'grouped'));
    }

    public function approveCheckIn(Request $request, string $token, CheckIn $checkIn)
    {
        $vehicle = $this->resolveVehicle($token);
        $this->assertOwnership($vehicle, $checkIn);

        try {
            $this->checkInService->approveByClient($checkIn, $request->ip(), $request->userAgent());
        } catch (RuntimeException $e) {
            return back()->withErrors(['approval' => $e->getMessage()]);
        }

        return redirect()->route('public.portal', $token)
            ->with('success', '¡Inventario aprobado! Gracias por tu confirmación.');
    }

    public function rejectCheckIn(Request $request, string $token, CheckIn $checkIn)
    {
        $vehicle = $this->resolveVehicle($token);
        $this->assertOwnership($vehicle, $checkIn);

        $validated = $request->validate([
            'reason' => ['required', 'string', 'max:500'],
        ], ['reason.required' => 'Indica el motivo del rechazo.']);

        try {
            $this->checkInService->rejectByClient($checkIn, $validated['reason'], $request->ip(), $request->userAgent());
        } catch (RuntimeException $e) {
            return back()->withErrors(['approval' => $e->getMessage()]);
        }

        return redirect()->route('public.portal', $token)
            ->with('success', 'Registramos tu observación. El taller revisará el inventario.');
    }

    public function approveEstimate(Request $request, string $token, Estimate $estimate)
    {
        $vehicle = $this->resolveVehicle($token);
        $this->assertOwnership($vehicle, $estimate);

        try {
            $this->estimateService->changeStatusByClient($estimate, 'approved_client', null, $request->ip(), $request->userAgent());
        } catch (RuntimeException $e) {
            return back()->withErrors(['approval' => $e->getMessage()]);
        }

        return redirect()->route('public.portal', $token)
            ->with('success', '¡Presupuesto aprobado! Tu vehículo pasará a reparación.');
    }

    public function rejectEstimate(Request $request, string $token, Estimate $estimate)
    {
        $vehicle = $this->resolveVehicle($token);
        $this->assertOwnership($vehicle, $estimate);

        $validated = $request->validate([
            'reason' => ['required', 'string', 'max:500'],
        ], ['reason.required' => 'Indica el motivo del rechazo.']);

        try {
            $this->estimateService->changeStatusByClient($estimate, 'rejected_client', $validated['reason'], $request->ip(), $request->userAgent());
        } catch (RuntimeException $e) {
            return back()->withErrors(['approval' => $e->getMessage()]);
        }

        return redirect()->route('public.portal', $token)
            ->with('success', 'Registramos tu observación. El taller revisará el presupuesto.');
    }

    /**
     * Detalle público de una orden de trabajo: vehículo, trabajos (presupuestos)
     * y el control de calidad realizado.
     */
    public function showWorkOrder(string $token, WorkOrder $workOrder): View
    {
        $vehicle = $this->resolveVehicle($token);
        $this->assertOwnership($vehicle, $workOrder);

        $workOrder->load([
            'vehicle.vehicleModel.brand',
            'client',
            'establishment',
            'estimates.items.service.category',
            'estimates.items.part.category',
            'qualityControls.reviewer',
            'satisfactionSurvey',
        ]);

        $latestQc = $workOrder->qualityControls->first();
        $qcTemplate = $latestQc?->template
            ?: FormTemplate::resolveFor($workOrder->establishment_id, FormTemplate::TYPE_QUALITY_CONTROL);

        return view('public.work-order', compact('vehicle', 'workOrder', 'latestQc', 'qcTemplate'));
    }

    /**
     * Formulario público de la encuesta de satisfacción de la OT.
     */
    public function showSurvey(string $token, WorkOrder $workOrder): View
    {
        $vehicle = $this->resolveVehicle($token);
        $this->assertOwnership($vehicle, $workOrder);

        $template = FormTemplate::resolveFor($workOrder->establishment_id, FormTemplate::TYPE_SATISFACTION_SURVEY);

        if (! $template) {
            abort(404, 'No hay una encuesta configurada para este taller.');
        }

        $survey = $workOrder->satisfactionSurvey;

        return view('public.survey', compact('vehicle', 'workOrder', 'template', 'survey'));
    }

    /**
     * Guarda las respuestas de la encuesta de satisfacción (una sola vez).
     */
    public function submitSurvey(SurveyRequest $request, string $token, WorkOrder $workOrder)
    {
        $vehicle = $this->resolveVehicle($token);
        $this->assertOwnership($vehicle, $workOrder);

        $template = FormTemplate::resolveFor($workOrder->establishment_id, FormTemplate::TYPE_SATISFACTION_SURVEY);

        if (! $template) {
            return back()->withErrors(['survey' => 'No hay una encuesta configurada para este taller.']);
        }

        if ($workOrder->satisfactionSurvey()->exists()) {
            return back()->with('success', 'Ya respondiste la encuesta. ¡Gracias por tu opinión!');
        }

        $answerService = app(FormAnswerService::class);
        $validated = $request->validate($answerService->rulesFor($template), $answerService->messagesFor($template));
        $answers = $answerService->normalize($validated['answers'] ?? [], $template);

        $workOrder->satisfactionSurvey()->create([
            'form_template_id' => $template->id,
            'answers' => $answers,
            'ip_address' => $request->ip(),
            'responded_at' => now(),
        ]);

        return redirect()->route('public.work-order.survey', [$token, $workOrder])
            ->with('success', '¡Gracias por tu respuesta! Tu opinión nos ayuda a mejorar.');
    }

    /**
     * Resuelve el vehículo a partir del token del enlace público.
     */
    protected function resolveVehicle(string $token): Vehicle
    {
        $vehicle = Vehicle::query()->where('access_token', $token)->first();

        abort_if(! $vehicle, 404, 'El enlace no es válido.');

        return $vehicle;
    }

    /**
     * El documento debe pertenecer al vehículo del token (si no, 404).
     */
    protected function assertOwnership(Vehicle $vehicle, $document): void
    {
        if ((int) $document->vehicle_id !== (int) $vehicle->id) {
            abort(404);
        }
    }
}
