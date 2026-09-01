<?php

namespace App\Http\Controllers;

use App\Http\Requests\QualityControlRequest;
use App\Http\Requests\WorkOrderRequest;
use App\Jobs\SendWhatsAppMessage;
use App\Models\CheckIn;
use App\Models\CompanySetting;
use App\Models\Estimate;
use App\Models\FormTemplate;
use App\Models\User;
use App\Models\WorkOrder;
use App\Models\WorkOrderAssignment;
use App\Models\WorkOrderInternalExpense;
use App\Models\WorkOrderQualityControl;
use App\Models\WorkOrderSubstage;
use App\Services\EstimateService;
use App\Services\FormAnswerService;
use App\Services\InventoryGuideService;
use App\Services\NotificationService;
use App\Services\WhatsAppService;
use App\Services\WorkOrderService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;
use RuntimeException;

class WorkOrderController extends Controller
{
    protected WorkOrderService $service;

    public function __construct(WorkOrderService $service)
    {
        $this->service = $service;
    }

    /**
     * Salida de repuestos vinculada a la OT: emite una NSA1 (motivo 10,
     * Salida a producción) que descuenta el stock y queda en el kardex.
     */
    public function stockExit(Request $request, WorkOrder $workOrder)
    {
        Gate::authorize('update', $workOrder);

        $data = $request->validate([
            'part_id' => ['required', 'exists:parts,id'],
            'warehouse_id' => ['required', 'exists:warehouses,id'],
            'quantity' => ['required', 'numeric', 'gt:0'],
        ]);

        try {
            $guide = app(InventoryGuideService::class)->createOutput([
                'establishment_id' => $workOrder->establishment_id,
                'movement_reason_code' => '10',
                'origin_warehouse_id' => (int) $data['warehouse_id'],
                'work_order_id' => $workOrder->id,
                'notes' => 'Salida de repuestos para OT '.($workOrder->document_sn ?? ''),
                'items' => [[
                    'part_id' => (int) $data['part_id'],
                    'quantity' => (float) $data['quantity'],
                ]],
            ]);

            return redirect()->route('work-orders.show', $workOrder)
                ->with('success', "Salida registrada: guía {$guide->document_sn} (NSA1, motivo 10).");
        } catch (\InvalidArgumentException $e) {
            return back()->withErrors(['quantity' => $e->getMessage()]);
        }
    }

    /**
     * Reabre una OT cerrada (garantía o siniestro por responsabilidad del taller).
     */
    public function reopen(Request $request, WorkOrder $workOrder)
    {
        Gate::authorize('changeStatus', $workOrder);

        $reason = $request->input('reason') ?: 'Reapertura por garantía o siniestro';

        try {
            $this->service->reopen($workOrder, $reason);

            return redirect()->route('work-orders.show', $workOrder)
                ->with('success', "OT {$workOrder->document_sn} reabierta correctamente.");
        } catch (RuntimeException $e) {
            return back()->withErrors(['reason' => $e->getMessage()]);
        }
    }

    /**
     * Registra un gasto interno asumido por el taller dentro de la OT
     * (arañazo, repuesto malogrado u otro error durante el trabajo).
     */
    public function addInternalExpense(Request $request, WorkOrder $workOrder)
    {
        Gate::authorize('update', $workOrder);

        $data = $request->validate([
            'type' => ['required', 'string', 'in:scratch,damaged_part,other'],
            'description' => ['nullable', 'string', 'max:1000'],
            'amount' => ['required', 'numeric', 'min:0'],
            'currency' => ['nullable', 'string', 'max:3'],
            'exchange_rate' => ['nullable', 'numeric', 'min:0'],
            'responsible_user_id' => ['nullable', 'integer', 'exists:users,id'],
            'occurred_at' => ['nullable', 'date'],
            'notes' => ['nullable', 'string', 'max:1000'],
        ]);

        $this->service->addInternalExpense($workOrder, $data);

        return redirect()->route('work-orders.show', $workOrder)
            ->with('success', 'Gasto interno registrado correctamente (responsabilidad del taller).');
    }

    /**
     * Elimina un gasto interno de la OT.
     */
    public function deleteInternalExpense(WorkOrder $workOrder, WorkOrderInternalExpense $expense)
    {
        Gate::authorize('update', $workOrder);

        try {
            $this->service->removeInternalExpense($workOrder, $expense);
        } catch (RuntimeException $e) {
            return back()->withErrors(['expense' => $e->getMessage()]);
        }

        return redirect()->route('work-orders.show', $workOrder)
            ->with('success', 'Gasto interno eliminado.');
    }

