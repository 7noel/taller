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

        // Franquicia: NO descuenta del total del presupuesto. Es informativa y
        // se calcula a nivel de GRUPO (siniestro + ampliaciones + sus OC de
        // terceros). El resultado vive en el presupuesto principal; en una
        // ampliación se limpia la franquicia local (se recalcula el principal).
        $estimate->updateQuietly([
            'subtotal' => $subtotalTotal,
            'discount' => $totalDiscount,
            'taxable_base' => $taxableBase,
            'iva' => $iva,
            'total' => $total,
        ]);

        $this->applyFranchise($estimate);

        $this->syncDiscountReflections($estimate, $linesDiscountTotal, $globalDiscount);
    }

    /**
     * Aplica la franquicia según el rol del presupuesto dentro del grupo:
     *  - Principal (raíz): calcula la franquicia del GRUPO completo.
     *  - Ampliación: limpia sus campos franchise_* (viven en el principal) y
     *    dispara el recálculo de la franquicia del grupo en su presupuesto padre.
     */
    protected function applyFranchise(Estimate $estimate): void
    {
        if ($estimate->is_ampliacion) {
            $estimate->updateQuietly([
                'franchise_minimum_amount' => null,
                'franchise_percentage' => null,
                'franchise_minimum_includes_tax' => false,
                'franchise_minimum_without_tax' => null,
                'franchise_base' => null,
                'franchise_percentage_applied' => null,
                'franchise_amount' => null,
            ]);

            $root = $estimate->parent;

            if ($root) {
                $this->calculateGroupFranchise($root);
            }

            return;
        }

        $this->calculateGroupFranchise($estimate);
    }

    /**
     * Calcula la franquicia del grupo completo (presupuesto principal + sus
     * ampliaciones + TODAS las órdenes de compra de terceros del grupo) y la
     * persiste en el presupuesto principal.
     *
     * Reglas:
     *  - base = Σ taxable_base del grupo + Σ amount_without_iva de OC del grupo.
     *  - minimum_without_tax = minimum_amount / (1 + igv) si incluye IGV.
     *  - percentage_applied = base * (percentage / 100).
     *  - franquicia = max(minimum_without_tax, percentage_applied).
     * Los parámetros (mínimo, porcentaje) son los del presupuesto principal.
     */
    public function calculateGroupFranchise(Estimate $root): void
    {
        $group = collect([$root])->merge($root->ampliaciones()->get());

        $igvRate = $this->getIgvRate($root);

        $taxableBaseSum = 0.0;
        $ordersTotal = 0.0;

        foreach ($group as $estimate) {
            $taxableBaseSum += (float) $estimate->taxable_base;
            $ordersTotal += (float) $estimate->thirdPartyOrders()->sum('amount_without_iva');
        }

        $base = round($taxableBaseSum + $ordersTotal, 2);

        $minimumAmount = (float) ($root->franchise_minimum_amount ?? 0);
        $percentage = (float) ($root->franchise_percentage ?? 0);
        $includesTax = (bool) ($root->franchise_minimum_includes_tax ?? false);

        $minimumWithoutTax = null;
        if ($minimumAmount > 0) {
            $minimumWithoutTax = $includesTax
                ? round($minimumAmount / (1 + $igvRate), 2)
                : round($minimumAmount, 2);
        }

        $percentageApplied = null;
        if ($percentage > 0 && $base > 0) {
            $percentageApplied = round($base * ($percentage / 100), 2);
        }

        $amount = null;
        if ($minimumWithoutTax !== null || $percentageApplied !== null) {
            $amount = max($minimumWithoutTax ?? 0, $percentageApplied ?? 0);
        }

        $root->updateQuietly([
            'franchise_minimum_amount' => $minimumAmount > 0 ? round($minimumAmount, 2) : null,
            'franchise_percentage' => $percentage > 0 ? round($percentage, 2) : null,
            'franchise_minimum_includes_tax' => $includesTax,
            'franchise_minimum_without_tax' => $minimumWithoutTax,
            'franchise_base' => $base,
            'franchise_percentage_applied' => $percentageApplied,
            'franchise_amount' => $amount,
        ]);
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
        // Nota: 'insurance' queda excluido porque era el vehículo de la franquicia,
        // que ahora es informativa (campos franchise_* del estimate) y NO descuenta
        // el total del presupuesto.
        return (float) $estimate->discounts()
            ->whereIn('source', ['promotion', 'other'])
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
    public function preview(
        array $items,
        ?string $globalDiscountType,
        float $globalDiscountValue,
        ?int $establishmentId = null,
        array $thirdPartyOrders = [],
        float $franchiseMinimumAmount = 0,
        float $franchisePercentage = 0,
        bool $franchiseMinimumIncludesTax = false,
    ): array {
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

        // Órdenes de compra de terceros: solo alimentan la base de la franquicia.
        $ordersTotal = 0.0;
        foreach ($thirdPartyOrders as $order) {
            $ordersTotal += (float) ($order['amount_without_iva'] ?? 0);
        }
        $ordersTotal = round($ordersTotal, 2);

        $franchiseBase = round($taxableBase + $ordersTotal, 2);

        $minimumWithoutTax = null;
        if ($franchiseMinimumAmount > 0) {
            $minimumWithoutTax = $franchiseMinimumIncludesTax
                ? round($franchiseMinimumAmount / (1 + $igvRate), 2)
                : round($franchiseMinimumAmount, 2);
        }

        $percentageApplied = null;
        if ($franchisePercentage > 0 && $franchiseBase > 0) {
            $percentageApplied = round($franchiseBase * ($franchisePercentage / 100), 2);
        }

        $franchiseAmount = null;
        if ($minimumWithoutTax !== null || $percentageApplied !== null) {
            $franchiseAmount = max($minimumWithoutTax ?? 0, $percentageApplied ?? 0);
        }

        return [
            'subtotal' => $subtotalTotal,
            'discount' => $totalDiscount,
            'taxable_base' => $taxableBase,
            'iva' => $iva,
            'total' => $total,
            'igv_rate' => $igvRate,
            'lines' => $lines,
            'orders_total' => $ordersTotal,
            'franchise' => [
                'minimum_amount' => $franchiseMinimumAmount > 0 ? round($franchiseMinimumAmount, 2) : null,
                'percentage' => $franchisePercentage > 0 ? round($franchisePercentage, 2) : null,
                'minimum_includes_tax' => $franchiseMinimumIncludesTax,
                'minimum_without_tax' => $minimumWithoutTax,
                'base' => $franchiseBase,
                'percentage_applied' => $percentageApplied,
                'amount' => $franchiseAmount,
            ],
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