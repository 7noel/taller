<?php

namespace App\Http\Controllers;

use App\Http\Requests\AppointmentRequest;
use App\Models\Appointment;
use App\Models\Vehicle;
use App\Services\AppointmentService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;

class AppointmentController extends Controller
{
    protected AppointmentService $appointmentService;

    public function __construct(AppointmentService $appointmentService)
    {
        $this->appointmentService = $appointmentService;
    }

    public function index(Request $request): View
    {
        Gate::authorize('viewAny', Appointment::class);

        $from = $request->input('from') ?: now()->startOfDay()->format('Y-m-d');
        $to = $request->input('to') ?: now()->addDays(30)->endOfDay()->format('Y-m-d');

        return view('appointments.index', compact('from', 'to'));
    }

    public function create(): View
    {
        Gate::authorize('create', Appointment::class);

        return view('appointments.create');
    }

    public function store(AppointmentRequest $request): RedirectResponse
    {
        Gate::authorize('create', Appointment::class);

        $this->appointmentService->create($request->validated());

        return redirect()->route('appointments.index')
            ->with('success', 'Cita agendada correctamente.');
    }

    public function show(Appointment $appointment): View
    {
        Gate::authorize('view', $appointment);

        $appointment->load([
            'vehicle.vehicleModel.brand',
            'party',
            'advisor',
            'checkIn',
            'establishment',
            'creator',
        ]);

        return view('appointments.show', compact('appointment'));
    }

    public function edit(Appointment $appointment): View
    {
        Gate::authorize('update', $appointment);

        $appointment->load(['vehicle.vehicleModel.brand', 'party', 'advisor']);

        return view('appointments.edit', compact('appointment'));
    }

    public function update(AppointmentRequest $request, Appointment $appointment): RedirectResponse
    {
        Gate::authorize('update', $appointment);

        $this->appointmentService->update($appointment, $request->validated());

        return redirect()->route('appointments.show', $appointment)
            ->with('success', 'Cita actualizada correctamente.');
    }

    public function destroy(Appointment $appointment): RedirectResponse
    {
        Gate::authorize('delete', $appointment);

        $appointment->delete();

        return redirect()->route('appointments.index')
            ->with('success', 'Cita eliminada correctamente.');
    }

    public function confirm(Appointment $appointment): RedirectResponse
    {
        Gate::authorize('update', $appointment);

        $this->appointmentService->confirm($appointment);

        return back()->with('success', 'Cita confirmada.');
    }

    public function cancel(Appointment $appointment): RedirectResponse
    {
        Gate::authorize('update', $appointment);

        $this->appointmentService->cancel($appointment);

        return back()->with('success', 'Cita cancelada.');
    }

    public function unlink(Appointment $appointment): RedirectResponse
    {
        Gate::authorize('update', $appointment);

        $this->appointmentService->unlink($appointment);

        return back()->with('success', 'La cita se desasoció del ingreso.');
    }

    /**
     * Citas pendientes de un vehículo para los indicadores del formulario de ingreso.
     */
    public function vehicleInfo(Vehicle $vehicle): JsonResponse
    {
        Gate::authorize('viewAny', Appointment::class);

        return response()->json($this->appointmentService->vehicleInfo($vehicle));
    }

    /**
     * Listado plano para Tabulator (sin paginación remota, patrón api.parties.search).
     */
    public function search(Request $request): JsonResponse
    {
        Gate::authorize('viewAny', Appointment::class);

        $query = Appointment::query()
            ->with(['vehicle.vehicleModel.brand', 'party', 'advisor', 'checkIn']);

        if ($request->filled('from')) {
            $query->whereDate('scheduled_at', '>=', $request->input('from'));
        }

        if ($request->filled('to')) {
            $query->whereDate('scheduled_at', '<=', $request->input('to'));
        }

        if ($request->filled('q')) {
            $term = $request->input('q');
            $query->where(function ($q) use ($term) {
                $q->where('contact_name', 'like', "%{$term}%")
                    ->orWhere('contact_phone', 'like', "%{$term}%")
                    ->orWhereHas('vehicle', fn ($v) => $v->where('plate', 'like', "%{$term}%"))
                    ->orWhereHas('party', fn ($p) => $p
                        ->where('business_name', 'like', "%{$term}%")
                        ->orWhere('first_name', 'like', "%{$term}%")
                        ->orWhere('last_name', 'like', "%{$term}%"));
            });
        }

        $rows = $query
            ->orderBy('scheduled_at')
            ->limit($request->integer('limit', 100))
            ->get();

        return response()->json($rows->map(function (Appointment $a) {
            return [
                'id' => $a->id,
                'scheduled_at' => $a->scheduled_at?->format('Y-m-d H:i'),
                'scheduled_date' => $a->scheduled_at?->format('d/m/Y'),
                'time' => $a->scheduled_at?->format('H:i'),
                'plate' => $a->vehicle?->plate,
                'vehicle_label' => $a->vehicle
                    ? trim(($a->vehicle->vehicleModel?->brand?->name ?? '') . ' ' . ($a->vehicle->vehicleModel?->name ?? ''))
                    : '',
                'contact_name' => $a->contact_name,
                'contact_phone' => $a->contact_phone,
                'party_name' => $a->party?->display_name,
                'advisor_name' => $a->advisor?->name,
                'service_type' => $a->service_type,
                'service_type_label' => $a->service_type_label,
                'status' => $a->status,
                'status_label' => $a->status_label,
                'check_in_sn' => $a->checkIn?->document_sn,
                'check_in_id' => $a->checkIn?->id,
            ];
        }));
    }
}
