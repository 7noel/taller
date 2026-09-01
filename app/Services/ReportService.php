<?php

namespace App\Services;

use App\Models\CheckIn;
use App\Models\Estimate;
use App\Models\EstimateItem;
use App\Models\FollowUp;
use App\Models\Invoice;
use App\Models\Party;
use App\Models\Payment;
use App\Models\ServiceVoucher;
use App\Models\ThirdPartyOrder;
use App\Models\User;
use App\Models\WorkOrder;
use App\Models\WorkOrderAssignment;
use App\Models\WorkOrderInternalExpense;
use Illuminate\Support\Carbon;

/**
 * Reportes de gestión del taller (módulo 10).
 *
 * Todos los montos se normalizan a la moneda funcional PEN usando el snapshot
 * de tipo de cambio de cada registro (convención del sistema: exchange_rate =
 * soles por 1 dólar; ver ExchangeRateService).
 */
class ReportService
{
    public const APPROVED_STATUSES = ['approved_insurance', 'approved_client', 'in_repair', 'finalized'];

    public function __construct(protected ExchangeRateService $exchange)
    {
    }

    /* ----------------------------- utilidades ----------------------------- */

    protected function range(array $f): array
    {
        $from = ! empty($f['from']) ? $f['from'] : now()->subMonths(12)->startOfMonth()->toDateString();
        $to = ! empty($f['to']) ? $f['to'] : now()->toDateString();

        return [$from, $to];
    }

    protected function establishmentId(array $f): ?int
    {
        $id = $f['establishment_id'] ?? null;

        return $id ? (int) $id : null;
    }

    protected function applyEstablishment($query, array $f, string $column = 'establishment_id')
    {
        $est = $this->establishmentId($f);

        if ($est) {
            $query->where($column, $est);
        }

        return $query;
    }

    protected function toPen(float $amount, ?string $currency, ?float $rate): float
    {
        return (float) $this->exchange->convert($amount, strtoupper((string) ($currency ?: 'PEN')), 'PEN', (float) ($rate ?: 1));
    }

    protected function money(float $amount): float
    {
        return round($amount, 2);
    }

    protected function pct(float $part, float $total): float
    {
        return $total > 0 ? round($part * 100 / $total, 1) : 0.0;
    }

    protected function partyName(?Party $party): string
    {
        if (! $party) {
            return '—';
        }

        $name = trim((string) $party->business_name);

        if ($name === '') {
            $name = trim(($party->first_name ?? '').' '.($party->last_name ?? ''));
        }

        return $name !== '' ? $name : '—';
    }

    /* ------------------- 1. Frecuencia de vehículos (flota) ------------------- */

