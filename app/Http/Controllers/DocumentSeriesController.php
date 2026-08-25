<?php

namespace App\Http\Controllers;

use App\Http\Requests\SeriesRequest;
use App\Models\DocumentSeries;
use App\Models\DocumentType;
use App\Models\Establishment;
use App\Services\DocumentSeriesService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;

class DocumentSeriesController extends Controller
{
    protected DocumentSeriesService $documentSeriesService;

    public function __construct(DocumentSeriesService $documentSeriesService)
    {
        $this->documentSeriesService = $documentSeriesService;
    }

    public function index(Establishment $establishment): View
    {
        Gate::authorize('ver series');

        $series = $establishment->documentSeries()
            ->with('documentType')
            ->orderBy('document_type_id')
            ->get();

        $documentTypes = DocumentType::query()
            ->where('is_active', true)
            ->orderBy('name')
            ->get();

        return view('establishments.series', compact('establishment', 'series', 'documentTypes'));
    }

    public function store(SeriesRequest $request, Establishment $establishment): RedirectResponse
    {
        Gate::authorize('crear series');

        $this->documentSeriesService->createSeries($establishment, $request->validated());

        return redirect()->route('establishments.series.index', $establishment)
            ->with('success', 'Serie creada correctamente.');
    }

    public function update(SeriesRequest $request, Establishment $establishment, DocumentSeries $series): RedirectResponse
    {
        Gate::authorize('editar series');

        abort_unless($series->establishment_id === $establishment->id, 404);

        $this->documentSeriesService->updateSeries($series, $request->validated());

        return redirect()->route('establishments.series.index', $establishment)
            ->with('success', 'Serie actualizada correctamente.');
    }

    public function destroy(Establishment $establishment, DocumentSeries $series): RedirectResponse
    {
        Gate::authorize('eliminar series');

        abort_unless($series->establishment_id === $establishment->id, 404);

        if ($this->documentSeriesService->hasAssociatedDocuments($series)) {
            return back()->with('error', "No se puede eliminar la serie {$series->prefix_serie}: tiene documentos asociados.");
        }

        $this->documentSeriesService->destroy($series);

        return redirect()->route('establishments.series.index', $establishment)
            ->with('success', 'Serie eliminada correctamente.');
    }
}
