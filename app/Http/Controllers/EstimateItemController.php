<?php

namespace App\Http\Controllers;

use App\Http\Requests\LinkEstimateItemRequest;
use App\Models\Estimate;
use App\Models\EstimateItem;
use App\Models\Part;
use App\Services\EstimateItemService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class EstimateItemController extends Controller
{
    protected EstimateItemService $service;

    public function __construct(EstimateItemService $service)
    {
        $this->service = $service;
    }

    public function linkPart(LinkEstimateItemRequest $request, EstimateItem $estimateItem): JsonResponse
    {
        abort_unless($estimateItem->estimate, 422, 'El ítem no tiene presupuesto asociado.');
        Gate::authorize('update', $estimateItem->estimate);

        $item = $this->service->linkToPart($estimateItem, (int) $request->input('part_id'));

        return response()->json(['ok' => true, 'item' => $this->itemPayload($item)]);
    }

    public function linkService(LinkEstimateItemRequest $request, EstimateItem $estimateItem): JsonResponse
    {
        abort_unless($estimateItem->estimate, 422, 'El ítem no tiene presupuesto asociado.');
        Gate::authorize('update', $estimateItem->estimate);

        $item = $this->service->linkToService($estimateItem, (int) $request->input('service_id'));

        return response()->json(['ok' => true, 'item' => $this->itemPayload($item)]);
    }

    public function unlink(EstimateItem $estimateItem): JsonResponse
    {
        abort_unless($estimateItem->estimate, 422, 'El ítem no tiene presupuesto asociado.');
        Gate::authorize('update', $estimateItem->estimate);

        $item = $this->service->unlink($estimateItem);

        return response()->json(['ok' => true, 'item' => $this->itemPayload($item)]);
    }

    /**
     * Líneas de presupuesto de tipo repuesto aún sin vínculo a catálogo,
     * para la pantalla de recepción/registro de repuestos.
     */
    public function unlinkedParts(Request $request): JsonResponse
    {
        Gate::authorize('create', Part::class);

        $query = EstimateItem::query()
            ->where('item_type', 'part')
            ->whereNull('part_id')
            ->whereHas('estimate')
            ->with(['estimate.vehicle.vehicleModel.brand', 'estimate.client'])
            ->when($request->filled('q'), fn ($q) => $q->where('description', 'like', '%'.$request->query('q').'%'))
            ->orderByDesc('id')
            ->limit($request->integer('limit', 100));

        $items = $query->get();

        return response()->json($items->map(fn (EstimateItem $i) => [
            'id' => $i->id,
            'description' => $i->description,
            'quantity' => (float) $i->quantity,
            'uom' => $i->uom,
            'estimate_id' => $i->estimate?->id,
            'estimate_sn' => $i->estimate?->document_sn,
            'estimate_status' => $i->estimate?->status,
            'estimate_status_label' => $i->estimate ? (Estimate::STATUS_LABELS[$i->estimate->status] ?? $i->estimate->status) : null,
            'vehicle' => $i->estimate?->vehicle
                ? trim(($i->estimate->vehicle->plate ?? '').' · '.($i->estimate->vehicle->vehicleModel?->brand?->name ?? '').' '.($i->estimate->vehicle->vehicleModel?->name ?? ''))
                : null,
            'client' => $i->estimate?->client?->display_name,
        ]));
    }

    protected function itemPayload(EstimateItem $item): array
    {
        $item->loadMissing(['part', 'service']);

        return [
            'id' => $item->id,
            'part_id' => $item->part_id,
            'service_id' => $item->service_id,
            'catalog_name' => $item->part?->name ?? $item->service?->name ?? null,
        ];
    }
}