    public function vehicleFrequency(array $f = []): array
    {
        [$from, $to] = $this->range($f);

        $query = CheckIn::query()
            ->whereDate('created_at', '>=', $from)
            ->whereDate('created_at', '<=', $to);
        $this->applyEstablishment($query, $f);

        if (! empty($f['service_type'])) {
            $query->where('service_type', $f['service_type']);
        }

        $checkIns = $query->with('vehicle:id,brand_id,model_id,year,plate')
            ->with('vehicle.brand:id,name')
            ->with('vehicle.vehicleModel:id,brand_id,name')
            ->get(['id', 'vehicle_id', 'service_type', 'created_at']);

        if ($checkIns->isEmpty()) {
            return ['kpis' => $this->vehicleKpis(0, 0, null), 'series' => [], 'rows' => []];
        }

        $total = count($checkIns);
        $uniqueVehicles = $checkIns->pluck('vehicle_id')->unique();

        $brands = [];
        $models = [];
        $years = [];
        $services = [];
        $months = [];
        $group = [];

        foreach ($checkIns as $ci) {
            $v = $ci->vehicle;
            $brand = $v?->brand?->name ?: 'Sin marca';
            $model = $v?->vehicleModel?->name ?: 'Sin modelo';
            $year = $v?->year ?: '—';
            $month = $ci->created_at->format('Y-m');
            $service = CheckIn::SERVICE_TYPES[$ci->service_type] ?? ucfirst((string) ($ci->service_type ?: 'otro'));

            $key = $brand.'|'.$model.'|'.$year;

            $group[$key] ??= ['brand' => $brand, 'model' => $model, 'year' => $year, 'visits' => 0, 'vehicles' => []];
            $group[$key]['visits']++;
            $group[$key]['vehicles'][$v->id] = true;

            $brands[$brand] = ($brands[$brand] ?? 0) + 1;
            $models[$model] = ($models[$model] ?? 0) + 1;
            $years[$year] = ($years[$year] ?? 0) + 1;
            $services[$service] = ($services[$service] ?? 0) + 1;
            $months[$month] = ($months[$month] ?? 0) + 1;
        }

        $rows = collect($group)->map(fn ($r) => [
            'brand' => $r['brand'],
            'model' => $r['model'],
            'year' => (string) $r['year'],
            'visits' => $r['visits'],
            'vehicles' => count($r['vehicles']),
            'share' => $this->pct($r['visits'], $total),
        ])->sortByDesc('visits')->values()->all();

        $brandRows = collect($brands)
            ->map(fn ($visits, $name) => ['name' => $name, 'visits' => $visits, 'share' => $this->pct($visits, $total)])
            ->sortByDesc('visits')->values();
        $topBrand = $brandRows->first();

        $monthly = collect($months)
            ->map(fn ($count, $key) => ['month' => $key, 'name' => Carbon::createFromFormat('Y-m', $key)->translatedFormat('M Y'), 'visits' => $count])
            ->sortBy('month')->values()->all();

        return [
            'kpis' => $this->vehicleKpis($total, $uniqueVehicles->count(), $topBrand),
            'series' => [
                'brands' => $brandRows->take(10)->values()->all(),
                'models' => collect($models)->map(fn ($visits, $name) => ['name' => $name, 'visits' => $visits])->sortByDesc('visits')->take(10)->values()->all(),
                'years' => collect($years)->map(fn ($visits, $year) => ['name' => (string) $year, 'visits' => $visits])->sortKeysDesc()->values()->all(),
                'services' => collect($services)->map(fn ($visits, $name) => ['name' => $name, 'visits' => $visits])->sortByDesc('visits')->values()->all(),
                'monthly' => $monthly,
            ],
            'rows' => $rows,
        ];
    }

    protected function vehicleKpis(int $total, int $unique, ?array $topBrand): array
    {
        return [
            ['label' => 'Visitas (ingresos)', 'value' => number_format($total, 0), 'sub' => 'check-ins en el período', 'color' => 'blue'],
            ['label' => 'Vehículos únicos', 'value' => number_format($unique, 0), 'sub' => 'placas distintas', 'color' => 'indigo'],
            ['label' => 'Visitas por vehículo', 'value' => $unique > 0 ? number_format($total / $unique, 2) : '0', 'sub' => 'promedio de reingreso', 'color' => 'gray'],
            ['label' => 'Marca líder', 'value' => $topBrand['name'] ?? '—', 'sub' => ($topBrand['share'] ?? 0).'% de las visitas', 'color' => 'green'],
        ];
    }
    /* ----------------------- 2. Costos y utilidad por OT ----------------------- */

