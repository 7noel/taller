<?php

namespace App\Http\Controllers;

use App\Http\Requests\InvoiceCreateRequest;
use App\Jobs\EmitInvoiceJob;
use App\Models\Invoice;
use App\Models\Party;
use App\Models\WorkOrder;
use App\Services\Facturacion\InvoiceService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Redirect;
use Illuminate\View\View;

class InvoiceController extends Controller
{
    public function __construct(protected InvoiceService $invoiceService)
    {
    }

    public function index(): View
    {
        Gate::authorize('viewAny', Invoice::class);

        return view('invoices.index');
    }

    /**
     * Formulario con selector de origen: OT, presupuestos o libre.
     */
    public function create(Request $request): View
    {
        Gate::authorize('create', Invoice::class);

        $origin = $request->input('origin', 'ot');
        $workOrder = null;
        $estimateIds = [];

        if ($origin === 'ot' && $request->filled('work_order_id')) {
            $workOrder = WorkOrder::with('estimates')->find($request->integer('work_order_id'));
        }

        if ($origin === 'estimate' && $request->filled('estimate_ids')) {
            $estimateIds = collect(explode(',', (string) $request->input('estimate_ids')))
                ->filter()->map(fn ($v) => (int) $v)->values()->all();
        }

        return view('invoices.create', compact('origin', 'workOrder', 'estimateIds'));
    }

    public function store(InvoiceCreateRequest $request): RedirectResponse
    {
        Gate::authorize('create', Invoice::class);

        $data = $request->validated();

        try {
            if ($data['origin'] === Invoice::ORIGIN_FREE) {
                $invoice = $this->invoiceService->createFree($data, $data['items'] ?? []);
            } elseif ($data['origin'] === Invoice::ORIGIN_OT) {
                $workOrder = WorkOrder::query()->findOrFail($data['work_order_id']);
                $estimateIds = $workOrder->estimates()->pluck('estimates.id')->all();
                $invoice = $this->invoiceService->createFromEstimates($estimateIds, $data);
            } else {
                $invoice = $this->invoiceService->createFromEstimates($data['estimate_ids'] ?? [], $data);
            }
        } catch (\InvalidArgumentException $e) {
            return back()->withErrors(['general' => $e->getMessage()])->withInput();
        }

        return Redirect::route('invoices.show', $invoice)
            ->with('success', 'Comprobante creado como borrador. Revisa el detalle y emítelo.');
    }

    public function show(Invoice $invoice): View
    {
        Gate::authorize('view', $invoice);

        $invoice->load([
            'establishment', 'party', 'vehicle.vehicleModel.brand', 'workOrder',
            'items.advanceInvoice', 'discounts', 'estimates', 'relatedInvoice', 'creator',
        ]);

        return view('invoices.show', compact('invoice'));
    }

    /**
     * Emite el borrador en el proveedor configurado.
     */
    public function emit(Invoice $invoice): RedirectResponse
    {
        Gate::authorize('update', $invoice);

        if (! in_array($invoice->status, [Invoice::STATUS_DRAFT, Invoice::STATUS_REJECTED], true)) {
            return back()->withErrors(['general' => 'El comprobante ya fue emitido.']);
        }

        try {
            EmitInvoiceJob::dispatch($invoice->loadMissing(['party', 'items', 'discounts']));
        } catch (\Exception $e) {
            return back()->withErrors(['general' => 'No se pudo encolar la emisión: ' . $e->getMessage()]);
        }

        return Redirect::route('invoices.show', $invoice)
            ->with('success', 'Emisión en proceso. La numeración y la respuesta de SUNAT se actualizarán en segundos.');
    }

    /**
     * Anula (comunicación de baja) un documento emitido.
     */
    public function void(Request $request, Invoice $invoice): RedirectResponse
    {
        Gate::authorize('update', $invoice);

        $request->validate(['motivo' => 'required|string|max:100']);

        try {
            $this->invoiceService->void($invoice, $request->input('motivo'));
        } catch (\Exception $e) {
            return back()->withErrors(['general' => 'No se pudo anular: ' . $e->getMessage()]);
        }

        return Redirect::route('invoices.show', $invoice)
            ->with('success', 'Comprobante anulado.');
    }

