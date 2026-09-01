<?php

namespace App\Services\Facturacion;

use App\Models\CompanySetting;
use App\Models\Estimate;
use App\Models\Invoice;
use App\Models\InvoiceDiscount;
use App\Models\InvoiceItem;
use App\Models\Party;
use App\Services\DocumentSeriesService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

/**
 * Orquestador de la facturación del taller.
 *
 * Reglas de negocio:
 * - 1 adelanto = 1 factura/boleta de adelanto (sunat_transaction = 4).
 * - El cierre (regular) agrupa TODOS los anticipos previos como líneas de
 *   regularización y el remanente del servicio.
 * - Con regularización de anticipos NO se puede usar descuento global
 *   (restricción NUBEFACT y FACTURA PERÚ): el descuento se prorratea por ítem.
 * - Franquicia → factura/boleta al cliente. Aseguradora → factura por
 *   total − franquicia.
 * - Boleta (serie B) si el receptor no tiene RUC; factura (serie F) si lo tiene.
 * - La numeración usa DocumentSeriesService::getNextNumber() (lock pesimista).
 */
class InvoiceService
{
    public function __construct(protected DocumentSeriesService $seriesService)
    {
    }

    /**
     * Crea una factura/boleta a partir de uno o más presupuestos (origen
     * estimate u ot). No emite en el proveedor: primero se construye el
     * borrador con sus ítems y totales.
     *
     * @param  array  $data  invoice_type, party_id, work_order_id?, invoice_date,
     *                       currency?, exchange_rate?, observations?, regularize_advances?,
     *                       advance_amount? (solo advance)
     */
    public function createFromEstimates(array $estimateIds, array $data): Invoice
    {
        return DB::transaction(function () use ($estimateIds, $data) {
            $estimates = Estimate::query()
                ->with(['items', 'client', 'insuranceCompany', 'vehicle'])
                ->whereIn('id', $estimateIds)
                ->orderBy('id')
                ->get();

            if ($estimates->isEmpty()) {
                throw new \InvalidArgumentException('Debe seleccionar al menos un presupuesto.');
            }

            // Guarda de estado: solo presupuestos facturables.
            $invalidStatus = $estimates->first(fn ($e) => ! in_array($e->status, Estimate::BILLABLE_STATUSES, true));

            if ($invalidStatus) {
                throw new \InvalidArgumentException(
                    "El presupuesto {$invalidStatus->document_sn} está en estado \"{$invalidStatus->status_label}\" y no puede facturarse."
                );
            }

            $type = $data['invoice_type'] ?? Invoice::TYPE_FREE;
            $this->guardInvoiceType($estimates, $type, (float) ($data['advance_amount'] ?? 0));
            $party = Party::query()->findOrFail($data['party_id']);

            $invoice = new Invoice([
                'establishment_id' => $estimates->first()->establishment_id,
                'invoice_type' => $type,
                'origin' => $data['origin'] ?? Invoice::ORIGIN_ESTIMATE,
                'work_order_id' => $data['work_order_id'] ?? null,
                'vehicle_id' => $data['vehicle_id'] ?? $estimates->first()->vehicle_id,
                'party_id' => $party->id,
                'provider' => $data['provider'] ?? (CompanySetting::get()?->facturador_provider ?? 'nubefact'),
                'currency' => $data['currency'] ?? $estimates->first()->currency ?? 'PEN',
                'exchange_rate' => $data['exchange_rate'] ?? $estimates->first()->exchange_rate,
                'invoice_date' => $data['invoice_date'] ?? now()->toDateString(),
                'observations' => $data['observations'] ?? null,
                'sunat_transaction' => in_array($type, [Invoice::TYPE_ADVANCE, Invoice::TYPE_REGULAR], true) ? 4 : 1,
                'status' => Invoice::STATUS_DRAFT,
                'created_by' => Auth::id(),
                'updated_by' => Auth::id(),
            ]);

            // Determina el tipo de documento (factura/boleta) según receptor.
            $invoice->document_type_code = $this->documentTypeCodeForParty($party);

            $this->buildLinesFromEstimates($invoice, $estimates, $data);

            $this->annotateVehiclePlates($invoice, $estimates);

            $this->computeTotals($invoice);
            $invoice->save();
            $invoice->items()->saveMany($invoice->items);
            $invoice->discounts()->saveMany($invoice->discounts);
            $invoice->estimates()->sync($estimates->pluck('id'));

            return $invoice->fresh();
        });
    }