    public function workOrderProfitability(array $f = []): array
    {
        $rows = $this->workOrderProfitRows($f);

        $income = array_sum(array_column($rows, 'income'));
        $cost = array_sum(array_column($rows, 'cost'));
        $profit = array_sum(array_column($rows, 'profit'));
        $profitable = count(array_filter($rows, fn ($r) => $r['profit'] >= 0));

        $monthly = [];
        foreach ($rows as $r) {
            $key = substr((string) $r['date'], 0, 7);
            $monthly[$key] ??= ['income' => 0.0, 'cost' => 0.0, 'profit' => 0.0];
            $monthly[$key]['income'] += $r['income'];
            $monthly[$key]['cost'] += $r['cost'];
            $monthly[$key]['profit'] += $r['profit'];
        }
        ksort($monthly);

        $monthlySeries = [];
        foreach ($monthly as $key => $m) {
            $monthlySeries[] = [
                'month' => $key,
                'name' => Carbon::createFromFormat('Y-m', $key)->translatedFormat('M Y'),
                'income' => $this->money($m['income']),
                'cost' => $this->money($m['cost']),
                'profit' => $this->money($m['profit']),
            ];
        }

        $costDonut = [
            ['name' => 'Repuestos', 'value' => $this->money(array_sum(array_column($rows, 'parts')))],
            ['name' => 'Mano de obra', 'value' => $this->money(array_sum(array_column($rows, 'assignments')))],
            ['name' => 'Vales terceros', 'value' => $this->money(array_sum(array_column($rows, 'vouchers')))],
            ['name' => 'OC a terceros', 'value' => $this->money(array_sum(array_column($rows, 'third_party')))],
            ['name' => 'Gastos internos', 'value' => $this->money(array_sum(array_column($rows, 'internal_expenses')))],
        ];

        $statusRows = collect($rows)->groupBy('status_label')
            ->map(fn ($g, $label) => ['name' => $label, 'count' => count($g)])
            ->sortByDesc('count')->values()->all();

        return [
            'kpis' => [
                ['label' => 'Órdenes de trabajo', 'value' => number_format(count($rows), 0), 'sub' => 'en el período', 'color' => 'blue'],
                ['label' => 'Ingresos', 'value' => 'S/ '.number_format($income, 2), 'sub' => 'presupuestos facturables', 'color' => 'green'],
                ['label' => 'Costos', 'value' => 'S/ '.number_format($cost, 2), 'sub' => 'todos los componentes', 'color' => 'red'],
                ['label' => 'Utilidad', 'value' => 'S/ '.number_format($profit, 2), 'sub' => 'margen '.($income > 0 ? round($profit * 100 / $income, 1) : 0).'% · '.$profitable.' OTs rentables', 'color' => 'indigo'],
            ],
            'series' => [
                'monthly' => $monthlySeries,
                'cost_donut' => $costDonut,
                'status' => $statusRows,
            ],
            'rows' => $rows,
        ];
    }

