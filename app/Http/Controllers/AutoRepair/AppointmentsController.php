<?php

namespace App\Http\Controllers\AutoRepair;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\Modules\AutoRepair\AppointmentRepo;
use App\Modules\AutoRepair\SchedulingRepo;

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
        return view('partials.create', compact('efectividad', 'asesores', 'tipoot','placa'));
    }

    public function store()
    {
        $data = \Request::all();
        $this->repo->save($data);
        return \Redirect::route('autorepair.appointment.index');
    }

    public function show($id)
    {
        //
    }

    public function edit($id)
    {
        $model = $this->repo->findOrFail($id);
        $model->adviser = $model->adviser->full_name;
        $groups = $this->checkitemGroupRepo->all();
        $technicians = $this->employeeRepo->getListTechnicians();
        return view('partials.edit', compact('model', 'groups', 'technicians'));
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
    
    /**
     * CREA UN PDF EN EL NAVEGADOR
     * @param  [integer] $id [Es el id del checklist]
     * @return [pdf]     [Retorna un pdf]
     */
}