<?php

namespace App\Http\Controllers;

use App\Models\CheckIn;
use App\Models\Estimate;
use App\Models\Vehicle;
use App\Services\CheckInService;
use App\Services\EstimateService;
use Illuminate\Http\Request;
use Illuminate\View\View;
use RuntimeException;

/**
 * Portal público del cliente.
 *
 * Un único enlace por vehículo (/c/{token}) que muestra todo lo pendiente de
 * aprobación (inventarios y presupuestos), el estado del caso activo y el
 * histórico de servicios. No requiere autenticación: la seguridad se basa en
 * el token aleatorio del vehículo + validación de pertenencia e idempotencia.
 */
class PortalController extends Controller
{
    protected CheckInService $checkInService;
    protected EstimateService $estimateService;

    public function __construct(CheckInService $checkInService, EstimateService $estimateService)
    {
        $this->checkInService = $checkInService;
        $this->estimateService = $estimateService;
    }

    /**
     * Pantalla principal: todo lo pendiente de aprobación + estado + histórico.
     */
    public function show(string $token): View
    {
        $vehicle = $this->resolveVehicle($token);

        $vehicle->load(['vehicleModel.brand', 'relationships.party']);

        $pendingCheckIns = CheckIn::query()
            ->where('vehicle_id', $vehicle->id)
            ->where('status', 'pending_approval')
            ->with(['establishment', 'client'])
            ->orderByDesc('created_at')
            ->get();

        $pendingEstimates = Estimate::query()
            ->where('vehicle_id', $vehicle->id)
            ->where('status', 'sent_client')
            ->with(['establishment', 'client'])
            ->orderByDesc('created_at')
            ->get();

        // Caso activo más reciente (para el banner de estado).
        $activeCheckIn = CheckIn::query()
            ->where('vehicle_id', $vehicle->id)
            ->whereIn('status', ['pending_approval', 'approved', 'rejected'])
            ->latest('created_at')
            ->first();

        $history = CheckIn::query()
            ->where('vehicle_id', $vehicle->id)
            ->with(['client', 'establishment', 'estimates'])
            ->orderByDesc('created_at')
            ->get();

        return view('public.portal', compact('vehicle', 'pendingCheckIns', 'pendingEstimates', 'activeCheckIn', 'history'));
    }

    public function showCheckIn(string $token, CheckIn $checkIn): View
    {
        $vehicle = $this->resolveVehicle($token);
        $this->assertOwnership($vehicle, $checkIn);

        $checkIn->load([
            'vehicle.vehicleModel.brand',
            'client',
            'insuranceCompany',
            'establishment',
            'checklistResults.checklistItem',
            'damages',
            'photos',
        ]);

        // Daños con coordenadas para pintar sobre la silueta (misma lógica que la vista admin).
        $damagesWithCoords = $checkIn->damages
            ->filter(fn ($d) => $d->pos_x !== null && $d->pos_y !== null)
            ->map(fn ($d) => [
                'damage_type' => $d->damage_type,
                'pos_x' => $d->pos_x,
                'pos_y' => $d->pos_y,
            ])
            ->values();

        return view('public.checkin', compact('vehicle', 'checkIn', 'damagesWithCoords'));
    }

    public function showEstimate(string $token, Estimate $estimate): View
    {
        $vehicle = $this->resolveVehicle($token);
        $this->assertOwnership($vehicle, $estimate);

        $estimate->load([
            'vehicle.vehicleModel.brand',
            'client',
            'insuranceCompany',
            'establishment',
            'items.service.category',
            'items.part.category',
            'items.serviceCategory',
            'items.partCategory',
            'thirdPartyOrders',
        ]);

        $grouped = $this->estimateService->getClientGroupedItems($estimate);

        return view('public.estimate', compact('vehicle', 'estimate', 'grouped'));
    }

    public function approveCheckIn(Request $request, string $token, CheckIn $checkIn)
    {
        $vehicle = $this->resolveVehicle($token);
        $this->assertOwnership($vehicle, $checkIn);

        try {
            $this->checkInService->approveByClient($checkIn, $request->ip(), $request->userAgent());
        } catch (RuntimeException $e) {
            return back()->withErrors(['approval' => $e->getMessage()]);
        }

        return redirect()->route('public.portal', $token)
            ->with('success', '¡Inventario aprobado! Gracias por tu confirmación.');
    }

    public function rejectCheckIn(Request $request, string $token, CheckIn $checkIn)
    {
        $vehicle = $this->resolveVehicle($token);
        $this->assertOwnership($vehicle, $checkIn);

        $validated = $request->validate([
            'reason' => ['required', 'string', 'max:500'],
        ], ['reason.required' => 'Indica el motivo del rechazo.']);

        try {
            $this->checkInService->rejectByClient($checkIn, $validated['reason'], $request->ip(), $request->userAgent());
        } catch (RuntimeException $e) {
            return back()->withErrors(['approval' => $e->getMessage()]);
        }

        return redirect()->route('public.portal', $token)
            ->with('success', 'Registramos tu observación. El taller revisará el inventario.');
    }

    public function approveEstimate(Request $request, string $token, Estimate $estimate)
    {
        $vehicle = $this->resolveVehicle($token);
        $this->assertOwnership($vehicle, $estimate);

        try {
            $this->estimateService->changeStatusByClient($estimate, 'approved_client', null, $request->ip(), $request->userAgent());
        } catch (RuntimeException $e) {
            return back()->withErrors(['approval' => $e->getMessage()]);
        }

        return redirect()->route('public.portal', $token)
            ->with('success', '¡Presupuesto aprobado! Tu vehículo pasará a reparación.');
    }

    public function rejectEstimate(Request $request, string $token, Estimate $estimate)
    {
        $vehicle = $this->resolveVehicle($token);
        $this->assertOwnership($vehicle, $estimate);

        $validated = $request->validate([
            'reason' => ['required', 'string', 'max:500'],
        ], ['reason.required' => 'Indica el motivo del rechazo.']);

        try {
            $this->estimateService->changeStatusByClient($estimate, 'rejected_client', $validated['reason'], $request->ip(), $request->userAgent());
        } catch (RuntimeException $e) {
            return back()->withErrors(['approval' => $e->getMessage()]);
        }

        return redirect()->route('public.portal', $token)
            ->with('success', 'Registramos tu observación. El taller revisará tu caso.');
    }

    /**
     * Resuelve el vehículo a partir del token del enlace público.
     */
    protected function resolveVehicle(string $token): Vehicle
    {
        $vehicle = Vehicle::query()->where('access_token', $token)->first();

        abort_if(! $vehicle, 404, 'El enlace no es válido.');

        return $vehicle;
    }

    /**
     * El documento debe pertenecer al vehículo del token (si no, 404).
     */
    protected function assertOwnership(Vehicle $vehicle, $document): void
    {
        if ((int) $document->vehicle_id !== (int) $vehicle->id) {
            abort(404);
        }
    }
}