    public function index(): View
    {
        Gate::authorize('viewAny', WorkOrder::class);

        return view('work-orders.index');
    }

    /**
     * Genera la OT desde un inventario (agrupa todos sus presupuestos aprobados)
     * o desde un presupuesto aprobado (agrupa sus hermanos del mismo check-in).
     */
    public function store(WorkOrderRequest $request)
    {
        Gate::authorize('create', WorkOrder::class);

        $validated = $request->validated();

        try {
            if (!empty($validated['check_in_id'])) {
                $checkIn = CheckIn::findOrFail($validated['check_in_id']);
                $estimates = $checkIn->estimates()
                    ->whereIn('status', ['approved_insurance', 'approved_client'])
                    ->whereNull('work_order_id')
                    ->get();

                $workOrder = $this->service->createFromEstimates($estimates, $validated);
            } elseif (!empty($validated['estimate_id'])) {
                $estimate = Estimate::findOrFail($validated['estimate_id']);

                $estimates = collect([$estimate]);
                if ($estimate->check_in_id) {
                    $estimates = Estimate::where('check_in_id', $estimate->check_in_id)
                        ->whereIn('status', ['approved_insurance', 'approved_client'])
                        ->whereNull('work_order_id')
                        ->orderBy('id')
                        ->get();
                }

                $workOrder = $this->service->createFromEstimates($estimates, $validated);
            } else {
                throw new RuntimeException('Seleccione un inventario o un presupuesto para generar la orden de trabajo.');
            }
        } catch (RuntimeException $e) {
            return back()->withErrors(['work_order' => $e->getMessage()]);
        }

        return redirect()->route('work-orders.show', $workOrder)
            ->with('success', "Orden de trabajo {$workOrder->document_sn} generada correctamente.");
    }

    public function show(WorkOrder $workOrder): View
    {
        Gate::authorize('view', $workOrder);

        $workOrder->load([
            'vehicle.vehicleModel.brand',
            'client',
            'establishment',
            'documentSeries.documentType',
            'creator',
            'updater',
            'estimates.items.service.category',
            'estimates.items.part.category',
            'estimates.vehicle.vehicleModel.brand',
            'estimates.thirdPartyOrders',
            'estimates.warrantyOf',
            'estimates.liabilityUser',
            'internalExpenses.responsible',
            'assignments.substage',
            'assignments.user',
            'serviceVouchers.provider',
            'checkIns',
            'qualityControls.reviewer',
            'satisfactionSurvey',
            'deliveredBy',
            'statusHistory.user',
        ]);

        $availableEstimates = Estimate::where('vehicle_id', $workOrder->vehicle_id)
            ->whereIn('status', ['approved_insurance', 'approved_client'])
            ->whereNull('work_order_id')
            ->orderByDesc('id')
            ->get(['id', 'document_sn', 'service_type', 'status', 'total']);

        $substages = WorkOrderSubstage::where('is_active', true)->orderBy('order')->get();
        $technicians = User::role('Técnico')->orderBy('name')->get();

        // Datos para el botón "Enviar por WhatsApp" / "Copiar mensaje" del flujo de entrega.
        $vehicle = $workOrder->vehicle;
        $recipient = $vehicle ? app(EstimateService::class)->resolveRecipient($vehicle) : null;
        $portalBase = $vehicle?->access_token ? url('/c/' . $vehicle->access_token) : '';
        $readyLink = $portalBase . '/work-orders/' . $workOrder->id;
        $surveyLink = $portalBase . '/work-orders/' . $workOrder->id . '/survey';
        $readyMessage = app(NotificationService::class)->buildMessage('work_order_ready', [
            'recipient' => $recipient['contact_name'] ?? 'cliente',
            'plate' => $vehicle?->plate ?? '',
            'sn' => $workOrder->document_sn ?? '',
            'link' => $readyLink,
        ]);
        $surveyMessage = app(NotificationService::class)->buildMessage('work_order_survey', [
            'recipient' => $recipient['contact_name'] ?? 'cliente',
            'plate' => $vehicle?->plate ?? '',
            'link' => $surveyLink,
        ]);
        $recipientsUrl = $vehicle ? route('api.work-orders.recipients', $workOrder) : '';
        $actionUrl = route('work-orders.whatsapp', $workOrder);
        $pendingAssignments = $workOrder->assignments()->whereIn('status', ['pending', 'in_progress'])->count();
        $qcGuardRequired = (bool) (CompanySetting::get()?->qc_require_assignments_completed ?? true);

        // Costos y utilidad de la OT (normalizado a PEN, moneda funcional).
        $costSummary = app(\App\Services\WorkOrderCostService::class)->summary($workOrder);

        return view('work-orders.show', compact(
            'workOrder',
            'availableEstimates',
            'substages',
            'technicians',
            'recipient',
            'readyLink',
            'surveyLink',
            'readyMessage',
            'surveyMessage',
            'recipientsUrl',
            'actionUrl',
            'pendingAssignments',
            'qcGuardRequired',
            'costSummary'
        ));
    }
    /**
     * Anexa un presupuesto aprobado del mismo vehículo a la OT (adicional o
     * reingreso con daños nuevos).
     */
    public function attachEstimate(Request $request, WorkOrder $workOrder)
    {
        Gate::authorize('attachEstimate', $workOrder);

        $validated = $request->validate([
            'estimate_id' => ['required', 'integer', 'exists:estimates,id'],
        ]);

        try {
            $estimate = Estimate::findOrFail($validated['estimate_id']);
            $this->service->attachEstimate($workOrder, $estimate);
        } catch (RuntimeException $e) {
            return back()->withErrors(['work_order' => $e->getMessage()]);
        }

        return back()->with('success', "Presupuesto {$estimate->document_sn} anexado a la orden de trabajo.");
    }

