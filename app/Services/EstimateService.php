<?php

namespace App\Services;

use App\Models\CheckIn;
use App\Models\Establishment;
use App\Models\Estimate;
use App\Models\EstimateItem;
use App\Models\Part;
use App\Models\Party;
use App\Models\PublicApprovalLog;
use App\Models\RepairService;
use App\Models\ThirdPartyOrder;
use App\Models\Vehicle;
use App\Models\VehicleRelationship;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use RuntimeException;

class EstimateService
{
    protected EstimateCalculationService $calculation;

    public function __construct(EstimateCalculationService $calculation)
    {
        $this->calculation = $calculation;
    }

    /**
     * Transiciones válidas del flujo de presupuestos.
     */
    public const TRANSITIONS = [
        'draft' => ['sent_insurance', 'sent_client'],
        'sent_insurance' => ['approved_insurance', 'rejected_insurance', 'draft'],
        'sent_client' => ['approved_client', 'rejected_client', 'draft'],
        'approved_insurance' => ['in_repair', 'draft', 'sent_client'],
        'approved_client' => ['in_repair', 'draft'],
        'in_repair' => ['finalized', 'draft'],
        'finalized' => [],
        // Los rechazados no son terminales: se pueden reabrir (draft) o reenviar
        // directamente tras corregirlos (sent_insurance / sent_client).
        'rejected_insurance' => ['draft', 'sent_insurance'],
        'rejected_client' => ['draft', 'sent_client'],
    ];

    public function create(array $data): Estimate
    {
        $data['created_by'] = Auth::id();
        $data['updated_by'] = Auth::id();
        $data['status'] = $data['status'] ?? 'draft';

        $establishmentId = $data['establishment_id'] ?? Auth::user()?->establishment_id;
        $data['establishment_id'] = $establishmentId;

        $this->fillDefaults($data);

        if (empty($data['document_number'])) {
            $this->assignDocumentNumber((int) $establishmentId, $data);
        }

        $estimate = DB::transaction(function () use ($data) {
            $estimate = Estimate::create($this->onlyEstimateFields($data));

            $this->syncItems($estimate, $data['items'] ?? []);
            $this->syncThirdPartyOrders($estimate, $data['third_party_orders'] ?? []);

            $this->calculation->calculate($estimate);

            return $estimate;
        });

        return $estimate->load($this->defaultRelations());
    }

    public function update(Estimate $estimate, array $data): Estimate
    {
        if ($estimate->status === 'finalized') {
            throw new RuntimeException('No se puede editar un presupuesto finalizado.');
        }

        $data['updated_by'] = Auth::id();

        DB::transaction(function () use ($estimate, $data) {
            $estimate->update($this->onlyEstimateFields($data));

            $this->syncItems($estimate, $data['items'] ?? []);
            $this->syncThirdPartyOrders($estimate, $data['third_party_orders'] ?? []);

            $this->calculation->calculate($estimate);
        });

        return $estimate->load($this->defaultRelations());
    }

    public function delete(Estimate $estimate): bool
    {
        if ($estimate->work_order_id) {
            throw new RuntimeException('No se puede eliminar un presupuesto vinculado a una orden de trabajo. Desvincúlelo primero desde la OT.');
        }

        return (bool) $estimate->delete();
    }