    /**
     * Cuando la factura agrupa presupuestos de varios vehículos (flota), deja
     * constancia de las placas en las observaciones para el PDF.
     */
    protected function annotateVehiclePlates(Invoice $invoice, $estimates): void
    {
        $plates = $estimates
            ->map(fn ($e) => $e->vehicle?->plate)
            ->filter()
            ->unique()
            ->values();

        if ($plates->count() > 1) {
            $invoice->observations = trim(
                ($invoice->observations ?? '') . ' | Placas: ' . $plates->implode(', ')
            );
        }
    }

    /**
     * Validaciones anti doble facturación por tipo de documento.
     */
    protected function guardInvoiceType($estimates, string $type, float $advanceAmount): void
    {
        $estimateIds = $estimates->pluck('id');
        $existing = fn (array $types) => Invoice::query()
            ->where('status', '!=', Invoice::STATUS_VOIDED)
            ->whereIn('invoice_type', $types)
            ->whereHas('estimates', fn ($q) => $q->whereIn('estimates.id', $estimateIds))
            ->count();

        switch ($type) {
            case Invoice::TYPE_ADVANCE:
                $advanceTotal = (float) Invoice::query()
                    ->where('status', '!=', Invoice::STATUS_VOIDED)
                    ->where('invoice_type', Invoice::TYPE_ADVANCE)
                    ->whereHas('estimates', fn ($q) => $q->whereIn('estimates.id', $estimateIds))
                    ->sum('total');

                $budget = (float) $estimates->sum('total');

                if ($advanceTotal + $advanceAmount > $budget) {
                    throw new \InvalidArgumentException(
                        'La suma de adelantos (' . number_format($advanceTotal + $advanceAmount, 2) .
                        ') supera el total del presupuesto (' . number_format($budget, 2) . ').'
                    );
                }
                break;

            case Invoice::TYPE_FRANCHISE:
                if ($existing([Invoice::TYPE_FRANCHISE]) > 0) {
                    throw new \InvalidArgumentException('Este presupuesto ya tiene una factura de franquicia emitida.');
                }
                break;

            case Invoice::TYPE_INSURANCE:
                if ($existing([Invoice::TYPE_INSURANCE]) > 0) {
                    throw new \InvalidArgumentException('Este presupuesto ya tiene una factura a la aseguradora emitida.');
                }
                break;

            case Invoice::TYPE_REGULAR:
                if ($existing([Invoice::TYPE_REGULAR, Invoice::TYPE_INSURANCE]) > 0) {
                    throw new \InvalidArgumentException('Ya existe un cierre o factura a la aseguradora para este presupuesto.');
                }
                break;
        }
    }

    /**
     * Construye las líneas del borrador según el tipo de facturación.
     */
    protected function buildLinesFromEstimates(Invoice $invoice, $estimates, array $data): void
    {
        $type = $invoice->invoice_type;

        switch ($type) {
            case Invoice::TYPE_FRANCHISE:
                $this->addFranchiseLines($invoice, $estimates);
                break;

            case Invoice::TYPE_ADVANCE:
                $this->addAdvanceLine($invoice, $estimates, (float) ($data['advance_amount'] ?? 0));
                break;

            case Invoice::TYPE_INSURANCE:
                $this->addInsuranceLines($invoice, $estimates, $data);
                break;

            case Invoice::TYPE_REGULAR:
                $this->addRegularLines($invoice, $estimates, $data);
                break;

            default:
                $this->addEstimateDetailLines($invoice, $estimates, $data);
        }
    }

