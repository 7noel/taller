<?php

namespace App\Services;

use App\Models\CompanySetting;
use App\Models\Estimate;
use App\Models\Establishment;
use Illuminate\Support\Facades\Auth;

class EstimateCalculationService
{
    /**
     * Recalcula los totales de cada ítem y de la cabecera del presupuesto.
     *
     * Criterio SUNAT: el IGV se calcula UNA sola vez sobre la base imponible
     * final (después de descuentos de línea, global y adicionales). Los campos
     * iva_line y total_line de cada ítem se guardan como valores informativos
     * (redondeo por línea); el total autoritativo es el de la cabecera.
     */
    public function calculate(Estimate $estimate): void
    {
        $igvRate = $this->getIgvRate($estimate);

        $subtotalTotal = 0.0;
        $linesDiscountTotal = 0.0;

        $items = $estimate->items()->orderBy('sort_order')->get();

        foreach ($items as $item) {
            $quantity = round((float) $item->quantity, 2);
            $unitPrice = round((float) $item->unit_price, 4);
            $discountPct = max(0, min(100, (float) $item->discount_pct));

            $subtotal = round($quantity * $unitPrice, 2);
            $discountAmount = round($subtotal * ($discountPct / 100), 2);
            $netLine = round($subtotal - $discountAmount, 2);
            $ivaLine = round($netLine * $igvRate, 2);
            $totalLine = round($netLine + $ivaLine, 2);

            $item->updateQuietly([
                'subtotal' => $subtotal,
                'discount_amount' => $discountAmount,
                'net_line' => $netLine,
                'iva_line' => $ivaLine,
                'total_line' => $totalLine,
            ]);

            $subtotalTotal += $subtotal;
            $linesDiscountTotal += $discountAmount;
        }

        // Descuento global de cabecera (si existe), aplicado sobre el neto
        // tras descuentos por ítem (criterio SUNAT: la base es el valor venta).
        $netAfterLines = round($subtotalTotal - $linesDiscountTotal, 2);
        $globalDiscount = $this->resolveGlobalDiscount($estimate, $netAfterLines);

        // Descuentos adicionales registrados (promotion / insurance / other).
        $additionalDiscount = $this->additionalDiscountsAmount($estimate);

        $subtotalTotal = round($subtotalTotal, 2);
        $linesDiscountTotal = round($linesDiscountTotal, 2);
        $globalDiscount = round($globalDiscount, 2);
        $additionalDiscount = round($additionalDiscount, 2);

        $totalDiscount = round($linesDiscountTotal + $globalDiscount + $additionalDiscount, 2);
        $taxableBase = round($subtotalTotal - $totalDiscount, 2);
        $taxableBase = max(0, $taxableBase);

        $iva = round($taxableBase * $igvRate, 2);
        $total = round($taxableBase + $iva, 2);

        $estimate->updateQuietly([
            'subtotal' => $subtotalTotal,
            'discount' => $totalDiscount,
            'taxable_base' => $taxableBase,
            'iva' => $iva,
            'total' => $total,
        ]);

        $this->syncDiscountReflections($estimate, $linesDiscountTotal, $globalDiscount);
    }

    /**
     * Obtiene la tasa de IGV: establecimiento → company setting → 0.18.
     */
    public function getIgvRate(Estimate $estimate): float
    {
        $establishment = $estimate->establishment()->first();

        if ($establishment && $establishment->igv_rate > 0) {
            return (float) $establishment->igv_rate;
        }

        $setting = CompanySetting::get();
        if ($setting && $setting->igv_rate > 0) {
            return (float) $setting->igv_rate;
        }

        return 0.18;
    }

    /**
     * Suma los descuentos adicionales (promotion, insurance, other) aplicados al
     * subtotal. Son registros creados manualmente o por otros módulos; NO son
     * reflejos calculados (global/line).
     */
    protected function additionalDiscountsAmount(Estimate $estimate): float
    {
        return (float) $estimate->discounts()
            ->whereIn('source', ['promotion', 'insurance', 'other'])
            ->where('applied_to', 'subtotal')
            ->sum('amount');
    }

    /**
     * Calcula el monto del descuento global aplicado sobre la base ya
     * descontada por línea (neto tras ítems).
     */
    protected function resolveGlobalDiscount(Estimate $estimate, float $subtotalTotal): float
    {
        $subtotalTotal = round($subtotalTotal, 2);
        $type = $estimate->global_discount_type;
        $value = (float) $estimate->global_discount_value;

        if (!$type || $value <= 0 || $subtotalTotal <= 0) {
            return 0.0;
        }

        if ($type === 'percentage') {
            $value = max(0, min(100, $value));
            $discount = round($subtotalTotal * ($value / 100), 2);
        } else {
            // fixed
            $discount = round($value, 2);
        }

        // Limitar el descuento al subtotal para no generar base negativa.
        return max(0, min($discount, $subtotalTotal));
    }