    protected function workOrderProfitRows(array $f): array
    {
        [$from, $to] = $this->range($f);

        $query = WorkOrder::query()
            ->with([
                'vehicle' => fn ($q) => $q->withTrashed()->select('id', 'plate', 'brand_id', 'model_id'),
                'vehicle.brand:id,name',
                'vehicle.vehicleModel:id,name',
                'client:id,business_name,first_name,last_name',
            ])
            ->whereDate('created_at', '>=', $from)
            ->whereDate('created_at', '<=', $to);
        $this->applyEstablishment($query, $f);

        $workOrders = $query->with(['estimates' => fn ($q) => $q->select('id', 'work_order_id', 'document_sn', 'total', 'currency', 'exchange_rate', 'is_chargeable', 'advisor_id', 'status')])
            ->get();

        if ($workOrders->isEmpty()) {
            return [];
        }

        $woIds = $workOrders->pluck('id');
        $income = [];
        $advisor = [];

        foreach ($workOrders as $wo) {
            $sum = 0.0;
            $woAdvisor = null;

            foreach ($wo->estimates as $e) {
                if ($e->is_chargeable === false) {
                    continue;
                }

                $sum += $this->toPen((float) $e->total, $e->currency, $e->exchange_rate);
                $woAdvisor ??= $e->advisor_id;
            }

            $income[$wo->id] = $this->money($sum);
            $advisor[$wo->id] = $woAdvisor;
        }

        // Componentes de costo: una consulta por componente, normalizada a PEN.
        $costs = array_fill_keys($woIds->all(), []);

        $parts = EstimateItem::query()
            ->where('item_type', 'part')
            ->join('estimates', 'estimates.id', '=', 'estimate_items.estimate_id')
            ->whereIn('estimates.work_order_id', $woIds)
            ->get(['estimates.work_order_id as wo_id', 'estimate_items.cost_price', 'estimate_items.quantity', 'estimates.currency', 'estimates.exchange_rate']);
        foreach ($parts as $p) {
            $costs[$p->wo_id]['parts'] = ($costs[$p->wo_id]['parts'] ?? 0) + $this->toPen((float) $p->cost_price * (float) $p->quantity, $p->currency, $p->exchange_rate);
        }

        $assignments = WorkOrderAssignment::query()->whereIn('work_order_id', $woIds)
            ->get(['work_order_id', 'cost', 'currency', 'exchange_rate']);
        foreach ($assignments as $a) {
            $costs[$a->work_order_id]['assignments'] = ($costs[$a->work_order_id]['assignments'] ?? 0) + $this->toPen((float) $a->cost, $a->currency, $a->exchange_rate);
        }

        $vouchers = ServiceVoucher::query()->whereIn('work_order_id', $woIds)
            ->get(['work_order_id', 'base_amount', 'currency', 'exchange_rate']);
        foreach ($vouchers as $v) {
            $costs[$v->work_order_id]['vouchers'] = ($costs[$v->work_order_id]['vouchers'] ?? 0) + $this->toPen((float) $v->base_amount, $v->currency, $v->exchange_rate);
        }

        $thirdParty = ThirdPartyOrder::query()
            ->join('estimates', 'estimates.id', '=', 'third_party_orders.estimate_id')
            ->whereIn('estimates.work_order_id', $woIds)
            ->get(['estimates.work_order_id as wo_id', 'third_party_orders.amount_without_iva', 'third_party_orders.currency', 'third_party_orders.exchange_rate', 'estimates.currency as est_currency', 'estimates.exchange_rate as est_rate']);
        foreach ($thirdParty as $t) {
            $costs[$t->wo_id]['third_party'] = ($costs[$t->wo_id]['third_party'] ?? 0) + $this->toPen((float) $t->amount_without_iva, $t->currency ?: $t->est_currency, $t->exchange_rate ?: $t->est_rate);
        }

        $internal = WorkOrderInternalExpense::query()->whereIn('work_order_id', $woIds)
            ->get(['work_order_id', 'amount', 'currency', 'exchange_rate']);
        foreach ($internal as $i) {
            $costs[$i->work_order_id]['internal_expenses'] = ($costs[$i->work_order_id]['internal_expenses'] ?? 0) + $this->toPen((float) $i->amount, $i->currency, $i->exchange_rate);
        }

        $rows = [];

        foreach ($workOrders as $wo) {
            $components = $costs[$wo->id] ?? [];
            $cost = $this->money(array_sum($components));
            $inc = $income[$wo->id] ?? 0.0;
            $profit = $this->money($inc - $cost);

            $rows[] = [
                'id' => $wo->id,
                'document_sn' => $wo->document_sn,
                'plate' => $wo->vehicle?->plate ?? '—',
                'brand' => $wo->vehicle?->brand?->name ?? '—',
                'model' => $wo->vehicle?->vehicleModel?->name ?? '—',
                'client' => $this->partyName($wo->client),
                'status' => $wo->status,
                'status_label' => $wo->status_label,
                'date' => $wo->created_at->format('Y-m-d'),
                'income' => $inc,
                'cost' => $cost,
                'parts' => $this->money($components['parts'] ?? 0),
                'vouchers' => $this->money($components['vouchers'] ?? 0),
                'assignments' => $this->money($components['assignments'] ?? 0),
                'third_party' => $this->money($components['third_party'] ?? 0),
                'internal_expenses' => $this->money($components['internal_expenses'] ?? 0),
                'profit' => $profit,
                'margin' => $inc > 0 ? round($profit * 100 / $inc, 1) : 0.0,
                'advisor_id' => $advisor[$wo->id] ?? null,
            ];
        }

        usort($rows, fn ($a, $b) => strcmp($b['date'], $a['date']));

        return $rows;
    }
    /* ------------------------------ 3. Asesores ------------------------------ */

