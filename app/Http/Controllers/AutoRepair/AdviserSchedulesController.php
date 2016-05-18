<?php

namespace App\Http\Controllers\AutoRepair;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\Modules\AutoRepair\AdviserScheduleRepo;
use App\Modules\AutoRepair\AppointmentRepo;

class AdviserSchedulesController extends Controller
{

    protected $repo;
    protected $appointmentRepo;

    public function __construct(AdviserScheduleRepo $repo, AppointmentRepo $appointmentRepo) {
        $this->repo = $repo;
        $this->appointmentRepo = $appointmentRepo;
    }

    public function index()
    {
        $models = $this->repo->index('name', \Request::get('name'));
        return view('partials.index',compact('models'));
    }

    public function create()
    {
        $asesores = [''=>'Seleccionar'] + $this->appointmentRepo->asesores();
        return view('partials.create', compact('asesores'));
    }

    public function store()
    {
        //dd(\Request::all());
        $this->repo->save(\Request::all());
        return \Redirect::route('autorepair.adviser_schedules.index');
    }

    public function show($id)
    {
        //
    }

    public function edit($id)
    {
        $asesores = [''=>'Seleccionar'] + $this->appointmentRepo->asesores();
        $model = $this->repo->findOrFail($id);
        return view('partials.edit', compact('model', 'asesores'));
    }

    public function update($id)
    {
        $this->repo->save(\Request::all(), $id);
        return \Redirect::route('autorepair.adviser_schedules.index');
    }

    public function destroy($id)
    {
        $model = $this->repo->destroy($id);
        if (\Request::ajax()) { return $model; }
        return redirect()->route('autorepair.adviser_schedules.index');
    }
    
    public function ajaxGetAsesorByOrder($orderasesor,$date)
    {
        return response()->json($this->repo->getAsesorByOrder($orderasesor,$date));
    }
}
