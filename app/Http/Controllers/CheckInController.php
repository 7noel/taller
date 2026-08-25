<?php

namespace App\Http\Controllers;

use App\Http\Requests\CheckInRequest;
use App\Models\CheckIn;
use App\Models\CheckInChecklistItem;
use App\Models\Party;
use App\Models\Vehicle;
use App\Services\CheckInService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;

class CheckInController extends Controller
{
    protected CheckInService $checkInService;

    public function __construct(CheckInService $checkInService)
    {
        $this->checkInService = $checkInService;
    }

    public function index(): View
    {
        Gate::authorize('viewAny', CheckIn::class);

        return view('check-ins.index');
    }

    public function create(): View
    {
        Gate::authorize('create', CheckIn::class);

        $checklistItems = CheckInChecklistItem::query()
            ->where('is_active', true)
            ->orderBy('order')
            ->get();

        return view('check-ins.create', compact('checklistItems'));
    }

    public function store(CheckInRequest $request)
    {
        Gate::authorize('create', CheckIn::class);

        $this->checkInService->create($request->validated());

        return redirect()->route('check-ins.index')
            ->with('success', 'Inventario creado correctamente.');
    }

    public function show(CheckIn $checkIn): View
    {
        Gate::authorize('view', $checkIn);

        $checkIn->load([
            'vehicle.vehicleModel.brand',
            'client',
            'insuranceCompany',
            'establishment',
            'documentSeries',
            'creator',
            'updater',
            'checklistResults.checklistItem',
            'damages',
            'photos',
        ]);

        return view('check-ins.show', compact('checkIn'));
    }

    public function edit(CheckIn $checkIn): View
    {
        Gate::authorize('update', $checkIn);

        $checklistItems = CheckInChecklistItem::query()
            ->where('is_active', true)
            ->orderBy('order')
            ->get();

        $checkIn->load([
            'vehicle.vehicleModel.brand',
            'client',
            'insuranceCompany',
            'documentSeries.documentType',
            'checklistResults.checklistItem',
            'damages',
            'photos',
            'vehicle.relationships.party',
        ]);

        return view('check-ins.edit', compact('checkIn', 'checklistItems'));
    }

    public function update(CheckInRequest $request, CheckIn $checkIn)
    {
        Gate::authorize('update', $checkIn);

        $this->checkInService->update($checkIn, $request->validated());

        return redirect()->route('check-ins.index')
            ->with('success', 'Inventario actualizado correctamente.');
    }

    public function destroy(CheckIn $checkIn)
    {
        Gate::authorize('delete', $checkIn);

        $this->checkInService->delete($checkIn);

        return redirect()->route('check-ins.index')
            ->with('success', 'Inventario eliminado correctamente.');
    }

    public function sendToClient(CheckIn $checkIn)
    {
        Gate::authorize('sendToClient', $checkIn);

        $this->checkInService->sendToClient($checkIn);

        return back()->with('success', 'Inventario enviado para aprobación del cliente.');
    }

    public function approve(CheckIn $checkIn)
    {
        Gate::authorize('approve', CheckIn::class);

        $this->checkInService->approve($checkIn);

        return back()->with('success', 'Inventario aprobado correctamente.');
    }

    public function reject(Request $request, CheckIn $checkIn)
    {
        Gate::authorize('reject', CheckIn::class);

        $request->validate(['reason' => ['nullable', 'string', 'max:500']]);

        $this->checkInService->reject($checkIn, $request->input('reason'));

        return back()->with('success', 'Inventario rechazado.');
    }