    public function detachEstimate(WorkOrder $workOrder, Estimate $estimate)
    {
        Gate::authorize('attachEstimate', $workOrder);

        try {
            $this->service->detachEstimate($workOrder, $estimate);
        } catch (RuntimeException $e) {
            return back()->withErrors(['work_order' => $e->getMessage()]);
        }

        return back()->with('success', "Presupuesto {$estimate->document_sn} desvinculado de la orden de trabajo.");
    }

    public function transition(Request $request, WorkOrder $workOrder)
    {
        Gate::authorize('changeStatus', $workOrder);

        $validated = $request->validate([
            'status' => ['required', 'string', 'in:' . implode(',', array_keys(WorkOrder::STATUS_LABELS))],
        ]);

        try {
            if ($validated['status'] === 'delivered') {
                $this->service->markDelivered($workOrder);
            } else {
                $this->service->changeStatus($workOrder, $validated['status']);
            }
        } catch (RuntimeException $e) {
            return back()->withErrors(['work_order' => $e->getMessage()]);
        }

        if ($validated['status'] === 'delivered') {
            return redirect(route('work-orders.show', $workOrder) . '?whatsapp=survey')
                ->with('success', 'Vehículo entregado. Envía la encuesta de satisfacción al cliente.');
        }

        return back()->with('success', "Estado actualizado: {$workOrder->status_label}.");
    }

    /**
     * Formulario de control de calidad (solo disponible en estado quality_control).
     */
    public function qualityControl(WorkOrder $workOrder): View
    {
        Gate::authorize('changeStatus', $workOrder);

        $workOrder->load([
            'vehicle.vehicleModel.brand',
            'client',
            'establishment',
            'assignments.substage',
            'assignments.user',
            'qualityControls.reviewer',
        ]);

        if ($workOrder->status !== 'quality_control') {
            return redirect()->route('work-orders.show', $workOrder)
                ->with('success', 'La orden de trabajo no está en control de calidad.');
        }

        $template = $this->service->resolveTemplateFor($workOrder, FormTemplate::TYPE_QUALITY_CONTROL);

        if (! $template) {
            abort(422, 'No hay una plantilla de control de calidad configurada.');
        }

        $pendingAssignments = $workOrder->assignments->whereIn('status', ['pending', 'in_progress'])->count();
        $qcGuardRequired = (bool) (CompanySetting::get()?->qc_require_assignments_completed ?? true);
        $rejectionReasons = WorkOrderQualityControl::REJECTION_REASONS;

        return view('work-orders.quality-control', compact('workOrder', 'template', 'pendingAssignments', 'qcGuardRequired', 'rejectionReasons'));
    }

    /**
     * Guarda la revisión de control de calidad y aplica la transición.
     */
    public function submitQualityControl(QualityControlRequest $request, WorkOrder $workOrder)
    {
        Gate::authorize('changeStatus', $workOrder);

        $template = $this->service->resolveTemplateFor($workOrder, FormTemplate::TYPE_QUALITY_CONTROL);

        if (! $template) {
            return back()->withErrors(['work_order' => 'No hay una plantilla de control de calidad configurada.']);
        }

        $validated = $request->validated();

        // Validación dinámica de las respuestas del formulario según la plantilla.
        $answerService = app(FormAnswerService::class);
        $validatedAnswers = $request->validate($answerService->rulesFor($template), $answerService->messagesFor($template));
        $validated['answers'] = $answerService->normalize($validatedAnswers['answers'] ?? [], $template);
        $validated['form_template_id'] = $template->id;

        try {
            $workOrder = $this->service->submitQualityControl($workOrder, $validated);
        } catch (RuntimeException $e) {
            return back()->withErrors(['work_order' => $e->getMessage()]);
        }

        if ($validated['result'] === WorkOrderQualityControl::RESULT_APPROVED) {
            return redirect(route('work-orders.show', $workOrder) . '?whatsapp=ready')
                ->with('success', 'Control de calidad aprobado. La OT pasó a "Lista para entrega". Avisa al cliente.');
        }

        return back()->with('success', 'Control de calidad rechazado. La OT volvió a reparación.');
    }

