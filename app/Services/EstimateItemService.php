<?php

namespace App\Services;

use App\Models\EstimateItem;
use App\Models\Part;
use App\Models\RepairService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use RuntimeException;

/**
 * Vínculo diferido de líneas libres del presupuesto al catálogo.
 *
 * Una línea libre (sin service_id/part_id) conserva su snapshot inmutable
 * (descripción, precios, IVA) y este servicio solo asigna la FK al catálogo,
 * para trazabilidad con stock, PartOrder y OT sin alterar lo cotizado.
 */
class EstimateItemService
{
    public function linkToPart(EstimateItem $item, int $partId): EstimateItem
    {
        return DB::transaction(function () use ($item, $partId) {
            $this->assertLinkable($item, 'part');
            $part = Part::findOrFail($partId);

            $item->update(['part_id' => $part->id]);

            $this->log($item, "Ítem vinculado al repuesto de catálogo \"{$part->name}\" (SKU {$part->sku}).");

            return $item->fresh();
        });
    }

    public function linkToService(EstimateItem $item, int $serviceId): EstimateItem
    {
        return DB::transaction(function () use ($item, $serviceId) {
            $this->assertLinkable($item, 'service');
            $service = RepairService::findOrFail($serviceId);

            $item->update(['service_id' => $service->id]);

            $this->log($item, "Ítem vinculado al servicio de catálogo \"{$service->name}\".");

            return $item->fresh();
        });
    }

    public function unlink(EstimateItem $item): EstimateItem
    {
        return DB::transaction(function () use ($item) {
            $this->assertLinkable($item, $item->item_type);

            $item->update(['part_id' => null, 'service_id' => null]);

            $this->log($item, 'Ítem desvinculado del catálogo.');

            return $item->fresh();
        });
    }

    protected function assertLinkable(EstimateItem $item, string $type): void
    {
        $estimate = $item->estimate;

        if (! $estimate) {
            throw new RuntimeException('El presupuesto asociado al ítem no existe.');
        }

        if ($estimate->status === 'finalized') {
            throw new RuntimeException('No se puede vincular un ítem de un presupuesto finalizado.');
        }

        if ($item->item_type !== $type) {
            throw new RuntimeException($type === 'part'
                ? 'La línea no es de tipo repuesto.'
                : 'La línea no es de tipo servicio.');
        }
    }

    protected function log(EstimateItem $item, string $message): void
    {
        $estimate = $item->estimate;

        if (! $estimate) {
            return;
        }

        activity()
            ->performedOn($estimate)
            ->causedBy(Auth::id())
            ->withProperties([
                'estimate_item_id' => $item->id,
                'item_type' => $item->item_type,
                'description' => $item->description,
            ])
            ->log($message);
    }
}
