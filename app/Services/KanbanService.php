<?php

namespace App\Services;

use App\Models\CheckIn;
use App\Models\Estimate;
use App\Models\User;
use App\Models\WorkOrder;
use Illuminate\Support\Collection;

/**
 * Agrega los datos del tablero Kanban del taller.
 *
 * El tablero refleja el flujo completo: Inventario → Presupuesto → Aprobación →
 * Reparación → Control de Calidad → Entrega. Cada tarjeta representa una
 * entidad activa (check-in, presupuesto u orden de trabajo) con su última
 * acción (status_histories), la próxima acción sugerida y las transiciones
 * permitidas para el usuario según sus permisos.
 */
class KanbanService
{
    /** Columnas del tablero en orden de flujo. */
    public const COLUMNS = ['inventario', 'presupuesto', 'aprobacion', 'reparacion', 'control_calidad', 'entrega'];

    public const COLUMN_TITLES = [
        'inventario' => 'Inventario',
        'presupuesto' => 'Presupuesto',
        'aprobacion' => 'Aprobación',
        'reparacion' => 'Reparación',
        'control_calidad' => 'Control de Calidad',
        'entrega' => 'Entrega',
    ];

    public const COLUMN_COLORS = [
        'inventario' => 'bg-blue-50 text-blue-700',
        'presupuesto' => 'bg-amber-50 text-amber-700',
        'aprobacion' => 'bg-violet-50 text-violet-700',
        'reparacion' => 'bg-sky-50 text-sky-700',
        'control_calidad' => 'bg-purple-50 text-purple-700',
        'entrega' => 'bg-green-50 text-green-700',
    ];

    public function board(User $user): array
    {
        $cards = collect()
            ->concat($this->checkInCards($user))
            ->concat($this->estimateCards($user))
            ->concat($this->workOrderCards($user))
            ->sortByDesc('sort_at')
            ->values();

        $grouped = $cards->groupBy('column');

        $columns = [];
        foreach (self::COLUMNS as $key) {
            $items = $grouped->get($key, collect())->values();
            $columns[] = [
                'key' => $key,
                'title' => self::COLUMN_TITLES[$key],
                'color' => self::COLUMN_COLORS[$key],
                'count' => $items->count(),
                'cards' => $items,
            ];
        }

        return [
            'columns' => $columns,
            'total' => $cards->count(),
            'per_column' => collect(self::COLUMNS)->mapWithKeys(
                fn (string $k) => [$k => $grouped->get($k, collect())->count()]
            ),
        ];
    }

    protected function checkInCards(User $user): Collection
    {
        return CheckIn::with(['vehicle.vehicleModel.brand', 'client', 'statusHistory.user'])
            ->whereIn('status', ['draft', 'pending_approval', 'approved', 'rejected'])
            ->get()
            ->map(fn (CheckIn $checkIn) => $this->baseCard($checkIn, 'check_in', 'inventario', $user) + [
                'actions' => $this->checkInActions($checkIn, $user),
            ]);
    }

    protected function estimateCards(User $user): Collection
    {
        return Estimate::with(['vehicle.vehicleModel.brand', 'client', 'statusHistory.user'])
            ->whereIn('status', [
                'draft', 'sent_insurance', 'approved_insurance', 'rejected_insurance',
                'sent_client', 'approved_client', 'rejected_client',
            ])
            ->whereNull('work_order_id')
            ->get()
            ->map(fn (Estimate $estimate) => $this->baseCard($estimate, 'estimate', $this->estimateColumn($estimate->status), $user) + [
                'actions' => $this->estimateActions($estimate, $user),
            ]);
    }

    protected function workOrderCards(User $user): Collection
    {
        return WorkOrder::with(['vehicle.vehicleModel.brand', 'client', 'statusHistory.user'])
            ->whereIn('status', [
                'open', 'in_progress', 'waiting_parts', 'quality_control',
                'ready_for_delivery', 'delivered', 'delivered_pending',
            ])
            ->get()
            ->map(fn (WorkOrder $workOrder) => $this->baseCard($workOrder, 'work_order', $this->workOrderColumn($workOrder->status), $user) + [
                'actions' => $this->workOrderActions($workOrder, $user),
            ]);
    }

    protected function estimateColumn(string $status): string
    {
        return in_array($status, ['sent_insurance', 'approved_insurance', 'sent_client', 'approved_client'], true)
            ? 'aprobacion'
            : 'presupuesto';
    }

    protected function workOrderColumn(string $status): string
    {
        return match ($status) {
            'quality_control' => 'control_calidad',
            'ready_for_delivery', 'delivered', 'delivered_pending' => 'entrega',
            default => 'reparacion',
        };
    }