    /**
     * Envía el mensaje de WhatsApp (listo para recoger / encuesta de satisfacción)
     * por API Evolution o abriendo WhatsApp Web (wa.me).
     */
    public function whatsapp(Request $request, WorkOrder $workOrder)
    {
        Gate::authorize('changeStatus', $workOrder);

        $validated = $request->validate([
            'recipient_name' => ['nullable', 'string', 'max:255'],
            'phone' => ['required', 'string', 'max:30'],
            'message' => ['required', 'string', 'max:5000'],
            'send_method' => ['required', 'in:wa_me,api'],
        ]);

        $updates = [
            'last_sent_to' => $validated['recipient_name'] ?: null,
            'last_sent_to_phone' => $validated['phone'],
            'last_sent_at' => now(),
        ];

        // Si la OT ya está entregada, el mensaje corresponde a la encuesta de satisfacción.
        if ($workOrder->status === 'delivered') {
            $updates['survey_sent_at'] = now();
            $updates['survey_sent_to'] = $validated['recipient_name'] ?: null;
            $updates['survey_sent_to_phone'] = $validated['phone'];
        }

        $workOrder->update($updates);

        $establishment = $workOrder->establishment ?? auth()->user()?->establishment;

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

        return redirect()->away($waLink)->with('success', 'Enlace de WhatsApp abierto para su envío.');
    }

    /**
     * Destinatarios del vehículo de la OT para el modal de WhatsApp.
     */
    public function recipients(Request $request, WorkOrder $workOrder): JsonResponse
    {
        Gate::authorize('view', $workOrder);

        return response()->json(app(EstimateService::class)->resolveRecipients($workOrder->vehicle));
    }

    public function addAssignment(Request $request, WorkOrder $workOrder)
    {
        Gate::authorize('manageAssignments', $workOrder);

        $validated = $request->validate([
            'substage_id' => ['required', 'integer', 'exists:work_order_substages,id'],
            'user_id' => ['nullable', 'integer', 'exists:users,id'],
            'hours' => ['nullable', 'numeric', 'min:0', 'max:999'],
            'cost' => ['nullable', 'numeric', 'min:0'],
            'notes' => ['nullable', 'string', 'max:500'],
        ]);

        $this->service->addAssignment($workOrder, $validated);

        return back()->with('success', 'Asignación registrada.');
    }

    public function updateAssignmentStatus(Request $request, WorkOrder $workOrder, WorkOrderAssignment $assignment)
    {
        Gate::authorize('manageAssignments', $workOrder);

        $validated = $request->validate([
            'status' => ['required', 'string', 'in:pending,in_progress,done'],
        ]);

        try {
            $this->service->updateAssignmentStatus($workOrder, $assignment, $validated['status']);
        } catch (RuntimeException $e) {
            return back()->withErrors(['work_order' => $e->getMessage()]);
        }

        return back()->with('success', 'Estado de la asignación actualizado.');
    }

    public function deleteAssignment(WorkOrder $workOrder, WorkOrderAssignment $assignment)
    {
        Gate::authorize('manageAssignments', $workOrder);

        $this->service->deleteAssignment($workOrder, $assignment);

        return back()->with('success', 'Asignación eliminada.');
    }

