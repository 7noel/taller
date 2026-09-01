<?php

namespace App\Services\Facturacion;

use App\Models\CashMovement;
use App\Models\CashRegister;
use App\Models\Estimate;
use App\Models\Invoice;
use App\Models\Payment;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

/**
 * Módulo de caja: apertura/cierre, pagos (ingresos) y egresos.
 * Un adelanto es: Payment (payable = Estimate, direction in) + factura/boleta
 * de adelanto emitida vía InvoiceService, enlazada por payment.invoice_id.
 */
class CashService
{
    public function __construct(protected InvoiceService $invoiceService)
    {
    }

    /**
     * Abre una caja para el establecimiento del usuario actual.
     */
    public function open(array $data): CashRegister
    {
        if ($this->currentRegister()) {
            throw new \InvalidArgumentException('Ya existe una caja abierta para este establecimiento.');
        }

        return CashRegister::create([
            'establishment_id' => $data['establishment_id'] ?? Auth::user()?->establishment_id,
            'name' => $data['name'] ?? ('Caja ' . now()->format('d/m/Y')),
            'opening_date' => now(),
            'opening_amount' => (float) ($data['opening_amount'] ?? 0),
            'notes' => $data['notes'] ?? null,
            'status' => CashRegister::STATUS_OPEN,
            'opened_by' => Auth::id(),
        ]);
    }

    /**
     * Cierra la caja actual validando el arqueo contra los movimientos.
     */
    public function close(CashRegister $register, array $data): CashRegister
    {
        if ($register->status !== CashRegister::STATUS_OPEN) {
            throw new \InvalidArgumentException('La caja ya está cerrada.');
        }

        $income = (float) $register->movements()->where('type', CashMovement::TYPE_INCOME)->sum('amount');
        $expense = (float) $register->movements()->where('type', CashMovement::TYPE_EXPENSE)->sum('amount');
        $expected = $register->opening_amount + $income - $expense;

        $register->update([
            'closing_date' => now(),
            'closing_amount' => (float) ($data['closing_amount'] ?? $expected),
            'expected_amount' => round($expected, 2),
            'notes' => $data['notes'] ?? $register->notes,
            'status' => CashRegister::STATUS_CLOSED,
            'closed_by' => Auth::id(),
        ]);

        return $register->fresh();
    }

    /**
     * Caja abierta del establecimiento actual (o null).
     */
    public function currentRegister(): ?CashRegister
    {
        return CashRegister::query()
            ->where('status', CashRegister::STATUS_OPEN)
            ->where('establishment_id', Auth::user()?->establishment_id)
            ->latest('id')
            ->first();
    }


    /**
     * Registra un adelanto: crea el Payment (ingreso) + la factura/boleta de
     * adelanto vía InvoiceService y la enlaza.
     *
     * @param  array  $data  amount, party_id, payment_method_id, bank_id?, reference?, payment_date?
     */
    public function registerAdvance(Estimate $estimate, array $data): Payment
    {
        return DB::transaction(function () use ($estimate, $data) {
            $invoice = $this->invoiceService->createFromEstimates(
                [$estimate->id],
                [
                    'invoice_type' => Invoice::TYPE_ADVANCE,
                    'party_id' => $data['party_id'],
                    'advance_amount' => (float) $data['amount'],
                    'invoice_date' => $data['payment_date'] ?? now()->toDateString(),
                    'currency' => $data['currency'] ?? $estimate->currency ?? 'PEN',
                ]
            );

            return $this->registerPayment($estimate, array_merge($data, [
                'amount' => $data['amount'],
                'invoice' => $invoice,
            ]));
        });
    }

    /**
     * Registra un pago (ingreso) contra un modelo polimórfico (Estimate/OT/Invoice).
     * Opcionalmente crea el movimiento de caja si hay caja abierta.
     */
    public function registerPayment($payable, array $data): Payment
    {
        return DB::transaction(function () use ($payable, $data) {
            $payment = Payment::create([
                'payable_type' => $payable->getMorphClass(),
                'payable_id' => $payable->id,
                'party_id' => $data['party_id'] ?? $payable->client_id ?? $payable->party_id ?? null,
                'amount' => (float) $data['amount'],
                'payment_method_id' => $data['payment_method_id'] ?? null,
                'bank_id' => $data['bank_id'] ?? null,
                'cash_register_id' => $data['cash_register_id'] ?? $this->currentRegister()?->id,
                'invoice_id' => $data['invoice']->id ?? ($data['invoice_id'] ?? null),
                'reference' => $data['reference'] ?? null,
                'payment_date' => $data['payment_date'] ?? now()->toDateString(),
                'direction' => Payment::DIRECTION_IN,
                'notes' => $data['notes'] ?? null,
                'created_by' => Auth::id(),
                'updated_by' => Auth::id(),
            ]);

            $register = $payment->cashRegister;

            if ($register) {
                CashMovement::create([
                    'cash_register_id' => $register->id,
                    'payment_id' => $payment->id,
                    'type' => CashMovement::TYPE_INCOME,
                    'amount' => $payment->amount,
                    'payment_method_id' => $payment->payment_method_id,
                    'bank_id' => $payment->bank_id,
                    'description' => 'Cobro / adelanto',
                    'reference' => $payment->reference,
                    'movement_date' => $payment->payment_date,
                    'created_by' => Auth::id(),
                ]);
            }

            return $payment;
        });
    }

    /**
     * Registra un egreso de caja (no ligado a una factura).
     */
    public function registerExpense(CashRegister $register, array $data): CashMovement
    {
        return CashMovement::create([
            'cash_register_id' => $register->id,
            'type' => CashMovement::TYPE_EXPENSE,
            'amount' => (float) $data['amount'],
            'payment_method_id' => $data['payment_method_id'] ?? null,
            'bank_id' => $data['bank_id'] ?? null,
            'description' => $data['description'] ?? null,
            'reference' => $data['reference'] ?? null,
            'movement_date' => $data['movement_date'] ?? now()->toDateString(),
            'created_by' => Auth::id(),
        ]);
    }

    /**
     * Total cobrado en un presupuesto (adelantos + pagos directos).
     */
    public function estimatePaid(Estimate $estimate): float
    {
        return (float) $estimate->payments()
            ->where('direction', Payment::DIRECTION_IN)
            ->whereNull('deleted_at')
            ->sum('amount');
    }
}