    protected function addEstimateDetailLines(Invoice $invoice, $estimates, array $data): void
    {
        $withAdvances = (bool) ($data['regularize_advances'] ?? false);
        $sort = 0;

        foreach ($estimates as $estimate) {
            foreach ($estimate->items as $item) {
                $invoice->items->push(new InvoiceItem([
                    'estimate_id' => $estimate->id,
                    'codigo_interno' => $item->codigo_interno,
                    'description' => $item->description,
                    'quantity' => $item->quantity,
                    'unit_price' => $item->unit_price,
                    'price' => $item->unit_price * (1 + $this->igvRate() / 100),
                    'discount' => $item->discount_amount,
                    'subtotal' => $item->net_line,
                    'affectation_igv_type' => '10',
                    'igv' => $item->iva_line,
                    'total' => $item->total_line,
                    'uom' => $item->uom === 'NIU' ? 'NIU' : 'ZZ',
                    'sort_order' => $sort++,
                ]));
            }
        }

        if ($withAdvances) {
            $this->addAdvanceRegularizationLines($invoice, $estimates, $sort);
        }

        // Descuento global del presupuesto: con anticipos se prorratea por
        // ítem (restricción de los proveedores); sin anticipos, descuento global.
        $globalDiscount = $estimates->sum('discount');

        if ($globalDiscount > 0) {
            if ($withAdvances) {
                $this->prorateDiscount($invoice, $globalDiscount);
            } else {
                $invoice->discounts->push(new InvoiceDiscount([
                    'code' => '02',
                    'description' => 'Descuento global',
                    'amount' => round($globalDiscount, 2),
                    'base' => round($estimates->sum('subtotal'), 2),
                ]));
            }
        }
    }


    /**
     * Franquicia → una línea por presupuesto con el monto de franquicia
     * (dirigida al cliente).
     */
    protected function addFranchiseLines(Invoice $invoice, $estimates): void
    {
        $sort = 0;
        $rate = $this->igvRate() / 100;

        foreach ($estimates as $estimate) {
            $franchise = (float) ($estimate->franchise_amount ?? 0);

            if ($franchise <= 0) {
                continue;
            }

            $unit = round($franchise / (1 + $rate), 2);
            $igv = round($unit * $rate, 2);

            $invoice->items->push(new InvoiceItem([
                'estimate_id' => $estimate->id,
                'description' => 'Franquicia (deducible) – ' . ($estimate->document_sn ?? 'presupuesto'),
                'quantity' => 1,
                'unit_price' => $unit,
                'price' => $franchise,
                'discount' => 0,
                'subtotal' => $unit,
                'affectation_igv_type' => '10',
                'igv' => $igv,
                'total' => $franchise,
                'uom' => 'ZZ',
                'sort_order' => $sort++,
            ]));
        }
    }

    /**
     * Adelanto → una línea "Adelanto por..." con el monto del pago parcial.
     */
    protected function addAdvanceLine(Invoice $invoice, $estimates, float $amount): void
    {
        if ($amount <= 0) {
            throw new \InvalidArgumentException('El monto del adelanto debe ser mayor a cero.');
        }

        $rate = $this->igvRate() / 100;
        $unit = round($amount / (1 + $rate), 2);
        $igv = round($unit * $rate, 2);
        $estimate = $estimates->first();

        $invoice->items->push(new InvoiceItem([
            'estimate_id' => $estimate->id,
            'description' => 'Adelanto por servicio de reparación',
            'quantity' => 1,
            'unit_price' => $unit,
            'price' => $amount,
            'discount' => 0,
            'subtotal' => $unit,
            'affectation_igv_type' => '10',
            'igv' => $igv,
            'total' => $amount,
            'uom' => 'ZZ',
            'sort_order' => 0,
        ]));
    }