    /**
     * Sincroniza ítems por diff/upsert (mismo patrón que CheckInService::syncDamages).
     */
    public function syncItems(Estimate $estimate, array $items): void
    {
        $existing = $estimate->items()->get()->keyBy('id');
        $sortOrder = 0;

        $serviceCache = [];
        $partCache = [];

        foreach ($items as $item) {
            $sortOrder++;

            // Fila totalmente vacía: se ignora (si tenía id previo, se elimina).
            if ($this->isEmptyItem($item)) {
                $id = $item['id'] ?? null;
                if ($id && isset($existing[$id])) {
                    $existing[$id]->delete();
                    $existing->forget($id);
                }
                continue;
            }

            $serviceId = $item['service_id'] ?? null;
            $partId = $item['part_id'] ?? null;

            // Ítems catalogados: derivar item_type y categoría snapshot desde el catálogo.
            if ($serviceId) {
                $service = $serviceCache[$serviceId] ??= RepairService::find($serviceId);
                $item['item_type'] = 'service';
                $item['service_category_id'] = $service?->service_category_id;
                $item['part_category_id'] = null;
                $item['uom'] = $service?->uom ?? 'HUR'; // snapshot SUNAT
            } elseif ($partId) {
                $part = $partCache[$partId] ??= Part::find($partId);
                $item['item_type'] = 'part';
                $item['part_category_id'] = $part?->part_category_id;
                $item['service_category_id'] = null;
                $item['uom'] = $part?->uom ?? 'NIU'; // snapshot SUNAT
            } else {
                // Ítem libre: el tipo y la categoría vienen del formulario (validados).
                $item['service_category_id'] = $item['service_category_id'] ?? null;
                $item['part_category_id'] = $item['part_category_id'] ?? null;
                $item['uom'] = $item['uom'] ?? null;
            }

            $id = $item['id'] ?? null;
            $payload = [
                'service_id' => $serviceId,
                'part_id' => $partId,
                'item_type' => $item['item_type'] ?? 'service',
                'service_category_id' => $item['service_category_id'] ?? null,
                'part_category_id' => $item['part_category_id'] ?? null,
                'description' => $item['description'] ?? null,
                'quantity' => $item['quantity'] ?? 1,
                'unit_price' => $item['unit_price'] ?? 0,
                'uom' => $item['uom'] ?? null,
                'discount_pct' => $item['discount_pct'] ?? 0,
                'supply_source' => $item['supply_source'] ?? 'internal',
                'cost_price' => $item['cost_price'] ?? 0,
                'sort_order' => $sortOrder,
            ];

            if ($id && isset($existing[$id])) {
                $existing[$id]->update($payload);
                $existing->forget($id);
            } else {
                EstimateItem::create(array_merge(['estimate_id' => $estimate->id], $payload));
            }
        }

        // Eliminar los ítems que ya no vienen en el request.
        foreach ($existing as $item) {
            $item->delete();
        }
    }

    /**
     * Sincroniza las órdenes de compra de terceros por diff/upsert
     * (mismo patrón que syncItems).
     */
    public function syncThirdPartyOrders(Estimate $estimate, array $orders): void
    {
        $existing = $estimate->thirdPartyOrders()->get()->keyBy('id');

        foreach ($orders as $order) {
            // Fila totalmente vacía: se ignora (si tenía id previo, se elimina).
            if ($this->isEmptyOrder($order)) {
                $id = $order['id'] ?? null;
                if ($id && isset($existing[$id])) {
                    $existing[$id]->delete();
                    $existing->forget($id);
                }
                continue;
            }

            $payload = [
                'description' => $order['description'] ?? null,
                'amount_without_iva' => $order['amount_without_iva'] ?? 0,
                'provider_name' => $order['provider_name'] ?? null,
            ];

            $id = $order['id'] ?? null;
            if ($id && isset($existing[$id])) {
                $existing[$id]->update($payload);
                $existing->forget($id);
            } else {
                ThirdPartyOrder::create(array_merge(['estimate_id' => $estimate->id], $payload));
            }
        }

        // Eliminar las OC que ya no vienen en el request.
        foreach ($existing as $order) {
            $order->delete();
        }
    }

    /**
     * Devuelve los ítems agrupados para la vista del cliente / PDF.
     *
     * Estructura:
     *  - services:   [{ category, items[] }]  — ítems tipo servicio agrupados por
     *                service_category (categoría snapshot o la del servicio).
     *  - parts_sale: [{ category, items[] }]  — repuestos que vende el taller
     *                (supply_source internal/external) agrupados por part_category.
     *  - parts_ins:  [{ category, items[] }]  — repuestos que traerá la compañía
     *                de seguros (supply_source insurance) agrupados por part_category.
     *
     * Los ítems catalogados obtienen su categoría del snapshot (service_category_id /
     * part_category_id) con fallback al catálogo; los libres usan el snapshot directo.
     */
    public function getClientGroupedItems(Estimate $estimate): array
    {
        $items = $estimate->items()
            ->with(['service.category', 'part.category', 'serviceCategory', 'partCategory'])
            ->orderBy('sort_order')
            ->get();

        $services = [];
        $partsSale = [];
        $partsIns = [];

        foreach ($items as $item) {
            if ($item->item_type === 'service') {
                $categoryName = $item->serviceCategory?->name
                    ?? $item->service?->category?->name
                    ?? 'Servicios';

                $services[$categoryName][] = $item;
                continue;
            }

            $categoryName = $item->partCategory?->name
                ?? $item->part?->category?->name
                ?? 'Repuestos';

            if (($item->supply_source ?? 'internal') === 'insurance') {
                $partsIns[$categoryName][] = $item;
            } else {
                $partsSale[$categoryName][] = $item;
            }
        }

        return [
            'services' => $this->groupToList($services),
            'parts_sale' => $this->groupToList($partsSale),
            'parts_ins' => $this->groupToList($partsIns),
        ];
    }

