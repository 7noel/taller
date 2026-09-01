<?php

namespace App\Services;

use App\Models\WorkOrder;

/**
 * Calcula el costo y la utilidad de una Orden de Trabajo.
 *
 * Moneda funcional: PEN. Cada componente de costo se registra en su moneda
 * original (PEN/USD) con snapshot del tipo de cambio (convención del sistema:
 * exchange_rate = soles por 1 dólar). El servicio normaliza todo a PEN para
 * calcular la utilidad y, además, expresa los montos en la moneda de
 * visualización (la del primer presupuesto de la OT, o PEN si no hay).
 *
 * Componentes de costo:
 *   - repuestos    → ítems de presupuesto tipo 'part' (cost_price × cantidad)
 *   - vales        → comprobantes de servicio tercerizado (base sin IGV)
 *   - mano de obra → asignaciones de técnicos (work_order_assignments.cost)
 *   - OC terceros  → órdenes de compra de terceros del presupuesto
 */
class WorkOrderCostService
{
    public function __construct(protected ExchangeRateService $exchange)
    {
    }

    /**
     * Resumen de ingresos, costos y utilidad de la OT.
     */
    public function summary(WorkOrder $workOrder): array
    {
        $workOrder->loadMissing([
            'estimates.items',
            'estimates.thirdPartyOrders',
            'serviceVouchers',
            'assignments',
        ]);

        $firstEstimate = $workOrder->estimates->first();
        $displayCurrency = strtoupper((string) ($firstEstimate?->currency ?: 'PEN'));
        $displayRate = (float) ($firstEstimate?->exchange_rate ?: 1);

        $incomePen = 0.0;
        $incomeDisplay = 0.0;
        $estimatesIncome = [];

        foreach ($workOrder->estimates as $estimate) {
            $currency = strtoupper((string) ($estimate->currency ?: 'PEN'));
            $rate = (float) ($estimate->exchange_rate ?: 1);
            $total = (float) $estimate->total;

            $incomePen += $this->toPen($total, $currency, $rate);
            $incomeDisplay += $this->toDisplay($total, $currency, $rate, $displayCurrency, $displayRate);

            $estimatesIncome[] = [
                'id' => $estimate->id,
                'document_sn' => $estimate->document_sn,
                'currency' => $currency,
                'amount' => round($total, 2),
                'amount_pen' => round($this->toPen($total, $currency, $rate), 2),
            ];
        }

        $components = [
            'parts' => $this->partsCost($workOrder, $displayCurrency, $displayRate),
            'vouchers' => $this->vouchersCost($workOrder, $displayCurrency, $displayRate),
            'assignments' => $this->assignmentsCost($workOrder, $displayCurrency, $displayRate),
            'third_party' => $this->thirdPartyOrdersCost($workOrder, $displayCurrency, $displayRate),
        ];

        $totalCostPen = 0.0;
        $totalCostDisplay = 0.0;

        foreach ($components as $component) {
            $totalCostPen += $component['amount_pen'];
            $totalCostDisplay += $component['amount_display'];
        }

        $profitPen = round($incomePen - $totalCostPen, 2);
        $profitDisplay = round($incomeDisplay - $totalCostDisplay, 2);
        $margin = $incomePen > 0 ? round($profitPen / $incomePen * 100, 2) : 0.0;

        return [
            'display_currency' => $displayCurrency,
            'display_rate' => $displayRate,
            'income' => round($incomeDisplay, 2),
            'income_pen' => round($incomePen, 2),
            'estimates' => $estimatesIncome,
            'components' => $components,
            'total_cost' => round($totalCostDisplay, 2),
            'total_cost_pen' => round($totalCostPen, 2),
            'profit' => $profitDisplay,
            'profit_pen' => $profitPen,
            'margin' => $margin,
        ];
    }

    /**
     * Costo de repuestos: ítems de presupuesto tipo 'part' (cost_price × cantidad),
     * expresados en la moneda del presupuesto (los ítems ya se guardan convertidos).
     */
    protected function partsCost(WorkOrder $workOrder, string $displayCurrency, float $displayRate): array
    {
        return $this->aggregate($workOrder->estimates->flatMap(function ($estimate) {
            $currency = strtoupper((string) ($estimate->currency ?: 'PEN'));
            $rate = (float) ($estimate->exchange_rate ?: 1);

            return $estimate->items
                ->where('item_type', 'part')
                ->map(fn ($item) => [
                    'amount' => (float) $item->cost_price * (float) $item->quantity,
                    'currency' => $currency,
                    'rate' => $rate,
                ]);
        }), $displayCurrency, $displayRate, 'parts', 'Repuestos (ítems de presupuesto)');
    }