    /**
     * Aseguradora → detalle del presupuesto menos la franquicia
     * (total − franquicia). Con anticipos, la franquicia se prorratea por ítem.
     */
    protected function addInsuranceLines(Invoice $invoice, $estimates, array $data): void
    {
        $withAdvances = (bool) ($data['regularize_advances'] ?? false);
        $this->addEstimateDetailLines($invoice, $estimates, ['regularize_advances' => $withAdvances]);

        $franchise = (float) $estimates->sum('franchise_amount');

        if ($franchise <= 0) {
            return;
        }

        // La franquicia es un monto TOTAL (con IGV) que asume el cliente.
        // Como el descuento global afecta la base imponible, se convierte.
        $franchiseBase = round($franchise / (1 + $this->igvRate() / 100), 2);

        if ($withAdvances) {
            $this->prorateDiscount($invoice, $franchiseBase);
        } else {
            $invoice->discounts->push(new InvoiceDiscount([
                'code' => '02',
                'description' => 'Franquicia asumida por el cliente',
                'amount' => $franchiseBase,
                'base' => round((float) $estimates->sum('subtotal'), 2),
            ]));
        }
    }

    /**
     * Cierre (regular) → detalle + líneas de regularización de TODOS los
     * anticipos previos emitidos para los presupuestos (sunat_transaction 4).
     */
    protected function addRegularLines(Invoice $invoice, $estimates, array $data): void
    {
        $this->addEstimateDetailLines($invoice, $estimates, ['regularize_advances' => true]);
    }

    /**
     * Agrega una línea de regularización por cada factura de adelanto previa
     * emitida para los presupuestos seleccionados. En NUBEFACT estas líneas
     * llevan anticipo_regularizacion = true + serie/número del anticipo.
     */
    protected function addAdvanceRegularizationLines(Invoice $invoice, $estimates, int &$sort): void
    {
        $advanceInvoices = Invoice::query()
            ->where('invoice_type', Invoice::TYPE_ADVANCE)
            ->where('status', '!=', Invoice::STATUS_VOIDED)
            ->whereHas('estimates', fn ($q) => $q->whereIn('estimates.id', $estimates->pluck('id')))
            ->with('items')
            ->orderBy('id')
            ->get();

        foreach ($advanceInvoices as $advance) {
            foreach ($advance->items as $item) {
                $invoice->items->push(new InvoiceItem([
                    'estimate_id' => $item->estimate_id,
                    'description' => 'Regularización del adelanto ' . ($advance->document_sn ?? ''),
                    'quantity' => 1,
                    'unit_price' => round((float) $item->subtotal, 2),
                    'price' => round((float) $item->total, 2),
                    'discount' => 0,
                    'subtotal' => round((float) $item->subtotal, 2),
                    'affectation_igv_type' => '10',
                    'igv' => round((float) $item->igv, 2),
                    'total' => round((float) $item->total, 2),
                    'uom' => 'ZZ',
                    'is_advance_line' => true,
                    'advance_invoice_id' => $advance->id,
                    'sort_order' => $sort++,
                ]));
            }

            $invoice->total_advances = (float) ($invoice->total_advances ?? 0) + (float) $advance->items->sum('subtotal');
        }
    }

    /**
     * Reparte un descuento total prorrateado entre las líneas del documento
     * (restricción: con regularización de anticipos no hay descuento global).
     */
    protected function prorateDiscount(Invoice $invoice, float $totalDiscount): void
    {
        $items = $invoice->items->filter(fn ($i) => ! $i->is_advance_line);
        $base = $items->sum('subtotal');

        if ($base <= 0 || $totalDiscount <= 0) {
            return;
        }

        $applied = 0.0;
        $count = $items->count();

        foreach ($items as $i => $item) {
            $isLast = $i === $count - 1;
            $discount = $isLast ? round($totalDiscount - $applied, 2) : round($item->subtotal / $base * $totalDiscount, 2);
            $applied += $discount;

            $item->discount = (float) $item->discount + $discount;
            $item->subtotal = round((float) $item->subtotal - $discount, 2);
            $item->igv = round((float) $item->subtotal * $this->igvRate() / 100, 2);
            $item->total = round((float) $item->subtotal + $item->igv, 2);
        }
    }