    public function advisorProfitability(array $f = []): array
    {
        [$from, $to] = $this->range($f);

        // Agregados a nivel de presupuesto (venta y aprobación).
        $estQuery = Estimate::query()
            ->whereDate('created_at', '>=', $from)
            ->whereDate('created_at', '<=', $to)
            ->whereNotNull('advisor_id');
        $this->applyEstablishment($estQuery, $f);

        $estimates = $estQuery->get(['id', 'advisor_id', 'status', 'total', 'currency', 'exchange_rate', 'is_chargeable']);

        $estimatesByAdvisor = [];
        foreach ($estimates as $e) {
            $id = (int) $e->advisor_id;
            $estimatesByAdvisor[$id] ??= ['estimates' => 0, 'approved' => 0, 'revenue' => 0.0, 'tickets' => 0, 'ticket_sum' => 0.0];

            $estimatesByAdvisor[$id]['estimates']++;
            if (in_array($e->status, self::APPROVED_STATUSES, true)) {
                $estimatesByAdvisor[$id]['approved']++;
            }
            if ($e->is_chargeable !== false) {
                $rev = $this->toPen((float) $e->total, $e->currency, $e->exchange_rate);
                $estimatesByAdvisor[$id]['revenue'] += $rev;
                $estimatesByAdvisor[$id]['ticket_sum'] += $rev;
                $estimatesByAdvisor[$id]['tickets']++;
            }
        }

        // Utilidad real atribuida desde las OTs (fuente única de verdad).
        $otByAdvisor = [];
        foreach ($this->workOrderProfitRows($f) as $r) {
            if (! $r['advisor_id']) {
                continue;
            }
            $otByAdvisor[$r['advisor_id']] ??= ['work_orders' => 0, 'income' => 0.0, 'cost' => 0.0, 'profit' => 0.0];
            $otByAdvisor[$r['advisor_id']]['work_orders']++;
            $otByAdvisor[$r['advisor_id']]['income'] += $r['income'];
            $otByAdvisor[$r['advisor_id']]['cost'] += $r['cost'];
            $otByAdvisor[$r['advisor_id']]['profit'] += $r['profit'];
        }

        $userIds = collect($estimatesByAdvisor)->keys()->merge(collect($otByAdvisor)->keys())->unique();
        $users = User::whereIn('id', $userIds)->get(['id', 'name'])->keyBy('id');

        $rows = [];

        foreach ($otByAdvisor as $id => $ot) {
            $est = $estimatesByAdvisor[$id] ?? ['estimates' => 0, 'approved' => 0, 'revenue' => 0.0, 'tickets' => 0, 'ticket_sum' => 0.0];
            $income = $this->money($ot['income']);
            $cost = $this->money($ot['cost']);
            $profit = $this->money($ot['profit']);

            $rows[] = [
                'advisor' => $users[$id]->name ?? "Usuario #{$id}",
                'estimates' => $est['estimates'],
                'approved' => $est['approved'],
                'approval_rate' => $est['estimates'] > 0 ? round($est['approved'] * 100 / $est['estimates'], 1) : 0.0,
                'revenue' => $this->money($est['revenue']),
                'ticket' => $est['tickets'] > 0 ? round($est['ticket_sum'] / $est['tickets'], 2) : 0.0,
                'work_orders' => $ot['work_orders'],
                'ot_income' => $income,
                'ot_cost' => $cost,
                'profit' => $profit,
                'margin' => $income > 0 ? round($profit * 100 / $income, 1) : 0.0,
            ];
        }

        // Asesores con presupuestos pero sin OTs atribuidas en el período.
        foreach ($estimatesByAdvisor as $id => $est) {
            if (isset($otByAdvisor[$id])) {
                continue;
            }

            $rows[] = [
                'advisor' => $users[$id]->name ?? "Usuario #{$id}",
                'estimates' => $est['estimates'],
                'approved' => $est['approved'],
                'approval_rate' => $est['estimates'] > 0 ? round($est['approved'] * 100 / $est['estimates'], 1) : 0.0,
                'revenue' => $this->money($est['revenue']),
                'ticket' => $est['tickets'] > 0 ? round($est['ticket_sum'] / $est['tickets'], 2) : 0.0,
                'work_orders' => 0,
                'ot_income' => 0.0,
                'ot_cost' => 0.0,
                'profit' => 0.0,
                'margin' => 0.0,
            ];
        }

        usort($rows, fn ($a, $b) => $b['profit'] <=> $a['profit']);

        $totalRevenue = array_sum(array_column($rows, 'revenue'));
        $totalProfit = array_sum(array_column($rows, 'profit'));
        $totalWos = array_sum(array_column($rows, 'work_orders'));

        return [
            'kpis' => [
                ['label' => 'Asesores activos', 'value' => number_format(count($rows), 0), 'sub' => 'con presupuestos u OTs', 'color' => 'blue'],
                ['label' => 'Presupuestos', 'value' => number_format(array_sum(array_column($rows, 'estimates')), 0), 'sub' => 'en el período', 'color' => 'indigo'],
                ['label' => 'Facturación estimada', 'value' => 'S/ '.number_format($totalRevenue, 2), 'sub' => 'presupuestos facturables', 'color' => 'green'],
                ['label' => 'Utilidad (OTs)', 'value' => 'S/ '.number_format($totalProfit, 2), 'sub' => $totalWos.' OTs atribuidas', 'color' => 'red'],
            ],
            'series' => [
                'profit' => collect($rows)->map(fn ($r) => ['name' => $r['advisor'], 'profit' => $r['profit'], 'income' => $r['ot_income']])->values()->all(),
                'ticket' => collect($rows)->map(fn ($r) => ['name' => $r['advisor'], 'ticket' => $r['ticket']])->sortByDesc('ticket')->values()->all(),
                'approval' => collect($rows)->map(fn ($r) => ['name' => $r['advisor'], 'rate' => $r['approval_rate']])->values()->all(),
            ],
            'rows' => $rows,
        ];
    }
    /* ----------------------------- 4. Seguimientos ----------------------------- */

