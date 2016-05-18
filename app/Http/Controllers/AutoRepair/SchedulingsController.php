<?php

namespace App\Http\Controllers\AutoRepair;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\Modules\AutoRepair\SchedulingRepo;
class SchedulingsController extends Controller
{

    protected $repo;

    public function __construct(SchedulingRepo $repo) {
        $this->repo = $repo;
    }

    public function index()
    {
        $models = $this->repo->index('name', \Request::get('name'));
        return view('partials.index', compact('models'));
    }

    public function create()
    {
        $groups = $this->checkitemGroupRepo->all();
        $technicians = $this->employeeRepo->getListTechnicians();
        return view('partials.create', compact('groups', 'technicians'));
    }

    public function store()
    {
        $data = \Request::all();
        $data['created_by_id'] = \Auth::user()->id;
        $data['updated_by_id'] = \Auth::user()->id;
        $this->repo->save($data);
        return \Redirect::route('autorepair.service_checklists.index');
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
    public function ajaxGetTime($date)
    {
        return $this->repo->getTime($date);
    }
    public function ajaxGetAsesores($date, $time)
    {
        return $this->repo->getAsesor($date, $time);
    }
    public function ajaxGetClienteByPlate($plate)
    {
        return response()->json($this->repo->getClienteByPlate($plate));
    }

}