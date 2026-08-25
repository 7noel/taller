<?php

namespace App\Http\Controllers;

use App\Models\CompanySetting;
use App\Models\Establishment;
use App\Models\Ubigeo;
use App\Services\DocumentSeriesService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;

class EstablishmentController extends Controller
{
    protected DocumentSeriesService $documentSeriesService;

    public function __construct(DocumentSeriesService $documentSeriesService)
    {
        $this->documentSeriesService = $documentSeriesService;
    }

    public function index(): View
    {
        Gate::authorize('ver establecimientos');

        $establishments = Establishment::withCount('documentSeries')->orderBy('code')->get();

        return view('establishments.index', compact('establishments'));
    }

    public function create(): View
    {
        Gate::authorize('crear establecimientos');

        $departamentos = Ubigeo::select('departamento')->distinct()->orderBy('departamento')->pluck('departamento');

        return view('establishments.create', compact('departamentos'));
    }

    public function store(Request $request): RedirectResponse
    {
        Gate::authorize('crear establecimientos');

        $validated = $this->validateData($request, null);

        // Copia el IGV por defecto desde la configuración de empresa si no se envió
        if (! array_key_exists('igv_rate', $validated) || $validated['igv_rate'] === null) {
            $validated['igv_rate'] = CompanySetting::get()?->igv_rate ?? 0.18;
        }

        $establishment = Establishment::create($validated);

        $this->documentSeriesService->generateSeriesForEstablishment($establishment->id);

        return redirect()->route('establishments.index')
            ->with('success', "Establecimiento '{$establishment->name}' creado y series generadas correctamente.");
    }

    public function edit(Establishment $establishment): View
    {
        Gate::authorize('editar establecimientos');

        $departamentos = Ubigeo::select('departamento')->distinct()->orderBy('departamento')->pluck('departamento');

        return view('establishments.edit', compact('establishment', 'departamentos'));
    }

    public function update(Request $request, Establishment $establishment): RedirectResponse
    {
        Gate::authorize('editar establecimientos');

        $validated = $this->validateData($request, $establishment->id);

        $establishment->update($validated);

        return redirect()->route('establishments.index')
            ->with('success', 'Establecimiento actualizado correctamente.');
    }

    public function destroy(Establishment $establishment): RedirectResponse
    {
        Gate::authorize('eliminar establecimientos');

        $establishment->delete();

        return redirect()->route('establishments.index')
            ->with('success', 'Establecimiento eliminado correctamente.');
    }

    /**
     * Copia los datos editables de company_settings hacia el establecimiento,
     * sin copiar RUC, Razón Social ni Nombre Comercial.
     */
    public function copyFromCompany(Establishment $establishment): RedirectResponse
    {
        Gate::authorize('editar establecimientos');

        $setting = CompanySetting::get();

        if ($setting) {
            $establishment->update([
                'address' => $setting->direccion ?? $establishment->address,
                'ubigeo_code' => $setting->ubigeo_code ?? $establishment->ubigeo_code,
                'phone' => $setting->telefono ?? $establishment->phone,
                'celular' => $setting->celular ?? $establishment->celular,
                'email' => $setting->email ?? $establishment->email,
                'igv_rate' => $setting->igv_rate ?? $establishment->igv_rate,
            ]);
        }

        return redirect()->route('establishments.edit', $establishment)
            ->with('success', 'Datos editables copiados desde la configuración de empresa. RUC, Razón Social y Nombre Comercial no se copian (se editan manualmente).');
    }

    /**
     * Regenera las series del establecimiento (para tipos de documento nuevos).
     */
    public function regenerateSeries(Establishment $establishment): RedirectResponse
    {
        Gate::authorize('ver series');

        $this->documentSeriesService->generateSeriesForEstablishment($establishment->id);

        return redirect()->route('establishments.series.index', $establishment)
            ->with('success', 'Series regeneradas correctamente.');
    }

    /**
     * Reglas de validación comunes para crear/editar establecimiento.
     */
    protected function validateData(Request $request, ?int $id): array
    {
        return $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'code' => ['required', 'string', 'max:10', 'unique:establishments,code' . ($id ? ',' . $id : '')],
            'address' => ['nullable', 'string', 'max:255'],
            'ubigeo_code' => ['nullable', 'string', 'size:6', 'exists:ubigeos,code'],
            'phone' => ['nullable', 'string', 'max:20'],
            'celular' => ['nullable', 'string', 'max:20'],
            'email' => ['nullable', 'email', 'max:255'],
            'igv_rate' => ['nullable', 'numeric', 'min:0', 'max:1'],
            'base_currency' => ['nullable', 'string', 'in:PEN,USD'],
            'prices_include_tax' => ['sometimes', 'boolean'],
            'default_hourly_rate' => ['nullable', 'numeric', 'min:0'],
            'default_panel_rate' => ['nullable', 'numeric', 'min:0'],
        ]);
    }
}