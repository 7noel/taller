<?php

namespace App\Services;

use App\Models\CheckIn;
use App\Models\CompanySetting;
use App\Models\Estimate;
use App\Models\FormTemplate;
use App\Models\WorkOrder;
use App\Models\WorkOrderAssignment;
use App\Models\WorkOrderQualityControl;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use RuntimeException;

class WorkOrderService
{
    /**
     * Transiciones válidas del flujo de la OT.
     *
     * 'delivered_pending' = el vehículo salió con trabajos pendientes (backorder).
     * La OT NO se cierra: queda viva hasta que todo el alcance esté completo.
     */
    public const TRANSITIONS = [
        'open' => ['in_progress', 'waiting_parts', 'closed'],
        'in_progress' => ['waiting_parts', 'quality_control', 'delivered_pending'],
        'waiting_parts' => ['in_progress', 'quality_control', 'delivered_pending'],
        'quality_control' => ['ready_for_delivery', 'in_progress'],
        'ready_for_delivery' => ['delivered', 'delivered_pending', 'in_progress'],
        'delivered' => ['closed'],
        'delivered_pending' => ['in_progress', 'waiting_parts', 'delivered'],
        'closed' => [],
    ];

    /**
     * Crea una OT a partir de uno o más presupuestos aprobados.
     *
     * Agrupa todos los presupuestos aprobados (inicial + adicionales) del mismo
     * vehículo/check-in en una sola OT, los marca como 'in_repair' y vincula los
     * check-ins (visitas) correspondientes a la OT.
     */
    public function createFromEstimates(Collection $estimates, array $data = []): WorkOrder
    {
        if ($estimates->isEmpty()) {
            throw new RuntimeException('No hay presupuestos aprobados para generar la orden de trabajo.');
        }

        $vehicleId = $estimates->first()->vehicle_id;
        $clientId = $estimates->first()->client_id;
        $establishmentId = $data['establishment_id']
            ?? $estimates->first()->establishment_id
            ?? Auth::user()?->establishment_id;

        foreach ($estimates as $estimate) {
            if (!in_array($estimate->status, ['approved_insurance', 'approved_client'], true)) {
                throw new RuntimeException("El presupuesto {$estimate->document_sn} no está aprobado.");
            }
            if ($estimate->work_order_id) {
                throw new RuntimeException("El presupuesto {$estimate->document_sn} ya pertenece a una orden de trabajo.");
            }
            if ((int) $estimate->vehicle_id !== (int) $vehicleId) {
                throw new RuntimeException('Los presupuestos deben pertenecer al mismo vehículo.');
            }
        }

        $data = array_merge([
            'vehicle_id' => $vehicleId,
            'client_id' => $clientId,
            'establishment_id' => $establishmentId,
            'status' => 'open',
            'created_by' => Auth::id(),
            'updated_by' => Auth::id(),
        ], $data);

        $workOrder = DB::transaction(function () use ($estimates, $data) {
            $this->assignDocumentNumber((int) $data['establishment_id'], $data);

            $workOrder = WorkOrder::create($data);
            $workOrder->recordStatusChange('open', null, 'OT generada desde presupuesto(s) aprobado(s).', 'system');

            foreach ($estimates as $estimate) {
                $this->markEstimateInRepair($estimate, $workOrder, 'OT generada: ' . $workOrder->document_sn);
            }

            $this->linkCheckInsToWorkOrder($estimates, $workOrder);

            return $workOrder;
        });

        return $workOrder->fresh($this->defaultRelations());
    }
    /**
     * Anexa un presupuesto aprobado a una OT existente.
     *
     * Cubre el caso de un adicional aprobado durante la reparación o un presupuesto
     * nuevo aprobado al reingresar el vehículo para completar trabajos pendientes.
     */
    public function attachEstimate(WorkOrder $workOrder, Estimate $estimate): WorkOrder
    {
        if (!in_array($estimate->status, ['approved_insurance', 'approved_client'], true)) {
            throw new RuntimeException("El presupuesto {$estimate->document_sn} no está aprobado.");
        }
        if ($estimate->work_order_id && (int) $estimate->work_order_id !== (int) $workOrder->id) {
            throw new RuntimeException("El presupuesto {$estimate->document_sn} ya pertenece a otra orden de trabajo.");
        }
        if ((int) $estimate->vehicle_id !== (int) $workOrder->vehicle_id) {
            throw new RuntimeException('El presupuesto pertenece a otro vehículo.');
        }

        DB::transaction(function () use ($workOrder, $estimate) {
            $this->markEstimateInRepair($estimate, $workOrder, 'Presupuesto anexado a la OT ' . $workOrder->document_sn);

            if ($estimate->check_in_id) {
                CheckIn::where('id', $estimate->check_in_id)
                    ->whereNull('work_order_id')
                    ->update(['work_order_id' => $workOrder->id, 'updated_by' => Auth::id()]);
            }
        });

        return $workOrder->fresh($this->defaultRelations());
    }