    /**
     * Estructura común de la tarjeta: identidad, vehículo, cliente, estado,
     * última acción (status_histories), próxima acción y enlaces.
     */
    protected function baseCard($entity, string $type, string $column, User $user): array
    {
        $last = $entity->statusHistory->last();
        $labels = $entity::STATUS_LABELS;

        return [
            'type' => $type,
            'id' => $entity->id,
            'column' => $column,
            'document_sn' => $entity->document_sn,
            'plate' => strtoupper($entity->vehicle?->plate ?? ''),
            'vehicle_label' => $this->vehicleLabel($entity->vehicle),
            'client' => $entity->client?->display_name,
            'status' => $entity->status,
            'status_label' => $entity->status_label,
            'next_action' => $entity::NEXT_ACTIONS[$entity->status] ?? null,
            'last_action' => $last ? [
                'text' => trim(
                    ($labels[$last->from_status] ?? $last->from_status) . ' → ' . ($labels[$last->to_status] ?? $last->to_status)
                ),
                'by' => $last->user?->name
                    ?? ($last->actor_type === 'client' ? 'Cliente' : ($last->actor_type === 'system' ? 'Sistema' : null)),
                'at' => $last->created_at?->toDateTimeString(),
                'human' => $last->created_at?->diffForHumans(),
            ] : null,
            'sort_at' => ($entity->updated_at ?? $entity->created_at)?->toDateTimeString(),
            'show_url' => route($this->showRoute($type), $entity),
            'edit_url' => $this->canEdit($type, $entity, $user) ? route($this->editRoute($type), $entity) : null,
        ];
    }

    protected function vehicleLabel($vehicle): string
    {
        if (! $vehicle) {
            return '';
        }

        return implode(' · ', array_filter([
            $vehicle->vehicleModel?->brand?->name,
            $vehicle->vehicleModel?->name,
            $vehicle->color,
            $vehicle->year,
        ]));
    }

    protected function showRoute(string $type): string
    {
        return match ($type) {
            'check_in' => 'check-ins.show',
            'estimate' => 'estimates.show',
            default => 'work-orders.show',
        };
    }

    protected function editRoute(string $type): string
    {
        return match ($type) {
            'check_in' => 'check-ins.edit',
            'estimate' => 'estimates.edit',
            default => 'work-orders.show', // las OT no tienen edición
        };
    }

    protected function canEdit(string $type, $entity, User $user): bool
    {
        return match ($type) {
            'check_in' => $user->can('editar inventarios'),
            'estimate' => $user->can('editar presupuestos'),
            default => false,
        };
    }

    /** Acciones contextuales de un check-in según su estado y permisos. */
    protected function checkInActions(CheckIn $checkIn, User $user): array
    {
        $actions = [['label' => 'Ver', 'method' => 'GET', 'url' => route('check-ins.show', $checkIn)]];

        if ($user->can('editar inventarios')) {
            $actions[] = ['label' => 'Editar', 'method' => 'GET', 'url' => route('check-ins.edit', $checkIn)];
        }

        if (in_array($checkIn->status, ['draft', 'rejected'], true)) {
            if ($user->can('editar inventarios')) {
                $actions[] = ['label' => 'Enviar al cliente', 'method' => 'POST', 'url' => route('check-ins.send-to-client', $checkIn)];
            }
        } elseif ($checkIn->status === 'pending_approval') {
            if ($user->can('aprobar inventarios')) {
                $actions[] = ['label' => 'Aprobar', 'method' => 'POST', 'url' => route('check-ins.approve', $checkIn), 'confirm' => '¿Confirmas la aprobación de este inventario?'];
                $actions[] = ['label' => 'Rechazar', 'method' => 'POST', 'url' => route('check-ins.reject', $checkIn), 'reason' => true];
            }
        } elseif ($checkIn->status === 'approved') {
            if ($user->can('crear presupuestos')) {
                $actions[] = ['label' => 'Crear presupuesto', 'method' => 'GET', 'url' => route('estimates.create', ['check_in_id' => $checkIn->id])];
            }
        }

        return $actions;
    }

