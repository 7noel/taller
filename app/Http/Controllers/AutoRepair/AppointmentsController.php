<?php

namespace App\Http\Controllers\AutoRepair;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\Modules\AutoRepair\AppointmentRepo;
use App\Modules\AutoRepair\SchedulingRepo;
use App\Modules\AutoRepair\AdviserScheduleRepo;

class AppointmentsController extends Controller
{

    protected $repo;
    protected $schedulingRepo;

    public function __construct(AppointmentRepo $repo, SchedulingRepo $schedulingRepo) {
        $this->repo = $repo;
        $this->schedulingRepo = $schedulingRepo;
    }

    public function index()
    {
        $models = $this->repo->index('placa', \Request::get('name'));
        return view('partials.index', compact('models'));
    }

    public function create()
    {
        $placa=\Session::get('placa2');
        
        $efectividad = [''=>'Seleccionar'] + $this->repo->efectividad();
        $asesores = [''=>'Seleccionar'] + $this->repo->asesores();
        $tipoot = [''=>'Seleccionar'] + $this->repo->tipoot();
        $times = [''=>'Seleccionar'];
        $orderasesores = [''=>'Seleccionar'];
        return view('partials.create', compact('efectividad', 'asesores', 'tipoot','placa', 'times', 'orderasesores'));
    }

    public function store()
    {
        $data = \Request::all();
        $this->repo->save($data);
        return \Redirect::route('autorepair.appointments.index');
    }

    public function show($id)
    {
        //
    }

    public function edit($id)
    {
        $model = $this->repo->findOrFail($id);
        $times = $this->schedulingRepo->getTime($model->fecha, $model->idcita);
        $orderasesores = $this->schedulingRepo->getAsesor($model->fecha, $model->hora, $model->idcita);
        $efectividad = [''=>'Seleccionar'] + $this->repo->efectividad();
        $asesores = [''=>'Seleccionar'] + $this->repo->asesores();
        $tipoot = [''=>'Seleccionar'] + $this->repo->tipoot();
        $placa = '';
        return view('partials.edit', compact('model', 'placa', 'efectividad', 'asesores', 'tipoot', 'times', 'orderasesores'));
    }

    public function update($id)
    {
        $data = \Request::all();
        $data['updated_by_id'] = \Auth::user()->id;
        $this->repo->save($data, $id);
        return \Redirect::route('autorepair.service_checklists.index');
    }

    public function destroy($id)
    {
        $model = $this->repo->destroy($id);
        if (\Request::ajax()) { return $model; }
        return redirect()->route('autorepair.service_checklists.index');
    }
}