    public function followUps(array $f = []): array
    {
        [$from, $to] = $this->range($f);

        $query = FollowUp::query()
            ->whereDate('date', '>=', $from)
            ->whereDate('date', '<=', $to)
            ->with(['party:id,business_name,first_name,last_name', 'vehicle:id,plate', 'creator:id,name']);

        if (! empty($f['type'])) {
            $query->where('type', $f['type']);
        }

        $state = $f['state'] ?? 'all';
        if ($state === 'pending') {
            $query->where('done', false);
        } elseif ($state === 'done') {
            $query->where('done', true);
        } elseif ($state === 'overdue') {
            $query->where('done', false)->whereDate('next_action_date', '<', now()->toDateString());
        }

        $est = $this->establishmentId($f);
        if ($est) {
            $query->whereIn('created_by', User::where('establishment_id', $est)->pluck('id'));
        }

        $items = $query->orderByDesc('date')->get();

        $rows = $items->map(function ($fu) {
            $overdue = $fu->next_action_date && ! $fu->done && $fu->next_action_date->lt(now()->startOfDay());

            return [
                'id' => $fu->id,
                'party' => $this->partyName($fu->party),
                'vehicle' => $fu->vehicle?->plate ?? '—',
                'type' => $fu->type,
                'type_label' => $fu->type_label,
                'date' => $fu->date?->format('Y-m-d') ?? '—',
                'next_action' => $fu->next_action_date?->format('Y-m-d') ?? '—',
                'done' => $fu->done,
                'overdue' => $overdue,
                'advisor' => $fu->creator?->name ?? '—',
            ];
        })->values()->all();

        $total = count($rows);
        $pending = count(array_filter($rows, fn ($r) => ! $r['done']));
        $overdue = count(array_filter($rows, fn ($r) => $r['overdue']));

        $byType = collect($rows)->groupBy('type_label')
            ->map(fn ($g) => ['name' => $g->first()['type_label'], 'count' => count($g)])
            ->sortByDesc('count')->values()->all();

        $byAdvisor = collect($rows)->groupBy('advisor')
            ->map(fn ($g, $name) => ['name' => $name, 'count' => count($g)])
            ->sortByDesc('count')->values()->all();

        $monthly = [];
        foreach ($rows as $r) {
            $key = substr((string) $r['date'], 0, 7);
            $monthly[$key] = ($monthly[$key] ?? 0) + 1;
        }
        ksort($monthly);

        $monthlySeries = collect($monthly)->map(fn ($count, $key) => [
            'name' => Carbon::createFromFormat('Y-m', $key)->translatedFormat('M Y'),
            'count' => $count,
        ])->values()->all();

        return [
            'kpis' => [
                ['label' => 'Seguimientos', 'value' => number_format($total, 0), 'sub' => 'en el período', 'color' => 'blue'],
                ['label' => 'Pendientes', 'value' => number_format($pending, 0), 'sub' => 'por concretar', 'color' => 'amber'],
                ['label' => 'Vencidos', 'value' => number_format($overdue, 0), 'sub' => 'con próxima acción vencida', 'color' => 'red'],
                ['label' => 'Tasa de cierre', 'value' => $total > 0 ? round(($total - $pending) * 100 / $total, 1).'%' : '0%', 'sub' => 'completados', 'color' => 'green'],
            ],
            'series' => [
                'types' => $byType,
                'advisors' => $byAdvisor,
                'monthly' => $monthlySeries,
            ],
            'rows' => $rows,
        ];
    }

    /* --------------------------- 5. Ingresos y cobranza --------------------------- */