    /**
     * Facturación libre (sin presupuesto): ítems desde catálogo o manuales.
     *
     * @param  array  $items  array de filas: description, quantity, unit_price,
     *                        uom, codigo_interno, affectation_igv_type
     */
    public function createFree(array $data, array $items): Invoice
    {
        return DB::transaction(function () use ($data, $items) {
            $party = Party::query()->findOrFail($data['party_id']);

            $invoice = new Invoice([
                'establishment_id' => $data['establishment_id'] ?? Auth::user()?->establishment_id,
                'invoice_type' => Invoice::TYPE_FREE,
                'origin' => Invoice::ORIGIN_FREE,
                'vehicle_id' => $data['vehicle_id'] ?? null,
                'party_id' => $party->id,
                'provider' => $data['provider'] ?? (CompanySetting::get()?->facturador_provider ?? 'nubefact'),
                'currency' => $data['currency'] ?? 'PEN',
                'exchange_rate' => $data['exchange_rate'] ?? null,
                'invoice_date' => $data['invoice_date'] ?? now()->toDateString(),
                'observations' => $data['observations'] ?? null,
                'sunat_transaction' => 1,
                'status' => Invoice::STATUS_DRAFT,
                'created_by' => Auth::id(),
                'updated_by' => Auth::id(),
            ]);

            $invoice->document_type_code = $this->documentTypeCodeForParty($party);
            $this->addFreeItems($invoice, $items);
            $this->computeTotals($invoice);
            $invoice->save();
            $invoice->items()->saveMany($invoice->items);

            return $invoice->fresh();
        });
    }

    protected function addFreeItems(Invoice $invoice, array $items): void
    {
        $rate = $this->igvRate() / 100;
        $sort = 0;

        foreach ($items as $row) {
            if (empty($row['description'])) {
                continue;
            }

            $quantity = (float) ($row['quantity'] ?? 1);
            $unitPrice = (float) ($row['unit_price'] ?? 0);
            $subtotal = round($quantity * $unitPrice, 2);
            $igv = round($subtotal * $rate, 2);

            $invoice->items->push(new InvoiceItem([
                'codigo_interno' => $row['codigo_interno'] ?? null,
                'description' => $row['description'],
                'quantity' => $quantity,
                'unit_price' => $unitPrice,
                'price' => round($unitPrice * (1 + $rate), 2),
                'discount' => 0,
                'subtotal' => $subtotal,
                'affectation_igv_type' => $row['affectation_igv_type'] ?? '10',
                'igv' => $igv,
                'total' => round($subtotal + $igv, 2),
                'uom' => $row['uom'] ?? 'NIU',
                'sort_order' => $sort++,
            ]));
        }
    }

    /**
     * Crea una nota de crédito (07) o débito (08) que modifica un documento.
     */
    public function createNote(Invoice $related, string $type, string $motivo, float $amount): Invoice
    {
        if (! in_array($type, [Invoice::DOC_CREDIT_NOTE, Invoice::DOC_DEBIT_NOTE], true)) {
            throw new \InvalidArgumentException('Tipo de nota inválido.');
        }

        return DB::transaction(function () use ($related, $type, $motivo, $amount) {
            $rate = $this->igvRate() / 100;
            $unit = round($amount / (1 + $rate), 2);
            $igv = round($unit * $rate, 2);

            $invoice = new Invoice([
                'establishment_id' => $related->establishment_id,
                'invoice_type' => $related->invoice_type,
                'origin' => $related->origin,
                'work_order_id' => $related->work_order_id,
                'vehicle_id' => $related->vehicle_id,
                'party_id' => $related->party_id,
                'related_invoice_id' => $related->id,
                'documento_que_se_modifica_tipo' => $related->document_type_code,
                'documento_que_se_modifica_serie' => $related->document_serie,
                'documento_que_se_modifica_numero' => $related->document_number,
                'tipo_de_nota' => $type === Invoice::DOC_CREDIT_NOTE ? '01' : '01',
                'motivo_nota' => $motivo,
                'provider' => $related->provider,
                'currency' => $related->currency,
                'exchange_rate' => $related->exchange_rate,
                'invoice_date' => now()->toDateString(),
                'sunat_transaction' => 1,
                'status' => Invoice::STATUS_DRAFT,
                'created_by' => Auth::id(),
                'updated_by' => Auth::id(),
            ]);

            // La serie de la nota depende del documento modificado (F para
            // factura, B para boleta) y del tipo de nota (C = crédito, D = débito).
            $invoice->document_type_code = $type;
            $invoice->document_serie = $this->noteSeriePrefix($related, $type);

            $invoice->items->push(new InvoiceItem([
                'description' => ($type === Invoice::DOC_CREDIT_NOTE ? 'Nota de crédito' : 'Nota de débito') . ' de ' . $related->document_sn,
                'quantity' => 1,
                'unit_price' => $unit,
                'price' => $amount,
                'discount' => 0,
                'subtotal' => $unit,
                'affectation_igv_type' => '10',
                'igv' => $igv,
                'total' => $amount,
                'uom' => 'ZZ',
                'sort_order' => 0,
            ]));

            $this->computeTotals($invoice);
            $invoice->save();
            $invoice->items()->saveMany($invoice->items);

            if ($related->estimates()->exists()) {
                $invoice->estimates()->sync($related->estimates()->pluck('estimates.id'));
            }

            return $invoice->fresh();
        });
    }