    /**
     * Convierte un mapa categoría => ítems en una lista [{category, items}].
     */
    protected function groupToList(array $groups): array
    {
        $list = [];
        foreach ($groups as $category => $items) {
            $list[] = ['category' => $category, 'items' => $items];
        }

        return $list;
    }

    /**
     * Valida y registra el cambio de estado (usuario interno).
     *
     * Cuando el nuevo estado es una aprobación/rechazo (seguro o cliente), registra
     * en las columnas approved_by_* / rejected_by_* quién lo hizo y escribe el log
     * de auditoría public_approval_logs. La aprobación del seguro y del cliente
     * comparten las columnas: gana la última (el gate final habilita la reparación);
     * el historial completo queda en estimate_status_history.
     */
    public function changeStatus(Estimate $estimate, string $newStatus, ?string $reason = null, ?string $date = null): Estimate
    {
        $from = $estimate->status;
        $allowed = self::TRANSITIONS[$from] ?? [];

        if (!in_array($newStatus, $allowed, true)) {
            throw new RuntimeException("Transición de estado inválida: {$from} → {$newStatus}.");
        }

        $date = $date ?: now()->format('Y-m-d');

        DB::transaction(function () use ($estimate, $from, $newStatus, $reason, $date) {
            $approvalFields = [];

            if ($newStatus === 'approved_insurance') {
                // Gate del SEGURO: fecha registrada manualmente por el usuario.
                $approvalFields = [
                    'insurance_approved_by_user_id' => Auth::id(),
                    'insurance_approved_at' => $date,
                ];
            } elseif ($newStatus === 'rejected_insurance') {
                $approvalFields = [
                    'insurance_rejected_by_user_id' => Auth::id(),
                    'insurance_rejected_at' => $date,
                    'insurance_rejection_reason' => $reason,
                ];
            } elseif ($newStatus === 'approved_client') {
                $approvalFields = [
                    'approved_by_user_id' => Auth::id(),
                    'approved_at' => now(),
                ];
            } elseif ($newStatus === 'rejected_client') {
                $approvalFields = [
                    'rejected_by_user_id' => Auth::id(),
                    'rejected_at' => now(),
                    'rejection_reason' => $reason,
                ];
            }

            $estimate->update(array_merge([
                'status' => $newStatus,
                'updated_by' => Auth::id(),
            ], $approvalFields));

            $estimate->recordStatusChange($newStatus, $from, $reason, 'internal');

            if (in_array($newStatus, ['approved_insurance', 'rejected_insurance', 'approved_client', 'rejected_client'], true)) {
                $this->logApproval(
                    $estimate,
                    str_starts_with($newStatus, 'approved') ? 'approved' : 'rejected',
                    'internal',
                    $reason
                );
            }

            // Cuando el presupuesto se finaliza, el vehículo salió del taller:
            // se cierra automáticamente el inventario asociado (estado terminal).
            if ($newStatus === 'finalized' && $estimate->check_in_id) {
                $checkIn = CheckIn::find($estimate->check_in_id);
                if ($checkIn && ! in_array($checkIn->status, ['closed', 'rejected'], true)) {
                    $from = $checkIn->status;

                    $checkIn->update([
                        'status' => 'closed',
                        'closed_by' => Auth::id(),
                        'closed_at' => now(),
                        'updated_by' => Auth::id(),
                    ]);

                    $checkIn->recordStatusChange('closed', $from, 'Cerrado automáticamente al finalizar el presupuesto.', 'system');
                }
            }
        });

        return $estimate->fresh();
    }