    public function destroy(WorkOrder $workOrder)
    {
        Gate::authorize('delete', $workOrder);

        $sn = $workOrder->document_sn;

        try {
            $this->service->delete($workOrder);
        } catch (RuntimeException $e) {
            return back()->withErrors(['work_order' => $e->getMessage()]);
        }

        return redirect()->route('work-orders.index')
            ->with('success', "Orden de trabajo {$sn} eliminada. Los presupuestos volvieron a estado aprobado.");
    }
    public function search(Request $request): JsonResponse
    {
        Gate::authorize('viewAny', WorkOrder::class);

        $query = WorkOrder::query()
            ->withCount('estimates')
            ->withSum('estimates', 'total')
            ->with([
                'vehicle.vehicleModel.brand',
                'client',
                'establishment',
                'documentSeries.documentType',
            ])
            ->when($request->filled('q'), function ($q) use ($request) {
                $term = $request->query('q');
                $q->where(function ($sub) use ($term) {
                    $sub->whereHas('vehicle', fn ($v) => $v->where('plate', 'like', "%{$term}%"))
                        ->orWhereHas('vehicle.vehicleModel.brand', fn ($b) => $b->where('name', 'like', "%{$term}%"))
                        ->orWhereHas('client', fn ($c) => $c
                            ->where('business_name', 'like', "%{$term}%")
                            ->orWhere('first_name', 'like', "%{$term}%")
                            ->orWhere('last_name', 'like', "%{$term}%"))
                        ->orWhere('document_sn', 'like', "%{$term}%")
                        ->orWhere('document_serie', 'like', "%{$term}%")
                        ->orWhere('document_type_code', 'like', "%{$term}%")
                        ->orWhereRaw('CAST(document_number AS CHAR) LIKE ?', ["%{$term}%"]);
                });
            })
            ->when($request->filled('status'), function ($q) use ($request) {
                $q->where('status', $request->query('status'));
            })
            ->when($request->filled('date_from'), function ($q) use ($request) {
                $q->whereDate('created_at', '>=', $request->query('date_from'));
            })
            ->when($request->filled('date_to'), function ($q) use ($request) {
                $q->whereDate('created_at', '<=', $request->query('date_to'));
            })
            ->orderByDesc('id');

        $limit = $request->integer('limit', 100);
        $data = $query->limit($limit)->get()->map(function (WorkOrder $workOrder) {
            return [
                'id' => $workOrder->id,
                'plate' => $workOrder->vehicle?->plate,
                'vehicle_brand' => $workOrder->vehicle?->vehicleModel?->brand?->name,
                'vehicle_model' => $workOrder->vehicle?->vehicleModel?->name,
                'client_name' => $workOrder->client?->display_name,
                'document_type_code' => $workOrder->document_type_code,
                'document_serie' => $workOrder->document_serie,
                'document_number' => $workOrder->document_number,
                'document_sn' => $workOrder->document_sn,
                'document_type_name' => $workOrder->documentSeries?->documentType?->name,
                'estimates_count' => (int) ($workOrder->estimates_count ?? 0),
                'total' => (float) ($workOrder->estimates_sum_total ?? 0),
                'status' => $workOrder->status,
                'status_label' => $workOrder->status_label,
                'next_action' => $workOrder->next_action,
                'start_date' => $workOrder->start_date?->format('d/m/Y'),
                'estimated_end_date' => $workOrder->estimated_end_date?->format('d/m/Y'),
                'establishment' => $workOrder->establishment?->name,
                'text' => sprintf(
                    '%s · %s · %s · %d presupuesto(s) · S/ %s',
                    $workOrder->document_sn,
                    $workOrder->vehicle?->plate ?? 'sin placa',
                    $workOrder->client?->display_name ?? '—',
                    (int) ($workOrder->estimates_count ?? 0),
                    number_format((float) ($workOrder->estimates_sum_total ?? 0), 2)
                ),
            ];
        });

        return response()->json($data);
    }

    /**
     * OTs elegibles para reingreso (status 'delivered_pending') de un vehículo.
     * También permite resolver una OT puntual por work_order_id para precargar
     * el formulario desde el botón "Registrar reingreso" de la vista de la OT.
     */
    public function reentryOptions(Request $request): JsonResponse
    {
        Gate::authorize('viewAny', WorkOrder::class);

        $query = WorkOrder::with(['vehicle.vehicleModel.brand', 'client'])
            ->where('status', 'delivered_pending');

        if ($request->filled('vehicle_id')) {
            $query->where('vehicle_id', (int) $request->query('vehicle_id'));
        } elseif ($request->filled('work_order_id')) {
            $query->where('id', (int) $request->query('work_order_id'));
        } else {
            return response()->json([]);
        }

        return response()->json($query->orderByDesc('id')->limit(20)->get()->map(fn (WorkOrder $workOrder) => [
            'id' => $workOrder->id,
            'document_sn' => $workOrder->document_sn,
            'plate' => $workOrder->vehicle?->plate,
            'vehicle_id' => $workOrder->vehicle_id,
            'client_name' => $workOrder->client?->display_name,
            'status' => $workOrder->status,
            'status_label' => $workOrder->status_label,
            'text' => sprintf(
                '%s · %s · %s',
                $workOrder->document_sn,
                $workOrder->vehicle?->plate ?? 'sin placa',
                $workOrder->client?->display_name ?? '—'
            ),
        ]));
    }
}
