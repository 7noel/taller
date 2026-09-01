<?php

namespace App\Http\Controllers;

use App\Models\Brand;
use App\Models\Establishment;
use App\Services\ReportService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ReportController extends Controller
{
    public function __construct(protected ReportService $service)
    {
    }

    /* ----------------------------- páginas ----------------------------- */

    public function index(): View
    {
        return $this->reportView('reports.index');
    }

    public function vehicles(): View
    {
        return $this->reportView('reports.vehicles', ['brands' => Brand::orderBy('name')->get()]);
    }

    public function advisors(): View
    {
        return $this->reportView('reports.advisors');
    }

    public function profitability(): View
    {
        return $this->reportView('reports.profitability');
    }

    public function followups(): View
    {
        return $this->reportView('reports.followups');
    }

    public function revenue(): View
    {
        return $this->reportView('reports.revenue');
    }

    public function parts(): View
    {
        return $this->reportView('reports.parts', ['brands' => Brand::orderBy('name')->get()]);
    }

    /* --------------------------- datos (JSON) --------------------------- */

    public function vehiclesData(Request $request): JsonResponse
    {
        return response()->json($this->service->vehicleFrequency($request->query()));
    }

    public function advisorsData(Request $request): JsonResponse
    {
        return response()->json($this->service->advisorProfitability($request->query()));
    }

    public function profitabilityData(Request $request): JsonResponse
    {
        return response()->json($this->service->workOrderProfitability($request->query()));
    }

    public function followupsData(Request $request): JsonResponse
    {
        return response()->json($this->service->followUps($request->query()));
    }

    public function revenueData(Request $request): JsonResponse
    {
        return response()->json($this->service->revenue($request->query()));
    }

    public function partsData(Request $request): JsonResponse
    {
        return response()->json($this->service->partsUsage($request->query()));
    }

    /* ----------------------------- helpers ----------------------------- */

    protected function reportView(string $name, array $extra = []): View
    {
        return view($name, array_merge([
            'establishments' => Establishment::orderBy('name')->get(),
        ], $extra));
    }
}