    /**
     * Cambia el estado del presupuesto desde el portal del cliente.
     *
     * Solo permite la aprobación/rechazo del gate del cliente (sent_client →
     * approved_client / rejected_client). El responsable se copia del snapshot
     * last_sent_to / last_sent_to_phone grabado al momento del envío del enlace.
     */
    public function changeStatusByClient(
        Estimate $estimate,
        string $newStatus,
        ?string $reason = null,
        string $ip = '',
        string $userAgent = ''
    ): Estimate {
        $from = $estimate->status;
        $allowed = self::TRANSITIONS[$from] ?? [];

        if (!in_array($newStatus, $allowed, true)) {
            throw new RuntimeException("Transición de estado inválida: {$from} → {$newStatus}.");
        }

        if ($estimate->service_type === 'siniestro' && ! $estimate->insurance_approved_at) {
            throw new RuntimeException('Para un siniestro, el presupuesto debe estar aprobado por el seguro antes de que el cliente lo apruebe o rechace.');
        }

        if (!in_array($newStatus, ['approved_client', 'rejected_client'], true)) {
            throw new RuntimeException('El portal del cliente solo puede aprobar o rechazar la aprobación del cliente.');
        }

        $recipient = $estimate->last_sent_to ?: $estimate->contact_name ?: $estimate->client?->display_name;
        $phone = $estimate->last_sent_to_phone ?: $estimate->contact_phone;

        DB::transaction(function () use ($estimate, $from, $newStatus, $reason, $recipient, $phone, $ip, $userAgent) {
            $approvalFields = $newStatus === 'approved_client'
                ? ['approved_by_recipient' => $recipient, 'approved_by_phone' => $phone, 'approved_at' => now()]
                : ['rejected_by_recipient' => $recipient, 'rejected_by_phone' => $phone, 'rejection_reason' => $reason, 'rejected_at' => now()];

            $estimate->update(array_merge(['status' => $newStatus], $approvalFields));

            $estimate->recordStatusChange(
                $newStatus,
                $from,
                $reason ?: ($newStatus === 'approved_client'
                    ? 'Aprobado por el cliente vía portal'
                    : 'Rechazado por el cliente vía portal'),
                'client',
                null
            );

            $this->logApproval(
                $estimate,
                $newStatus === 'approved_client' ? 'approved' : 'rejected',
                'portal',
                $reason,
                $recipient,
                $phone,
                $ip,
                $userAgent
            );
        });

        return $estimate->fresh();
    }

    /**
     * Registra la aprobación/rechazo (interno o portal) en public_approval_logs.
     */
    protected function logApproval(
        Estimate $estimate,
        string $action,
        string $actorType,
        ?string $reason = null,
        ?string $recipient = null,
        ?string $phone = null,
        string $ip = '',
        string $userAgent = ''
    ): void {
        PublicApprovalLog::create([
            'vehicle_id' => $estimate->vehicle_id,
            'action' => $action,
            'entity_type' => 'estimate',
            'entity_id' => $estimate->id,
            'actor_type' => $actorType,
            'actor_user_id' => $actorType === 'internal' ? Auth::id() : null,
            'actor_recipient' => $recipient,
            'actor_phone' => $phone,
            'reason' => $reason,
            'ip_address' => $ip ?: null,
            'user_agent' => $userAgent ?: null,
        ]);
    }

    /**
     * Resuelve el destinatario por defecto de un vehículo para el presupuesto.
     *
     * Prioridad: 1º relación con rol 'approver' (aprobador), 2º rol 'owner'
     * (propietario). Devuelve los datos del contacto (para precargar client_id
     * y contact_name/phone/email) o null si el vehículo no tiene ninguno.
     */
    public function resolveRecipient(Vehicle $vehicle): ?array
    {
        $relationship = $vehicle->relationships()
            ->whereIn('role', ['approver', 'owner'])
            ->with('party')
            ->orderByRaw("FIELD(role, 'approver', 'owner')")
            ->first();

        $party = $relationship?->party;

        if (!$party) {
            return null;
        }

        return [
            'role' => $relationship->role,
            'party_id' => $party->id,
            'contact_name' => $party->display_name,
            'contact_phone' => $party->mobile ?: $party->phone,
            'contact_email' => $party->email,
            'party' => [
                'id' => $party->id,
                'display_name' => $party->display_name,
                'document_number' => $party->document_number,
            ],
        ];
    }