    /**
     * Emite un borrador en el proveedor configurado:
     * 1. Asigna serie/número vía DocumentSeriesService (lock pesimista).
     * 2. Envía al proveedor.
     * 3. Persiste la respuesta (external_id, aceptación SUNAT, enlaces).
     */
    public function emit(Invoice $invoice): Invoice
    {
        return DB::transaction(function () use ($invoice) {
            $invoice->loadMissing(['party', 'items.advanceInvoice', 'discounts']);

            $this->assignDocumentNumber($invoice);

            $provider = FacturadorProviderFactory::make();
            $response = $provider->emitInvoice($invoice);

            $invoice->external_id = $response['external_id'] ?? null;
            $invoice->accepted_by_sunat = $response['accepted_by_sunat'] ?? null;
            $invoice->sunat_description = $response['sunat_description'] ?? null;
            $invoice->sunat_note = $response['sunat_note'] ?? null;
            $invoice->sunat_responsecode = $response['sunat_responsecode'] ?? null;
            $invoice->enlace_pdf = $response['enlace_pdf'] ?? null;
            $invoice->enlace_xml = $response['enlace_xml'] ?? null;
            $invoice->enlace_cdr = $response['enlace_cdr'] ?? null;
            $invoice->cadena_qr = $response['cadena_qr'] ?? null;
            $invoice->codigo_hash = $response['codigo_hash'] ?? null;

            // Proveedores API devuelven el correlativo asignado.
            if ($response['serie'] && $invoice->document_number === null) {
                $invoice->document_serie = $response['serie'];
                $invoice->document_number = (int) $response['numero'];
                $invoice->document_sn = sprintf('%s-%06d', $response['serie'], (int) $response['numero']);
            }

            $invoice->issued_at = now();
            $invoice->status = ($invoice->accepted_by_sunat === false)
                ? Invoice::STATUS_REJECTED
                : Invoice::STATUS_EMITTED;

            $invoice->save();

            $invoice->recordStatusChange($invoice->status, Invoice::STATUS_DRAFT, 'Documento emitido en ' . $invoice->provider);

            return $invoice->fresh();
        });
    }

    /**
     * Anula un documento emitido (comunicación de baja).
     */
    public function void(Invoice $invoice, string $reason): Invoice
    {
        if ($invoice->status === Invoice::STATUS_VOIDED) {
            throw new \InvalidArgumentException('El documento ya está anulado.');
        }

        $provider = FacturadorProviderFactory::make();
        $provider->voidInvoice($invoice, $reason);

        $invoice->status = Invoice::STATUS_VOIDED;
        $invoice->save();
        $invoice->recordStatusChange(Invoice::STATUS_VOIDED, null, $reason);

        return $invoice->fresh();
    }