    /** Acciones contextuales de un presupuesto según su estado y permisos. */
    protected function estimateActions(Estimate $estimate, User $user): array
    {
        $actions = [['label' => 'Ver', 'method' => 'GET', 'url' => route('estimates.show', $estimate)]];
        $canEdit = $user->can('editar presupuestos');
        $canApprove = $user->can('aprobar presupuestos');
        $canCreateWo = $user->can('crear órdenes de trabajo');

        if ($canEdit) {
            $actions[] = ['label' => 'Editar', 'method' => 'GET', 'url' => route('estimates.edit', $estimate)];
        }

        switch ($estimate->status) {
            case 'draft':
                if ($canEdit) {
                    $actions[] = ['label' => 'Enviar al seguro', 'method' => 'POST', 'url' => route('estimates.send-to-insurance', $estimate)];
                    $actions[] = ['label' => 'Enviar al cliente', 'method' => 'POST', 'url' => route('estimates.send-to-client', $estimate)];
                }
                break;

            case 'sent_insurance':
                if ($canApprove) {
                    $actions[] = ['label' => 'Aprobar seguro', 'method' => 'POST', 'url' => route('estimates.approve-insurance', $estimate), 'confirm' => '¿Confirmas la aprobación del seguro?'];
                    $actions[] = ['label' => 'Rechazar seguro', 'method' => 'POST', 'url' => route('estimates.reject-insurance', $estimate), 'reason' => true, 'reason_required' => true];
                }
                break;

            case 'approved_insurance':
                if ($canEdit) {
                    $actions[] = ['label' => 'Enviar al cliente', 'method' => 'POST', 'url' => route('estimates.send-to-client', $estimate)];
                }
                if ($canCreateWo) {
                    $actions[] = $this->generateWorkOrderAction($estimate);
                }
                break;

            case 'sent_client':
                if ($canApprove) {
                    $actions[] = ['label' => 'Aprobar cliente', 'method' => 'POST', 'url' => route('estimates.approve-client', $estimate), 'confirm' => '¿Confirmas la aprobación del cliente?'];
                    $actions[] = ['label' => 'Rechazar cliente', 'method' => 'POST', 'url' => route('estimates.reject-client', $estimate), 'reason' => true];
                }
                break;

            case 'approved_client':
                if ($canCreateWo) {
                    $actions[] = $this->generateWorkOrderAction($estimate);
                }
                break;

            case 'rejected_insurance':
                if ($canEdit) {
                    $actions[] = ['label' => 'Reenviar al seguro', 'method' => 'POST', 'url' => route('estimates.send-to-insurance', $estimate)];
                }
                break;

            case 'rejected_client':
                if ($canEdit) {
                    $actions[] = ['label' => 'Reenviar al cliente', 'method' => 'POST', 'url' => route('estimates.send-to-client', $estimate)];
                }
                break;
        }

        return $actions;
    }

    /** Acción "Generar OT" (work-orders.store con estimate_id). */
    protected function generateWorkOrderAction(Estimate $estimate): array
    {
        return [
            'label' => 'Generar OT',
            'method' => 'POST',
            'url' => route('work-orders.store'),
            'fields' => ['estimate_id' => $estimate->id],
            'confirm' => '¿Generar la orden de trabajo con este presupuesto?',
        ];
    }

    /** Acciones contextuales de una OT según su estado y permisos. */
    protected function workOrderActions(WorkOrder $workOrder, User $user): array
    {
        $actions = [['label' => 'Ver', 'method' => 'GET', 'url' => route('work-orders.show', $workOrder)]];
        $canStatus = $user->can('editar órdenes de trabajo');

        if ($workOrder->status === 'quality_control') {
            if ($canStatus) {
                $actions[] = ['label' => 'Realizar control de calidad', 'method' => 'GET', 'url' => route('work-orders.quality-control', $workOrder)];
            }
        } elseif ($canStatus) {
            foreach ($this->workOrderTransitions($workOrder->status) as [$to, $label, $confirm]) {
                $action = [
                    'label' => $label,
                    'method' => 'POST',
                    'url' => route('work-orders.transition', $workOrder),
                    'fields' => ['status' => $to],
                ];
                if ($confirm) {
                    $action['confirm'] = $confirm;
                }
                $actions[] = $action;
            }
        }

        return $actions;
    }

    /** Transiciones de OT legibles para el tablero (mirror del show). */
    protected function workOrderTransitions(string $status): array
    {
        return match ($status) {
            'open' => [['in_progress', 'Iniciar progreso', null]],
            'in_progress' => [
                ['quality_control', 'Enviar a control de calidad', null],
                ['waiting_parts', 'En espera de repuestos', null],
                ['delivered_pending', 'Entregar con pendientes', null],
            ],
            'waiting_parts' => [
                ['in_progress', 'Reanudar trabajo', null],
                ['quality_control', 'Enviar a control de calidad', null],
                ['delivered_pending', 'Entregar con pendientes', null],
            ],
            'ready_for_delivery' => [
                ['delivered', 'Marcar entregada', '¿Confirmas la entrega del vehículo?'],
                ['delivered_pending', 'Entregar con pendientes', null],
                ['in_progress', 'Volver a reparación', null],
            ],
            'delivered' => [['closed', 'Cerrar OT', '¿Cerrar la orden de trabajo?']],
            'delivered_pending' => [
                ['in_progress', 'Reanudar trabajo', null],
                ['delivered', 'Entrega final', '¿Confirmas la entrega final del vehículo?'],
            ],
            default => [],
        };
    }
}