    /**
     * Devuelve todos los contactos del vehículo agrupados por rol, para el
     * miniselector "Contacto del vehículo" del formulario de presupuestos.
     * Orden de prioridad: approver → owner → driver → operator → billing → resto.
     */
    public function resolveRecipients(Vehicle $vehicle): array
    {
        $roles = VehicleRelationship::roleLabels();

        return $vehicle->relationships()
            ->with('party')
            ->orderByRaw("FIELD(role, 'approver', 'owner', 'driver', 'operator', 'billing', 'insurance_company', 'emergency_contact', 'other')")
            ->get()
            ->map(fn ($rel) => [
                'id' => $rel->id,
                'role' => $rel->role,
                'role_label' => $roles[$rel->role] ?? ucfirst((string) $rel->role),
                'party_id' => $rel->party?->id,
                'contact_name' => $rel->party?->display_name,
                'contact_phone' => $rel->party?->mobile ?: $rel->party?->phone,
                'contact_email' => $rel->party?->email,
                'party' => $rel->party ? [
                    'id' => $rel->party->id,
                    'display_name' => $rel->party->display_name,
                    'document_number' => $rel->party->document_number,
                ] : null,
            ])
            ->values()
            ->all();
    }

    /**
     * Crea un presupuesto precargando los datos de un inventario (CheckIn).
     */
    public function createFromCheckIn(CheckIn $checkIn): Estimate
    {
        $establishment = $checkIn->establishment()->first()
            ?? Establishment::query()->first();

        $rates = $this->resolveRates($checkIn->insuranceCompany()->first(), $establishment);

        $data = [
            'check_in_id' => $checkIn->id,
            'vehicle_id' => $checkIn->vehicle_id,
            'client_id' => $checkIn->client_id,
            'insurance_company_id' => $checkIn->insurance_company_id,
            'claim_number' => $checkIn->claim_number,
            'service_type' => $checkIn->service_type,
            'establishment_id' => $checkIn->establishment_id,
            'advisor_id' => Auth::id(),
            'hourly_rate' => $rates['hourly_rate'],
            'panel_rate' => $rates['panel_rate'],
            'items' => [],
        ];

        return $this->create($data);
    }