    public function search(Request $request): JsonResponse
    {
        Gate::authorize('viewAny', CheckIn::class);

        $query = CheckIn::query()
            ->with([
                'vehicle.vehicleModel.brand',
                'client',
                'insuranceCompany',
                'establishment',
                'documentSeries.documentType',
            ])
            ->when($request->filled('q'), function ($q) use ($request) {
                $term = $request->query('q');
                $q->where(function ($sub) use ($term) {
                    $sub->whereHas('vehicle', fn ($v) => $v->where('plate', 'like', "%{$term}%"))
                        ->orWhereHas('vehicle.vehicleModel.brand', fn ($b) => $b->where('name', 'like', "%{$term}%"))
                        ->orWhereHas('client', fn ($c) => $c
                            ->where('business_name', 'like', "%{$term}%")
                            ->orWhere('first_name', 'like', "%{$term}%")
                            ->orWhere('last_name', 'like', "%{$term}%"))
                        ->orWhere('document_sn', 'like', "%{$term}%")
                        ->orWhere('document_serie', 'like', "%{$term}%")
                        ->orWhere('document_type_code', 'like', "%{$term}%")
                        ->orWhereRaw('CAST(document_number AS CHAR) LIKE ?', ["%{$term}%"]);
                });
            })
            ->when($request->filled('plate'), function ($q) use ($request) {
                $q->whereHas('vehicle', fn ($v) => $v->where('plate', 'like', '%' . strtoupper($request->query('plate')) . '%'));
            })
            ->when($request->filled('client_id'), function ($q) use ($request) {
                $q->where('client_id', $request->integer('client_id'));
            })
            ->when($request->filled('status'), function ($q) use ($request) {
                $q->where('status', $request->query('status'));
            })
            ->when($request->filled('date_from'), function ($q) use ($request) {
                $q->whereDate('created_at', '>=', $request->query('date_from'));
            })
            ->when($request->filled('date_to'), function ($q) use ($request) {
                $q->whereDate('created_at', '<=', $request->query('date_to'));
            })
            ->orderByDesc('created_at');

        $limit = $request->integer('limit', 100);
        $data = $query->limit($limit)->get()->map(function (CheckIn $checkIn) {
            return [
                'id' => $checkIn->id,
                'plate' => $checkIn->vehicle?->plate,
                'vehicle_brand' => $checkIn->vehicle?->vehicleModel?->brand?->name,
                'vehicle_model' => $checkIn->vehicle?->vehicleModel?->name,
                'client_name' => $checkIn->client?->display_name,
                'client_document' => $checkIn->client?->document_number,
                'document_type_code' => $checkIn->document_type_code,
                'document_serie' => $checkIn->document_serie,
                'document_number' => $checkIn->document_number,
                'document_sn' => $checkIn->document_sn,
                'formatted_document_number' => $checkIn->formatted_document_number,
                'document_type_name' => $checkIn->documentSeries?->documentType?->name,
                'is_electronic' => (bool) ($checkIn->documentSeries?->documentType?->is_electronic ?? false),
                'service_type' => $checkIn->service_type_label,
                'service_type_value' => $checkIn->service_type,
                'insurance_company' => $checkIn->insuranceCompany?->display_name,
                'created_at' => $checkIn->created_at?->format('d/m/Y H:i'),
                'status' => $checkIn->status,
                'status_label' => $checkIn->status_label,
                'establishment' => $checkIn->establishment?->name,
            ];
        });

        return response()->json($data);
    }

    public function contacts(Request $request): JsonResponse
    {
        Gate::authorize('viewAny', CheckIn::class);

        $vehicleId = $request->integer('vehicle_id');

        $vehicle = Vehicle::with(['relationships.party'])
            ->findOrFail($vehicleId);

        $types = ['owner', 'approver', 'driver', 'operator', 'insurance_company'];

        $result = [];
        foreach ($types as $type) {
            $relationship = $vehicle->relationships->first(fn ($r) => $r->role === $type);
            $result[$type] = $relationship ? [
                'party_id' => $relationship->party_id,
                'name' => $relationship->party?->first_name,
                'last_name' => $relationship->party?->last_name,
                'business_name' => $relationship->party?->business_name,
                'document_type' => $relationship->party?->document_type,
                'document_number' => $relationship->party?->document_number,
                'phone' => $relationship->party?->phone,
                'mobile' => $relationship->party?->mobile,
                'email' => $relationship->party?->email,
            ] : null;
        }

        return response()->json($result);
    }

    public function uploadPhoto(Request $request, CheckIn $checkIn): JsonResponse
    {
        Gate::authorize('uploadPhoto', $checkIn);

        $request->validate([
            'photo' => ['required', 'image', 'mimes:jpg,jpeg,png,webp', 'max:5120'],
            'description' => ['nullable', 'string', 'max:255'],
        ]);

        $photo = $this->checkInService->addPhoto(
            $checkIn,
            $request->file('photo'),
            $request->input('description')
        );

        return response()->json([
            'id' => $photo->id,
            'url' => $photo->url,
            'order' => $photo->order,
        ], 201);
    }

    public function destroyPhoto(CheckIn $checkIn, int $photo): JsonResponse
    {
        Gate::authorize('deletePhoto', $checkIn);

        $this->checkInService->removePhoto($checkIn, $photo);

        return response()->json(['success' => true]);
    }

    public function removeContact(Request $request): JsonResponse
    {
        Gate::authorize('editar inventarios');

        $request->validate([
            'vehicle_id' => ['required', 'integer', 'exists:vehicles,id'],
            'role' => ['required', 'string', 'in:approver,driver,operator,owner'],
        ]);

        $deleted = \App\Models\VehicleRelationship::where('vehicle_id', $request->integer('vehicle_id'))
            ->where('role', $request->string('role'))
            ->delete();

        return response()->json(['success' => (bool) $deleted]);
    }

    public function insuranceCompanies(): JsonResponse
    {
        Gate::authorize('viewAny', CheckIn::class);

        return response()->json(
            Party::where('is_insurance_company', true)
                ->orderBy('business_name')
                ->get(['id', 'business_name', 'document_number', 'document_type'])
        );
    }
}