    /**
     * Desvincula un presupuesto de la OT (revertiendo a un estado aprobado).
     */
    public function detachEstimate(WorkOrder $workOrder, Estimate $estimate): WorkOrder
    {
        if ((int) $estimate->work_order_id !== (int) $workOrder->id) {
            throw new RuntimeException('El presupuesto no pertenece a esta orden de trabajo.');
        }

        DB::transaction(function () use ($estimate, $workOrder) {
            $previous = $estimate->statusHistory()
                ->where('to_status', 'in_repair')
                ->orderByDesc('id')
                ->first();

            $estimate->update([
                'work_order_id' => null,
                'status' => $previous?->from_status ?: 'approved_client',
                'updated_by' => Auth::id(),
            ]);

            $estimate->recordStatusChange($estimate->status, 'in_repair', 'Desvinculado de la OT ' . $workOrder->document_sn, 'system');
        });

        return $workOrder->fresh($this->defaultRelations());
    }

    /**
     * Cambia el estado de la OT validando la transición.
     */
    public function changeStatus(WorkOrder $workOrder, string $newStatus): WorkOrder
    {
        $from = $workOrder->status;
        $allowed = self::TRANSITIONS[$from] ?? [];

        if (!in_array($newStatus, $allowed, true)) {
            throw new RuntimeException("Transición de estado inválida: {$from} → {$newStatus}.");
        }

        $workOrder->update([
            'status' => $newStatus,
            'updated_by' => Auth::id(),
        ]);

        $workOrder->recordStatusChange($newStatus, $from);

        return $workOrder->fresh($this->defaultRelations());
    }

    /**
     * Registra una revisión de control de calidad y aplica la transición:
     * aprobado => ready_for_delivery (listo para entrega); rechazado => in_progress
     * (vuelve a reparación para reenviarla luego a control de calidad).
     *
     * Guard configurable en company_settings.qc_require_assignments_completed:
     * si es true (por defecto) y hay asignaciones sin terminar, BLOQUEA la
     * aprobación; si es false, solo se advierte en la UI.
     */
    public function submitQualityControl(WorkOrder $workOrder, array $data): WorkOrder
    {
        if ($workOrder->status !== 'quality_control') {
            throw new RuntimeException('La orden de trabajo no está en control de calidad.');
        }

        $result = $data['result'] ?? null;
        if (! in_array($result, [WorkOrderQualityControl::RESULT_APPROVED, WorkOrderQualityControl::RESULT_REJECTED], true)) {
            throw new RuntimeException('Resultado de control de calidad inválido.');
        }

        if ($result === WorkOrderQualityControl::RESULT_REJECTED && empty($data['rejection_reason'])) {
            throw new RuntimeException('Debe indicar la causa del rechazo del control de calidad.');
        }

        if ($result === WorkOrderQualityControl::RESULT_APPROVED) {
            $this->assertAssignmentsCompletedForQc($workOrder);
        }

        DB::transaction(function () use ($workOrder, $data, $result) {
            WorkOrderQualityControl::create([
                'work_order_id' => $workOrder->id,
                'form_template_id' => $data['form_template_id'] ?? null,
                'result' => $result,
                'rejection_reason' => $result === WorkOrderQualityControl::RESULT_REJECTED ? ($data['rejection_reason'] ?? null) : null,
                'rejection_details' => $result === WorkOrderQualityControl::RESULT_REJECTED ? ($data['rejection_details'] ?? null) : null,
                'answers' => $data['answers'] ?? [],
                'reviewed_by' => Auth::id(),
                'reviewed_at' => now(),
                'created_by' => Auth::id(),
                'updated_by' => Auth::id(),
            ]);

            $this->changeStatus(
                $workOrder,
                $result === WorkOrderQualityControl::RESULT_APPROVED ? 'ready_for_delivery' : 'in_progress'
            );
        });

        return $workOrder->fresh($this->defaultRelations());
    }