    public function getSearchResults(array $filters): array
    {
        $query = Estimate::query()
            ->with(['vehicle.vehicleModel.brand', 'client', 'insuranceCompany']);

        if (!empty($filters['q'])) {
            $term = $filters['q'];
            $query->where(function ($q) use ($term) {
                $q->whereHas('vehicle', fn ($v) => $v->where('plate', 'like', "%{$term}%"))
                    ->orWhereHas('vehicle.vehicleModel.brand', fn ($b) => $b->where('name', 'like', "%{$term}%"))
                    ->orWhereHas('client', fn ($c) => $c
                        ->where('business_name', 'like', "%{$term}%")
                        ->orWhere('first_name', 'like', "%{$term}%")
                        ->orWhere('last_name', 'like', "%{$term}%"))
                    ->orWhere('document_sn', 'like', "%{$term}%")
                    ->orWhere('document_serie', 'like', "%{$term}%")
                    ->orWhereRaw('CAST(document_number AS CHAR) LIKE ?', ["%{$term}%"]);
            });
        }

        if (!empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        if (!empty($filters['plate'])) {
            $query->whereHas('vehicle', fn ($v) => $v->where('plate', 'like', '%' . strtoupper($filters['plate']) . '%'));
        }

        if (!empty($filters['client_id'])) {
            $query->where('client_id', $filters['client_id']);
        }

        if (!empty($filters['date_from'])) {
            $query->whereDate('created_at', '>=', $filters['date_from']);
        }

        if (!empty($filters['date_to'])) {
            $query->whereDate('created_at', '<=', $filters['date_to']);
        }

        $query->orderByDesc('created_at');

        $limit = (int) ($filters['limit'] ?? 100);
        $query->limit($limit);

        return $query->get()->map(fn (Estimate $estimate) => [
            'id' => $estimate->id,
            'document_sn' => $estimate->document_sn,
            'plate' => $estimate->vehicle?->plate,
            'client_name' => $estimate->client?->display_name,
            'client_document' => $estimate->client?->document_number,
            'insurance_company' => $estimate->insuranceCompany?->display_name,
            'status' => $estimate->status,
            'status_label' => $estimate->status_label,
            'subtotal' => $estimate->subtotal,
            'total' => $estimate->total,
            'created_at' => $estimate->created_at?->format('d/m/Y'),
        ])->all();
    }

    /**
     * Determina hourly_rate y panel_rate: aseguradora → establecimiento.
     */
    public function resolveRates(?Party $insurance, ?Establishment $establishment): array
    {
        $hourlyRate = 0.0;
        $panelRate = 0.0;

        if ($insurance) {
            $hourlyRate = (float) $insurance->insurance_hourly_rate;
            $panelRate = (float) $insurance->insurance_panel_rate;
        }

        if ($establishment) {
            if ($hourlyRate <= 0) {
                $hourlyRate = (float) $establishment->default_hourly_rate;
            }
            if ($panelRate <= 0) {
                $panelRate = (float) $establishment->default_panel_rate;
            }
        }

        return [
            'hourly_rate' => round($hourlyRate, 2),
            'panel_rate' => round($panelRate, 2),
        ];
    }

    /**
     * Rellena moneda, tipo de cambio y tarifas por defecto desde el establecimiento.
     */
    protected function fillDefaults(array &$data): void
    {
        $establishmentId = $data['establishment_id'] ?? null;
        $establishment = $establishmentId ? Establishment::find($establishmentId) : null;

        if ($establishment) {
            $data['currency'] = $data['currency'] ?? ($establishment->base_currency ?: 'PEN');
            $data['exchange_rate'] = $data['exchange_rate'] ?? ($data['currency'] === 'PEN' ? 1 : 1);

            $insuranceId = $data['insurance_company_id'] ?? null;
            $insurance = $insuranceId ? Party::find($insuranceId) : null;

            $rates = $this->resolveRates($insurance, $establishment);

            $data['hourly_rate'] = $data['hourly_rate'] ?? $rates['hourly_rate'];
            $data['panel_rate'] = $data['panel_rate'] ?? $rates['panel_rate'];
        } else {
            $data['currency'] = $data['currency'] ?? 'PEN';
            $data['exchange_rate'] = $data['exchange_rate'] ?? 1;
            $data['hourly_rate'] = $data['hourly_rate'] ?? 0;
            $data['panel_rate'] = $data['panel_rate'] ?? 0;
        }
    }

    protected function assignDocumentNumber(int $establishmentId, array &$data): void
    {
        $result = app(DocumentSeriesService::class)->getNextNumber($establishmentId, 'PRE');

        $data['document_series_id'] = $result['series']->id;
        $data['document_type_code'] = $result['document_type_code'];
        $data['document_serie'] = $result['series']->prefix_serie;

        if ($result['number'] === null) {
            // Serie API: sin correlativo aún. Guardar el sn como 'PRE01-####'.
            $data['document_number'] = null;
            $data['document_sn'] = $result['series']->prefix_serie . '-####';
            return;
        }

        $data['document_number'] = $result['number'];
        $data['document_sn'] = $result['sn'];
    }

    /**
     * Filtra solo los campos que pertenecen a la tabla estimates.
     */
    protected function onlyEstimateFields(array $data): array
    {
        return array_intersect_key($data, array_flip((new Estimate())->getFillable()));
    }

    protected function isEmptyItem(array $item): bool
    {
        $isEmpty = empty($item['service_id'])
            && empty($item['part_id'])
            && empty($item['description'])
            && empty($item['quantity'])
            && empty($item['unit_price']);

        return $isEmpty;
    }

    protected function isEmptyOrder(array $order): bool
    {
        return empty($order['description']) && empty($order['amount_without_iva']);
    }

    protected function defaultRelations(): array
    {
        return [
            'vehicle.vehicleModel.brand',
            'client',
            'insuranceCompany',
            'advisor',
            'establishment',
            'documentSeries.documentType',
            'items.service.category',
            'items.part.category',
            'items.serviceCategory',
            'items.partCategory',
            'thirdPartyOrders',
        ];
    }
}