    public function revenue(array $f = []): array
    {
        [$from, $to] = $this->range($f);

        $query = Invoice::query()
            ->whereDate('invoice_date', '>=', $from)
            ->whereDate('invoice_date', '<=', $to)
            ->with(['party:id,business_name,first_name,last_name']);
        $this->applyEstablishment($query, $f);

        $invoices = $query->get(['id', 'document_sn', 'party_id', 'document_type_code', 'invoice_date', 'currency', 'exchange_rate', 'total', 'status', 'invoice_type']);

        // Cobros por factura (morphMany + referencia directa invoice_id).
        $ids = $invoices->pluck('id');
        $paidByInvoice = [];
        if ($ids->isNotEmpty()) {
            $payments = Payment::query()
                ->where('direction', 'in')
                ->where(function ($q) use ($ids) {
                    $q->where(function ($q2) use ($ids) {
                        $q2->where('payable_type', Invoice::class)->whereIn('payable_id', $ids);
                    })->orWhereIn('invoice_id', $ids);
                })
                ->get(['invoice_id', 'payable_id', 'payable_type', 'amount', 'payment_date']);

            foreach ($payments as $p) {
                $invId = $p->payable_type === Invoice::class ? (int) $p->payable_id : (int) $p->invoice_id;
                if (! $invId) {
                    continue;
                }
                $paidByInvoice[$invId] = ($paidByInvoice[$invId] ?? 0) + (float) $p->amount;
            }
        }

        $rows = $invoices->map(function ($inv) use ($paidByInvoice) {
            $total = $this->toPen((float) $inv->total, $inv->currency, $inv->exchange_rate);
            $paid = $this->money($paidByInvoice[$inv->id] ?? 0);

            return [
                'id' => $inv->id,
                'document_sn' => $inv->document_sn,
                'doc_type' => $inv->doc_type_label,
                'party' => $this->partyName($inv->party),
                'invoice_type' => $inv->type_label,
                'date' => $inv->invoice_date?->format('Y-m-d') ?? '—',
                'total' => $this->money($total),
                'paid' => $paid,
                'balance' => $this->money($total - $paid),
                'status' => $inv->status,
                'status_label' => $inv->status_label,
            ];
        })->sortByDesc('date')->values()->all();

        $emitted = collect($rows)->filter(fn ($r) => ! in_array($r['status'], ['voided', 'rejected', 'draft'], true))->values();
        $facturado = $emitted->sum('total');
        $cobrado = $emitted->sum('paid');
        $pendiente = $this->money($facturado - $cobrado);

        $monthly = [];
        foreach ($emitted as $r) {
            $key = substr((string) $r['date'], 0, 7);
            $monthly[$key] ??= ['facturado' => 0.0, 'cobrado' => 0.0];
            $monthly[$key]['facturado'] += $r['total'];
            $monthly[$key]['cobrado'] += $r['paid'];
        }
        ksort($monthly);

        $monthlySeries = [];
        foreach ($monthly as $key => $m) {
            $monthlySeries[] = [
                'name' => Carbon::createFromFormat('Y-m', $key)->translatedFormat('M Y'),
                'facturado' => $this->money($m['facturado']),
                'cobrado' => $this->money($m['cobrado']),
            ];
        }

        $byType = collect($rows)->groupBy('doc_type')
            ->map(fn ($g) => ['name' => $g->first()['doc_type'], 'count' => count($g), 'total' => $this->money($g->sum('total'))])
            ->values()->all();

        $byParty = $emitted->groupBy('party')
            ->map(fn ($g) => ['name' => $g->first()['party'], 'total' => $this->money($g->sum('total'))])
            ->sortByDesc('total')->take(10)->values()->all();

        return [
            'kpis' => [
                ['label' => 'Comprobantes emitidos', 'value' => number_format(count($emitted), 0), 'sub' => 'no anulados', 'color' => 'blue'],
                ['label' => 'Facturado', 'value' => 'S/ '.number_format($facturado, 2), 'sub' => 'total emitido en PEN', 'color' => 'green'],
                ['label' => 'Cobrado', 'value' => 'S/ '.number_format($cobrado, 2), 'sub' => 'pagos recibidos', 'color' => 'indigo'],
                ['label' => 'Pendiente de cobro', 'value' => 'S/ '.number_format($pendiente, 2), 'sub' => 'saldo por cobrar', 'color' => 'red'],
            ],
            'series' => [
                'monthly' => $monthlySeries,
                'types' => $byType,
                'parties' => $byParty,
            ],
            'rows' => $rows,
        ];
    }

    /* ---------------------------- 6. Repuestos utilizados ---------------------------- */