    /**
     * Marca la OT como entregada (el cliente recogió el vehículo) registrando
     * fecha y usuario que confirmó la entrega.
     */
    public function markDelivered(WorkOrder $workOrder): WorkOrder
    {
        DB::transaction(function () use ($workOrder) {
            $this->changeStatus($workOrder, 'delivered');

            $workOrder->update([
                'delivered_at' => $workOrder->delivered_at ?? now(),
                'delivered_by' => $workOrder->delivered_by ?? Auth::id(),
            ]);
        });

        return $workOrder->fresh($this->defaultRelations());
    }

    /**
     * Plantilla de formulario vigente para la OT (del establecimiento o global).
     */
    public function resolveTemplateFor(WorkOrder $workOrder, string $type): ?FormTemplate
    {
        return FormTemplate::resolveFor($workOrder->establishment_id, $type);
    }

    /**
     * Valida el guard de aprobación de QC según la configuración de la empresa.
     */
    protected function assertAssignmentsCompletedForQc(WorkOrder $workOrder): void
    {
        $requireCompleted = (bool) (CompanySetting::get()?->qc_require_assignments_completed ?? true);
        $pending = $workOrder->assignments()
            ->whereIn('status', ['pending', 'in_progress'])
            ->count();

        if ($requireCompleted && $pending > 0) {
            throw new RuntimeException(
                "No se puede aprobar el control de calidad: hay {$pending} asignación(es) pendiente(s) o en progreso. Complétalas antes de aprobar."
            );
        }
    }

    /**
     * Elimina (soft delete) la OT: revierte los presupuestos a un estado aprobado
     * y desvincula los check-ins para no dejar documentos huérfanos en 'in_repair'.
     */
    public function delete(WorkOrder $workOrder): bool
    {
        return DB::transaction(function () use ($workOrder) {
            foreach ($workOrder->estimates()->get() as $estimate) {
                $this->revertEstimateToApproved($estimate, 'OT eliminada: ' . $workOrder->document_sn);
            }

            CheckIn::where('work_order_id', $workOrder->id)
                ->update(['work_order_id' => null, 'updated_by' => Auth::id()]);

            $workOrder->assignments()->delete();

            return (bool) $workOrder->delete();
        });
    }
    /**
     * Registra una asignación de técnico a una subetapa de la OT.
     */
    public function addAssignment(WorkOrder $workOrder, array $data): WorkOrderAssignment
    {
        return WorkOrderAssignment::create([
            'work_order_id' => $workOrder->id,
            'substage_id' => $data['substage_id'],
            'user_id' => $data['user_id'] ?? null,
            'hours' => $data['hours'] ?? 0,
            'cost' => $data['cost'] ?? 0,
            'status' => $data['status'] ?? 'pending',
            'notes' => $data['notes'] ?? null,
            'created_by' => Auth::id(),
            'updated_by' => Auth::id(),
        ]);
    }

    /**
     * Actualiza una asignación (diff/upsert por id).
     */
    public function updateAssignment(WorkOrder $workOrder, WorkOrderAssignment $assignment, array $data): WorkOrderAssignment
    {
        if ((int) $assignment->work_order_id !== (int) $workOrder->id) {
            throw new RuntimeException('La asignación no pertenece a esta orden de trabajo.');
        }

        $assignment->update([
            'substage_id' => $data['substage_id'] ?? $assignment->substage_id,
            'user_id' => $data['user_id'] ?? $assignment->user_id,
            'hours' => $data['hours'] ?? $assignment->hours,
            'cost' => $data['cost'] ?? $assignment->cost,
            'status' => $data['status'] ?? $assignment->status,
            'notes' => array_key_exists('notes', $data) ? $data['notes'] : $assignment->notes,
            'updated_by' => Auth::id(),
        ]);

        return $assignment->fresh();
    }