    /**
     * Genera una nota de crédito que modifica el documento.
     */
    public function creditNote(Request $request, Invoice $invoice): RedirectResponse
    {
        Gate::authorize('create', Invoice::class);

        $data = $request->validate([
            'motivo' => 'required|string|max:250',
            'amount' => 'required|numeric|min:0.01',
        ]);

        try {
            $note = $this->invoiceService->createNote($invoice, Invoice::DOC_CREDIT_NOTE, $data['motivo'], (float) $data['amount']);
        } catch (\Exception $e) {
            return back()->withErrors(['general' => $e->getMessage()]);
        }

        return Redirect::route('invoices.show', $note)
            ->with('success', 'Nota de crédito creada como borrador.');
    }

    /**
     * Genera una nota de débito que modifica el documento.
     */
    public function debitNote(Request $request, Invoice $invoice): RedirectResponse
    {
        Gate::authorize('create', Invoice::class);

        $data = $request->validate([
            'motivo' => 'required|string|max:250',
            'amount' => 'required|numeric|min:0.01',
        ]);

        try {
            $note = $this->invoiceService->createNote($invoice, Invoice::DOC_DEBIT_NOTE, $data['motivo'], (float) $data['amount']);
        } catch (\Exception $e) {
            return back()->withErrors(['general' => $e->getMessage()]);
        }

        return Redirect::route('invoices.show', $note)
            ->with('success', 'Nota de débito creada como borrador.');
    }


    /**
     * Listado plano para Tabulator.
     */
    public function search(Request $request): JsonResponse
    {
        Gate::authorize('viewAny', Invoice::class);

        $query = Invoice::query()
            ->with(['party', 'workOrder', 'vehicle', 'estimates'])
            ->latest('id');

        if ($request->filled('q')) {
            $term = $request->input('q');
            $query->where(function ($q) use ($term) {
                $q->where('document_sn', 'like', "%{$term}%")
                    ->orWhere('document_serie', 'like', "%{$term}%")
                    ->orWhereHas('party', fn ($p) => $p
                        ->where('business_name', 'like', "%{$term}%")
                        ->orWhere('first_name', 'like', "%{$term}%")
                        ->orWhere('last_name', 'like', "%{$term}%"));
            });
        }

        if ($request->filled('invoice_type')) {
            $query->where('invoice_type', $request->input('invoice_type'));
        }

        $rows = $query->limit($request->integer('limit', 100))->get();

        return response()->json($rows->map(fn (Invoice $i) => [
            'id' => $i->id,
            'document_sn' => $i->document_sn,
            'document_serie' => $i->document_serie,
            'doc_type_code' => $i->document_type_code,
            'doc_type_label' => $i->doc_type_label,
            'type_label' => $i->type_label,
            'party_name' => $i->party?->display_name,
            'invoice_date' => $i->invoice_date?->format('d/m/Y'),
            'total' => number_format($i->total, 2),
            'status' => $i->status,
            'status_label' => $i->status_label,
            'estimates_count' => $i->estimates->count(),
            'estimate_sns' => $i->estimates->pluck('document_sn')->implode(', '),
        ]));
    }

    /**
     * Receptores (clientes con RUC/empresa y aseguradoras) para Tom Select.
     */
    public function parties(Request $request): JsonResponse
    {
        Gate::authorize('viewAny', Invoice::class);

        $query = Party::query()->where(function ($q) {
            $q->where('is_insurance_company', true)
                ->orWhere('document_type', '6')
                ->orWhereNotNull('business_name');
        });

        if ($request->filled('q')) {
            $term = $request->input('q');
            $query->where(function ($q) use ($term) {
                $q->where('business_name', 'like', "%{$term}%")
                    ->orWhere('document_number', 'like', "%{$term}%")
                    ->orWhere('first_name', 'like', "%{$term}%")
                    ->orWhere('last_name', 'like', "%{$term}%");
            });
        }

        return response()->json($query->limit(30)->get()->map(fn (Party $p) => [
            'id' => $p->id,
            'text' => trim($p->display_name . ' · ' . ($p->document_type_label ?? '') . ' ' . $p->document_number),
        ]));
    }
}