    public function partsUsage(array $f = []): array
    {
        [$from, $to] = $this->range($f);

        $query = EstimateItem::query()
            ->where('item_type', 'part')
            ->join('estimates', 'estimates.id', '=', 'estimate_items.estimate_id')
            ->join('vehicles', 'vehicles.id', '=', 'estimates.vehicle_id')
            ->leftJoin('part_categories', 'part_categories.id', '=', 'estimate_items.part_category_id')
            ->leftJoin('brands', 'brands.id', '=', 'vehicles.brand_id')
            ->whereNull('estimates.deleted_at')
            ->whereDate('estimates.created_at', '>=', $from)
            ->whereDate('estimates.created_at', '<=', $to);
        $this->applyEstablishment($query, $f, 'estimates.establishment_id');

        if (! empty($f['brand_id'])) {
            $query->where('vehicles.brand_id', (int) $f['brand_id']);
        }

        $items = $query->get([
            'estimate_items.id',
            'estimate_items.description',
            'estimate_items.quantity',
            'estimate_items.cost_price',
            'estimate_items.net_line',
            'estimates.currency',
            'estimates.exchange_rate',
            'estimates.created_at',
            'part_categories.name as category',
            'brands.name as brand',
        ]);

        $parts = [];
        $categories = [];
        $brands = [];
        $monthly = [];

        foreach ($items as $item) {
            $name = trim((string) $item->description) ?: 'Repuesto sin descripción';
            $category = $item->category ?: 'Sin categoría';
            $brand = $item->brand ?: 'Sin marca';
            $cost = $this->toPen((float) $item->cost_price * (float) $item->quantity, $item->currency, $item->exchange_rate);
            $sale = $this->toPen((float) $item->net_line, $item->currency, $item->exchange_rate);
            $month = $item->created_at->format('Y-m');

            $parts[$name] ??= ['name' => $name, 'category' => $category, 'lines' => 0, 'units' => 0.0, 'cost' => 0.0, 'sale' => 0.0];
            $parts[$name]['lines']++;
            $parts[$name]['units'] += (float) $item->quantity;
            $parts[$name]['cost'] += $cost;
            $parts[$name]['sale'] += $sale;

            $categories[$category] = ($categories[$category] ?? 0) + (float) $item->quantity;
            $brands[$brand] = ($brands[$brand] ?? 0) + (float) $item->quantity;
            $monthly[$month] = ($monthly[$month] ?? 0) + 1;
        }

        $rows = collect($parts)->map(fn ($p) => [
            'name' => $p['name'],
            'category' => $p['category'],
            'lines' => $p['lines'],
            'units' => $p['units'],
            'cost' => $this->money($p['cost']),
            'sale' => $this->money($p['sale']),
        ])->sortByDesc('lines')->values()->all();

        $totalUnits = array_sum(array_column($rows, 'units'));
        $totalCost = array_sum(array_column($rows, 'cost'));
        $totalSale = array_sum(array_column($rows, 'sale'));

        $monthlySeries = collect($monthly)->sortKeys()->map(fn ($count, $key) => [
            'name' => Carbon::createFromFormat('Y-m', $key)->translatedFormat('M Y'),
            'count' => $count,
        ])->values()->all();

        return [
            'kpis' => [
                ['label' => 'Líneas de repuesto', 'value' => number_format(count($items), 0), 'sub' => 'en presupuestos del período', 'color' => 'blue'],
                ['label' => 'Unidades', 'value' => number_format($totalUnits, 1), 'sub' => 'total de unidades', 'color' => 'indigo'],
                ['label' => 'Costo total', 'value' => 'S/ '.number_format($totalCost, 2), 'sub' => 'valor de compra (PEN)', 'color' => 'red'],
                ['label' => 'Valor de venta', 'value' => 'S/ '.number_format($totalSale, 2), 'sub' => 'neto facturado (PEN)', 'color' => 'green'],
            ],
            'series' => [
                'top' => collect($parts)->sortByDesc('units')->take(12)->values()->map(fn ($p) => ['name' => $p['name'], 'units' => $p['units']])->all(),
                'categories' => collect($categories)->map(fn ($units, $name) => ['name' => $name, 'units' => $units])->sortByDesc('units')->values()->all(),
                'brands' => collect($brands)->map(fn ($units, $name) => ['name' => $name, 'units' => $units])->sortByDesc('units')->values()->all(),
                'monthly' => $monthlySeries,
            ],
            'rows' => $rows,
        ];
    }
}