<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProviderSettlementRequest;
use App\Models\CompanySetting;
use App\Models\ProviderSettlement;
use App\Models\ServiceVoucher;
use App\Services\ProviderSettlementService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;

class ProviderSettlementController extends Controller
{
    public function __construct(protected ProviderSettlementService $service)
    {
    }

    public function index(): View
    {
        Gate::authorize('viewAny', ProviderSettlement::class);

        return view('provider-settlements.index');
    }

    public function create(): View
    {
        Gate::authorize('create', ProviderSettlement::class);

        $setting = CompanySetting::get();

        return view('provider-settlements.create', [
            'igvRate' => $setting?->igv_rate ?? 0.18,
            'detractionRate' => $setting?->detraccion_rate ?? 0.12,
        ]);
    }

    public function store(ProviderSettlementRequest $request): RedirectResponse
    {
        Gate::authorize('create', ProviderSettlement::class);

        $settlement = $this->service->create($request->validated());

        return redirect()->route('provider-settlements.show', $settlement)
            ->with('success', "Liquidación {$settlement->document_sn} creada correctamente.");
    }

    public function show(ProviderSettlement $providerSettlement): View
    {
        Gate::authorize('view', $providerSettlement);

        $providerSettlement->load([
            'provider',
            'vouchers.workOrder.vehicle',
            'vouchers.provider',
            'documentSeries.documentType',
            'approvedBy',
            'paidBy',
            'statusHistory',
        ]);

        return view('provider-settlements.show', ['settlement' => $providerSettlement]);
    }

    public function edit(ProviderSettlement $providerSettlement): View
    {
        Gate::authorize('update', $providerSettlement);

        $setting = CompanySetting::get();

        return view('provider-settlements.edit', [
            'settlement' => $providerSettlement,
            'igvRate' => $setting?->igv_rate ?? 0.18,
            'detractionRate' => $setting?->detraccion_rate ?? 0.12,
        ]);
    }

    public function update(ProviderSettlementRequest $request, ProviderSettlement $providerSettlement): RedirectResponse
    {
        Gate::authorize('update', $providerSettlement);

        try {
            $this->service->update($providerSettlement, $request->validated());
        } catch (\RuntimeException $e) {
            return redirect()->back()->withInput()->with('error', $e->getMessage());
        }

        return redirect()->route('provider-settlements.show', $providerSettlement)
            ->with('success', 'Liquidación actualizada correctamente.');
    }

    public function destroy(ProviderSettlement $providerSettlement): RedirectResponse
    {
        Gate::authorize('delete', $providerSettlement);

        try {
            $this->service->delete($providerSettlement);
        } catch (\RuntimeException $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }

        return redirect()->route('provider-settlements.index')
            ->with('success', 'Liquidación eliminada correctamente.');
    }

    public function approve(ProviderSettlement $providerSettlement): RedirectResponse
    {
        Gate::authorize('update', $providerSettlement);

        try {
            $this->service->approve($providerSettlement);
        } catch (\RuntimeException $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }

        return redirect()->back()->with('success', 'Liquidación aprobada.');
    }

    public function pay(ProviderSettlement $providerSettlement): RedirectResponse
    {
        Gate::authorize('update', $providerSettlement);

        try {
            $this->service->pay($providerSettlement);
        } catch (\RuntimeException $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }

        return redirect()->back()->with('success', 'Liquidación pagada y comprobantes marcados como liquidados.');
    }

    public function detachVoucher(ProviderSettlement $providerSettlement, ServiceVoucher $serviceVoucher): RedirectResponse
    {
        Gate::authorize('update', $providerSettlement);

        try {
            $this->service->detachVoucher($providerSettlement, $serviceVoucher);
        } catch (\RuntimeException $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }

        return redirect()->back()->with('success', 'Comprobante retirado de la liquidación.');
    }

    public function search(Request $request): JsonResponse
    {
        Gate::authorize('viewAny', ProviderSettlement::class);

        $query = ProviderSettlement::query()
            ->with(['provider', 'documentSeries.documentType'])
            ->when($request->filled('q'), function ($q) use ($request) {
                $term = $request->query('q');
                $q->where(function ($sub) use ($term) {
                    $sub->where('document_sn', 'like', "%{$term}%")
                        ->orWhereHas('provider', fn ($p) => $p->where('business_name', 'like', "%{$term}%")
                            ->orWhere('first_name', 'like', "%{$term}%")
                            ->orWhere('last_name', 'like', "%{$term}%")
                            ->orWhere('document_number', 'like', "%{$term}%"));
                });
            })
            ->when($request->filled('status'), fn ($q) => $q->where('status', $request->query('status')))
            ->orderByDesc('id')
            ->limit($request->integer('limit', 100));

        return response()->json($query->get()->map(fn (ProviderSettlement $s) => [
            'id' => $s->id,
            'document_sn' => $s->document_sn,
            'provider_name' => $s->provider?->display_name,
            'period' => ($s->period_start?->format('d/m/Y') ?: '—') . ' — ' . ($s->period_end?->format('d/m/Y') ?: '—'),
            'subtotal' => round($s->subtotal, 2),
            'total_with_igv' => round($s->total_with_igv, 2),
            'detraction_amount' => round($s->detraction_amount, 2),
            'total_payable' => round($s->total_payable, 2),
            'status' => $s->status,
            'vouchers_count' => $s->vouchers()->count(),
        ]));
    }

    /**
     * Vales completados y aún no liquidados de un proveedor (para la liquidación).
     */
    public function availableVouchers(Request $request): JsonResponse
    {
        Gate::authorize('viewAny', ProviderSettlement::class);

        $vouchers = ServiceVoucher::query()
            ->with(['workOrder.vehicle'])
            ->where('provider_id', $request->integer('provider_id'))
            ->where('status', ServiceVoucher::STATUS_COMPLETED)
            ->where(function ($q) use ($request) {
                $q->whereNull('provider_settlement_id');
                if ($request->filled('settlement_id')) {
                    $q->orWhere('provider_settlement_id', $request->query('settlement_id'));
                }
            })
            ->when($request->filled('from'), fn ($q) => $q->whereDate('execution_date', '>=', $request->query('from')))
            ->when($request->filled('to'), fn ($q) => $q->whereDate('execution_date', '<=', $request->query('to')))
            ->orderBy('execution_date')
            ->get();

        return response()->json($vouchers->map(fn (ServiceVoucher $v) => [
            'id' => $v->id,
            'document_sn' => $v->document_sn,
            'execution_date' => $v->execution_date?->format('d/m/Y'),
            'description' => $v->description,
            'plate' => $v->workOrder?->vehicle?->plate,
            'base_amount' => round($v->base_amount, 2),
        ]));
    }
}

