<?php

namespace App\Http\Controllers\AutoRepair;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\Modules\AutoRepair\ServiceChecklistRepo;
use App\Modules\AutoRepair\CheckitemGroupRepo;

class ServiceChecklistsController extends Controller
{

    protected $repo;
    protected $checkitemRepo;

    public function __construct(ServiceChecklistRepo $repo, CheckitemGroupRepo $checkitemGroupRepo) {
        $this->repo = $repo;
        $this->checkitemGroupRepo = $checkitemGroupRepo;
    }

    public function index()
    {
        $models = $this->repo->index('plate', \Request::get('name'));
        return view('partials.index',compact('models'));
    }

    public function create()
    {
        $groups = $this->checkitemGroupRepo->all();
        return view('partials.create', compact('groups'));
    }

    public function store()
    {
        $this->repo->save(\Request::all());
        return \Redirect::route('autorepair.service_checklists.index');
    }

    public function show($id)
    {
        //
    }

    public function edit($id)
    {
        $model = $this->repo->findOrFail($id);
        $groups = $this->checkitemGroupRepo->all();
        //dd($model->checkitems[1]);
        return view('partials.edit', compact('model', 'groups'));
    }

    public function update($id)
    {
        $this->repo->save(\Request::all(), $id);
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
    public function pdf($id)
    {
        $model = $this->repo->findOrFail($id);
        $groups = $this->checkitemGroupRepo->all($model->catalog_car_id);
        return view('pdfs.service_checklist2', compact('model', 'groups'));
        $pdf = \PDF::loadView('pdfs.service_checklist2', compact('model', 'groups'));
        //$pdf = \PDF::loadView('pdfs.service_checklist');

        return $pdf->stream();
    }

    /**
     * OBTIENE LOS DATOS DE UNA OT
     * @param  INTEGER $order_id Nro de OT en la base de datos masaki (clarion)
     * @return JSON             Diccionario con los datos de la OT
     */
    public function ajaxGetOt($order_id)
    {
        $result = \DB::connection('masaki')->table('ordtra')->where('NroOrden',$order_id)->first();
        return \Response::json($result);;
    }
}
