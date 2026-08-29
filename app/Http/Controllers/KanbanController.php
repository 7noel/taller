<?php

namespace App\Http\Controllers;

use App\Services\KanbanService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class KanbanController extends Controller
{
    public function __construct(private KanbanService $service)
    {
    }

    public function index(): View
    {
        return view('kanban.index');
    }

    public function data(Request $request): JsonResponse
    {
        return response()->json($this->service->board($request->user()));
    }
}