    /**
     * Calcula los totales a partir de un array de ítems sin persistir
     * (vista previa en el frontend). Devuelve los totales de cabecera y los
     * totales por línea con el mismo criterio que calculate() (sin descuentos
     * adicionales, que no existen aún en la vista previa).
     */
    public function preview(array $items, ?string $globalDiscountType, float $globalDiscountValue, ?int $establishmentId = null): array
    {
        $igvRate = $this->getIgvRateForEstablishment($establishmentId);

        $subtotalTotal = 0.0;
        $linesDiscountTotal = 0.0;
        $lines = [];

        foreach ($items as $item) {
            $quantity = round((float) ($item['quantity'] ?? 0), 2);
            $unitPrice = round((float) ($item['unit_price'] ?? 0), 4);
            $discountPct = max(0, min(100, (float) ($item['discount_pct'] ?? 0)));

            $subtotal = round($quantity * $unitPrice, 2);
            $discountAmount = round($subtotal * ($discountPct / 100), 2);
            $netLine = round($subtotal - $discountAmount, 2);
            $ivaLine = round($netLine * $igvRate, 2);
            $totalLine = round($netLine + $ivaLine, 2);

            $lines[] = [
                'subtotal' => $subtotal,
                'discount_amount' => $discountAmount,
                'net_line' => $netLine,
                'iva_line' => $ivaLine,
                'total_line' => $totalLine,
            ];

            $subtotalTotal += $subtotal;
            $linesDiscountTotal += $discountAmount;
        }

        // Mismo criterio SUNAT: el % global se aplica sobre el neto tras ítems.
        $netAfterLines = round($subtotalTotal - $linesDiscountTotal, 2);
        $globalDiscount = $this->previewGlobalDiscount($globalDiscountType, $globalDiscountValue, $netAfterLines);

        $subtotalTotal = round($subtotalTotal, 2);
        $linesDiscountTotal = round($linesDiscountTotal, 2);
        $globalDiscount = round($globalDiscount, 2);

        $totalDiscount = round($linesDiscountTotal + $globalDiscount, 2);
        $taxableBase = max(0, round($subtotalTotal - $totalDiscount, 2));
        $iva = round($taxableBase * $igvRate, 2);
        $total = round($taxableBase + $iva, 2);

        return [
            'subtotal' => $subtotalTotal,
            'discount' => $totalDiscount,
            'taxable_base' => $taxableBase,
            'iva' => $iva,
            'total' => $total,
            'igv_rate' => $igvRate,
            'lines' => $lines,
        ];
    }

    /**
     * Obtiene la tasa de IGV sin un modelo Estimate (para la vista previa).
     */
    protected function getIgvRateForEstablishment(?int $establishmentId): float
    {
        if ($establishmentId) {
            $establishment = Establishment::find($establishmentId);
            if ($establishment && $establishment->igv_rate > 0) {
                return (float) $establishment->igv_rate;
            }
        }

        $setting = CompanySetting::get();
        if ($setting && $setting->igv_rate > 0) {
            return (float) $setting->igv_rate;
        }

        return 0.18;
    }

    /**
     * Calcula el descuento global para la vista previa.
     */
    protected function previewGlobalDiscount(?string $type, float $value, float $subtotalTotal): float
    {
        $subtotalTotal = round($subtotalTotal, 2);

        if (!$type || $value <= 0 || $subtotalTotal <= 0) {
            return 0.0;
        }

        if ($type === 'percentage') {
            $value = max(0, min(100, $value));
            $discount = round($subtotalTotal * ($value / 100), 2);
        } else {
            $discount = round($value, 2);
        }

        return max(0, min($discount, $subtotalTotal));
    }

    /**
     * Sincroniza los reflejos calculados del descuento global y de los
     * descuentos por línea en estimate_discounts (trazabilidad). No se usa
     * borrar-todo: se actualiza el registro existente por (source, applied_to)
     * o se crea si no existe. Los descuentos adicionales (promotion/insurance/
     * other) NO se tocan aquí.
     */
    protected function syncDiscountReflections(Estimate $estimate, float $lineAmount, float $globalAmount): void
    {
        $userId = Auth::id() ?? $estimate->created_by;

        // Reflejo del descuento global de cabecera.
        $type = $estimate->global_discount_type;
        $value = (float) $estimate->global_discount_value;

        if ($type && $value > 0) {
            $estimate->discounts()->updateOrCreate(
                ['estimate_id' => $estimate->id, 'source' => 'global', 'applied_to' => 'subtotal'],
                [
                    'type' => $type,
                    'value' => round($value, 2),
                    'amount' => round($globalAmount, 2),
                    'created_by' => $userId,
                ]
            );
        } else {
            $estimate->discounts()
                ->where('source', 'global')
                ->where('applied_to', 'subtotal')
                ->delete();
        }

        // Reflejo agregado de los descuentos por línea.
        if ($lineAmount > 0) {
            $estimate->discounts()->updateOrCreate(
                ['estimate_id' => $estimate->id, 'source' => 'line', 'applied_to' => 'subtotal'],
                [
                    'type' => 'fixed',
                    'value' => round($lineAmount, 2),
                    'amount' => round($lineAmount, 2),
                    'created_by' => $userId,
                ]
            );
        } else {
            $estimate->discounts()
                ->where('source', 'line')
                ->where('applied_to', 'subtotal')
                ->delete();
        }
    }
}