    /**
     * Cambia el estado de una asignación (pending → in_progress → done).
     */
    public function updateAssignmentStatus(WorkOrder $workOrder, WorkOrderAssignment $assignment, string $status): WorkOrderAssignment
    {
        if ((int) $assignment->work_order_id !== (int) $workOrder->id) {
            throw new RuntimeException('La asignación no pertenece a esta orden de trabajo.');
        }

        $allowed = WorkOrderAssignment::TRANSITIONS[$assignment->status] ?? [];
        if (!in_array($status, $allowed, true)) {
            throw new RuntimeException("Transición inválida para la asignación: {$assignment->status} → {$status}.");
        }

        return $this->updateAssignment($workOrder, $assignment, ['status' => $status]);
    }

    /**
     * Elimina una asignación.
     */
    public function deleteAssignment(WorkOrder $workOrder, WorkOrderAssignment $assignment): bool
    {
        if ((int) $assignment->work_order_id !== (int) $workOrder->id) {
            throw new RuntimeException('La asignación no pertenece a esta orden de trabajo.');
        }

        return (bool) $assignment->delete();
    }
    /**
     * Asigna la serie OT01 y el siguiente número de documento a la OT.
     */
    protected function assignDocumentNumber(int $establishmentId, array &$data): void
    {
        $result = app(DocumentSeriesService::class)->getNextNumber($establishmentId, 'OT');

        $data['document_series_id'] = $result['series']->id;

        if ($result['number'] === null) {
            throw new RuntimeException('La serie OT01 usa numeración por API. Configure la numeración local o asigne el número manualmente.');
        }

        $data['document_type_code'] = $result['document_type_code'];
        $data['document_serie'] = $result['series']->prefix_serie;
        $data['document_number'] = $result['number'];
        $data['document_sn'] = $result['sn'];
    }

    /**
     * Marca un presupuesto como 'in_repair' y registra el cambio en su historial.
     */
    protected function markEstimateInRepair(Estimate $estimate, WorkOrder $workOrder, string $comment): void
    {
        $from = $estimate->status;

        $estimate->update([
            'work_order_id' => $workOrder->id,
            'status' => 'in_repair',
            'updated_by' => Auth::id(),
        ]);

        $estimate->recordStatusChange('in_repair', $from, $comment, 'system');
    }

    /**
     * Revierte un presupuesto 'in_repair' a su estado aprobado previo y lo
     * desvincula de la OT.
     */
    protected function revertEstimateToApproved(Estimate $estimate, string $comment): void
    {
        $previous = $estimate->statusHistory()
            ->where('to_status', 'in_repair')
            ->orderByDesc('id')
            ->first();

        $estimate->update([
            'work_order_id' => null,
            'status' => $previous?->from_status ?: 'approved_client',
            'updated_by' => Auth::id(),
        ]);

        $estimate->recordStatusChange($estimate->status, 'in_repair', $comment, 'system');
    }

    /**
     * Vincula los check-ins (visitas) de los presupuestos a la OT.
     */
    protected function linkCheckInsToWorkOrder(Collection $estimates, WorkOrder $workOrder): void
    {
        $checkInIds = $estimates->pluck('check_in_id')->filter()->unique()->values();

        if ($checkInIds->isEmpty()) {
            return;
        }

        CheckIn::whereIn('id', $checkInIds)
            ->whereNull('work_order_id')
            ->update(['work_order_id' => $workOrder->id, 'updated_by' => Auth::id()]);
    }

    protected function defaultRelations(): array
    {
        return [
            'vehicle.vehicleModel.brand',
            'client',
            'establishment',
            'documentSeries.documentType',
            'creator',
            'updater',
            'estimates',
            'assignments.substage',
            'assignments.user',
            'checkIns',
        ];
    }
}