    /**
     * Costo de servicios tercerizados: base sin IGV de los vales (CST01).
     */
    protected function vouchersCost(WorkOrder $workOrder, string $displayCurrency, float $displayRate): array
    {
        return $this->aggregate($workOrder->serviceVouchers->map(fn ($voucher) => [
            'amount' => (float) $voucher->base_amount,
            'currency' => strtoupper((string) ($voucher->currency ?: 'PEN')),
            'rate' => (float) ($voucher->exchange_rate ?: 1),
        ]), $displayCurrency, $displayRate, 'vouchers', 'Servicios tercerizados (vales)');
    }

    /**
     * Costo de mano de obra interna: asignaciones de técnicos.
     */
    protected function assignmentsCost(WorkOrder $workOrder, string $displayCurrency, float $displayRate): array
    {
        return $this->aggregate($workOrder->assignments->map(fn ($assignment) => [
            'amount' => (float) $assignment->cost,
            'currency' => strtoupper((string) ($assignment->currency ?: 'PEN')),
            'rate' => (float) ($assignment->exchange_rate ?: 1),
        ]), $displayCurrency, $displayRate, 'assignments', 'Mano de obra (asignaciones)');
    }

    /**
     * Costo de órdenes de compra a terceros del presupuesto (sin IGV).
     */
    protected function thirdPartyOrdersCost(WorkOrder $workOrder, string $displayCurrency, float $displayRate): array
    {
        return $this->aggregate($workOrder->estimates->flatMap(function ($estimate) {
            return $estimate->thirdPartyOrders->map(fn ($order) => [
                'amount' => (float) $order->amount_without_iva,
                'currency' => strtoupper((string) ($order->currency ?: $estimate->currency ?: 'PEN')),
                'rate' => (float) ($order->exchange_rate ?: $estimate->exchange_rate ?: 1),
            ]);
        }), $displayCurrency, $displayRate, 'third_party', 'Órdenes de compra a terceros');
    }

    /**
     * Agrega un conjunto de montos (cada uno con moneda y T.C.) a PEN y a la
     * moneda de visualización. Si los montos mezclan monedas se indica con
     * 'mixed_currency' (la suma se expresa en la moneda de visualización).
     */
    protected function aggregate(iterable $rows, string $displayCurrency, float $displayRate, string $key, string $label): array
    {
        $amountPen = 0.0;
        $amountDisplay = 0.0;
        $count = 0;
        $currencies = [];

        foreach ($rows as $row) {
            $amount = (float) ($row['amount'] ?? 0);
            $currency = strtoupper((string) ($row['currency'] ?? 'PEN'));
            $rate = (float) ($row['rate'] ?? 1);

            $amountPen += $this->toPen($amount, $currency, $rate);
            $amountDisplay += $this->toDisplay($amount, $currency, $rate, $displayCurrency, $displayRate);
            $count++;
            $currencies[$currency] = true;
        }

        return [
            'key' => $key,
            'label' => $label,
            'count' => $count,
            'amount_pen' => round($amountPen, 2),
            'amount_display' => round($amountDisplay, 2),
            'mixed_currency' => count($currencies) > 1,
        ];
    }

    /**
     * Convierte un monto a PEN con el tipo de cambio snapshot.
     */
    protected function toPen(float $amount, string $currency, float $rate): float
    {
        return $this->exchange->convert($amount, $currency, 'PEN', $rate);
    }

    /**
     * Convierte un monto a la moneda de visualización (vía PEN).
     */
    protected function toDisplay(float $amount, string $currency, float $rate, string $displayCurrency, float $displayRate): float
    {
        if (strtoupper($currency) === $displayCurrency) {
            return $amount;
        }

        $pen = $this->toPen($amount, $currency, $rate);

        return $this->exchange->convert($pen, 'PEN', $displayCurrency, $displayRate);
    }
}