    /**
     * Asigna serie y correlativo (snapshot de identidad) usando el servicio
     * de numeración con lock pesimista. Para series API deja number = null.
     */
    protected function assignDocumentNumber(Invoice $invoice): void
    {
        $establishmentId = $invoice->establishment_id ?? Auth::user()?->establishment_id;

        if (! $establishmentId) {
            throw new \RuntimeException('No hay establecimiento para numerar el documento.');
        }

        $prefix = $invoice->document_serie ?: $this->seriePrefix($invoice);

        $result = $this->seriesService->getNextNumber(
            $establishmentId,
            $invoice->document_type_code,
            $prefix
        );

        $invoice->document_series_id = $result['series']->id;
        $invoice->document_type_code = $result['document_type_code'] ?? $invoice->document_type_code;
        $invoice->document_serie = $result['series']->prefix_serie;

        if ($result['number'] !== null) {
            $invoice->document_number = $result['number'];
            $invoice->document_sn = $result['sn'];
        }
    }

    /**
     * Prefijo de serie por tipo de documento.
     */
    protected function seriePrefix(Invoice $invoice): string
    {
        return match ($invoice->document_type_code) {
            Invoice::DOC_INVOICE => 'FTR1',
            Invoice::DOC_RECEIPT => 'BLT1',
            Invoice::DOC_CREDIT_NOTE => 'FTC1',
            Invoice::DOC_DEBIT_NOTE => 'FTD1',
            default => 'FTR1',
        };
    }

    /**
     * Serie de NC/ND según el documento que modifica (factura → F, boleta → B).
     */
    protected function noteSeriePrefix(Invoice $related, string $type): string
    {
        $isInvoice = $related->document_type_code === Invoice::DOC_INVOICE;

        return match (true) {
            $type === Invoice::DOC_CREDIT_NOTE && $isInvoice => 'FTC1',
            $type === Invoice::DOC_CREDIT_NOTE => 'BLC1',
            $type === Invoice::DOC_DEBIT_NOTE && $isInvoice => 'FTD1',
            default => 'BLD1',
        };
    }

    protected function documentTypeCodeForParty(Party $party): string
    {
        return $party->document_type === '6' ? Invoice::DOC_INVOICE : Invoice::DOC_RECEIPT;
    }


    /**
     * Recalcula los totales de la cabecera a partir de las líneas y descuentos.
     * Las líneas de regularización de anticipos REDUCEN la base imponible.
     */
    protected function computeTotals(Invoice $invoice): void
    {
        $rate = $this->igvRate() / 100;

        $subtotal = 0.0;
        $discount = 0.0;
        $base = 0.0;
        $igv = 0.0;
        $total = 0.0;
        $advanceDiscount = 0.0;

        foreach ($invoice->items as $item) {
            if ($item->is_advance_line) {
                $advanceDiscount += (float) $item->subtotal;

                continue;
            }

            $subtotal += (float) $item->subtotal + (float) $item->discount;
            $discount += (float) $item->discount;
            $base += (float) $item->subtotal;
            $igv += (float) $item->igv;
            $total += (float) $item->total;
        }

        // Descuentos globales (código 02): reducen base e IGV.
        $globalDiscount = $invoice->discounts
            ->filter(fn ($d) => $d->code === '02')
            ->sum('amount');

        if ($globalDiscount > 0) {
            $base = max(0, round($base - $globalDiscount, 2));
            $discount = round($discount + $globalDiscount, 2);
        }

        // Anticipos regularizados: reducen la base imponible (como NUBEFACT:
        // total = servicio − anticipos).
        $base = max(0, round($base - $advanceDiscount, 2));
        $igv = round($base * $rate, 2);
        $total = round($base + $igv, 2);

        $invoice->subtotal = round($subtotal, 2);
        $invoice->discount = round($discount, 2);
        $invoice->taxable_base = round($base, 2);
        $invoice->iva = round($igv, 2);
        $invoice->total = round($total, 2);
    }

    protected function igvRate(): float
    {
        $rate = (float) (CompanySetting::get()?->igv_rate ?? 18.00);

        // La configuración puede guardar la tasa como fracción (0.18) o
        // como porcentaje (18.00); normaliza siempre a porcentaje.
        return $rate <= 1 ? round($rate * 100, 2) : round($rate, 2);
    }
}

