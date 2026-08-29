<?php

namespace App\Http\Controllers;

use App\Http\Requests\ServiceVoucherRequest;
use App\Models\CompanySetting;
use App\Models\ServiceVoucher;
use App\Services\ServiceVoucherService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;

class ServiceVoucherController extends Controller
{
    public function __construct(protected ServiceVoucherService $service)
    {
    }

    public function index(): View
    {
        Gate::authorize('viewAny', ServiceVoucher::class);

        return view('service-vouchers.index');
    }

    public function create(): View
    {
        Gate::authorize('create', ServiceVoucher::class);

        $setting = CompanySetting::get();

        return view('service-vouchers.create', [
            'igvRate' => $setting?->igv_rate ?? 0.18,
            'detractionRate' => $setting?->detraccion_rate ?? 0.12,
        ]);
    }

    public function store(ServiceVoucherRequest $request): RedirectResponse
    {
        Gate::authorize('create', ServiceVoucher::class);

        $voucher = $this->service->create($request->validated());

        return redirect()->route('service-vouchers.show', $voucher)
            ->with('success', "Comprobante {$voucher->document_sn} emitido correctamente.");
    }

    public function show(ServiceVoucher $serviceVoucher): View
    {
        Gate::authorize('view', $serviceVoucher);

        $serviceVoucher->load([
            'workOrder.vehicle.vehicleModel.brand',
            'provider',
            'settlement',
            'documentSeries.documentType',
            'createdBy',
            'statusHistory',
        ]);

        return view('service-vouchers.show', ['voucher' => $serviceVoucher]);
    }

    public function edit(ServiceVoucher $serviceVoucher): View
    {
        Gate::authorize('update', $serviceVoucher);

        $setting = CompanySetting::get();

        return view('service-vouchers.edit', [
            'voucher' => $serviceVoucher,
            'igvRate' => $setting?->igv_rate ?? 0.18,
            'detractionRate' => $setting?->detraccion_rate ?? 0.12,
        ]);
    }

    public function update(ServiceVoucherRequest $request, ServiceVoucher $serviceVoucher): RedirectResponse
    {
        Gate::authorize('update', $serviceVoucher);

        try {
            $this->service->update($serviceVoucher, $request->validated());
        } catch (\RuntimeException $e) {
            return redirect()->back()->withInput()->with('error', $e->getMessage());
        }

        return redirect()->route('service-vouchers.show', $serviceVoucher)
            ->with('success', 'Comprobante actualizado correctamente.');
    }

    public function destroy(ServiceVoucher $serviceVoucher): RedirectResponse
    {
        Gate::authorize('delete', $serviceVoucher);

        try {
            $this->service->delete($serviceVoucher);
        } catch (\RuntimeException $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }

        return redirect()->route('service-vouchers.index')
            ->with('success', 'Comprobante eliminado correctamente.');
    }

    public function complete(ServiceVoucher $serviceVoucher): RedirectResponse
    {
        Gate::authorize('update', $serviceVoucher);

        $this->service->complete($serviceVoucher);

        return redirect()->back()->with('success', 'Comprobante marcado como completado.');
    }

    public function search(Request $request): JsonResponse
    {
        Gate::authorize('viewAny', ServiceVoucher::class);

        $query = ServiceVoucher::query()
            ->with(['workOrder.vehicle', 'provider', 'settlement'])
            ->when($request->filled('q'), function ($q) use ($request) {
                $term = $request->query('q');
                $q->where(function ($sub) use ($term) {
                    $sub->where('document_sn', 'like', "%{$term}%")
                        ->orWhere('document_serie', 'like', "%{$term}%")
                        ->orWhereHas('provider', fn ($p) => $p->where('business_name', 'like', "%{$term}%")
                            ->orWhere('first_name', 'like', "%{$term}%")
                            ->orWhere('last_name', 'like', "%{$term}%")
                            ->orWhere('document_number', 'like', "%{$term}%"))
                        ->orWhereHas('workOrder.vehicle', fn ($v) => $v->where('plate', 'like', "%{$term}%"));
                });
            })
            ->when($request->filled('status'), fn ($q) => $q->where('status', $request->query('status')))
            ->when($request->filled('provider_id'), fn ($q) => $q->where('provider_id', $request->query('provider_id')))
            ->when($request->filled('settlement_id'), fn ($q) => $q->where('provider_settlement_id', $request->query('settlement_id')))
            ->orderByDesc('id')
            ->limit($request->integer('limit', 100));

        return response()->json($query->get()->map(fn (ServiceVoucher $v) => [
            'id' => $v->id,
            'document_sn' => $v->document_sn,
            'execution_date' => $v->execution_date?->format('d/m/Y'),
            'provider_name' => $v->provider?->display_name,
            'provider_document' => $v->provider?->document_number,
            'plate' => $v->workOrder?->vehicle?->plate,
            'work_order_sn' => $v->workOrder?->document_sn,
            'base_amount' => round($v->base_amount, 2),
            'igv_amount' => round($v->igv_amount, 2),
            'total_with_igv' => round($v->total_with_igv, 2),
            'detraction_amount' => round($v->detraction_amount, 2),
            'total_payable' => round($v->total_payable, 2),
            'status' => $v->status,
            'settlement_sn' => $v->settlement?->document_sn,
            'settlement_status' => $v->settlement?->status,
        ]));
    }
}
