<?php

namespace App\Http\Controllers\AutoRepair;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\Modules\AutoRepair\ServiceChecklistRepo;
use App\Modules\AutoRepair\CheckitemGroupRepo;
use App\Modules\HumanResources\EmployeeRepo;

class ServiceChecklistsController extends Controller
{

    protected $repo;
    protected $checkitemRepo;
    protected $employeeRepo;

    public function __construct(ServiceChecklistRepo $repo, CheckitemGroupRepo $checkitemGroupRepo, EmployeeRepo $employeeRepo) {
        $this->repo = $repo;
        $this->checkitemGroupRepo = $checkitemGroupRepo;
        $this->employeeRepo = $employeeRepo;
    }

    public function index()
    {
        $models = $this->repo->index('plate', \Request::get('name'));
        return view('partials.index',compact('models'));
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
    
    /**
     * CREA UN PDF EN EL NAVEGADOR
     * @param  [integer] $id [Es el id del checklist]
     * @return [pdf]     [Retorna un pdf]
     */
    public function print_out($id)
    {
        $model = $this->repo->findOrFail($id);
        $groups = $this->checkitemGroupRepo->all($model->catalog_car_id);
        return view('pdfs.service_checklist2', compact('model', 'groups'));
        //$pdf = \PDF::loadView('pdfs.service_checklist2', compact('model', 'groups'));
        //$pdf = \PDF::loadView('pdfs.service_checklist');

        //return $pdf->stream();
    }

    /**
     * OBTIENE LOS DATOS DE UNA OT
     * @param  INTEGER $order_id Nro de OT en la base de datos masaki (clarion)
     * @return JSON             Diccionario con los datos de la OT
     */
    public function ajaxGetOt($order_id)
    {
        $result = \DB::connection('masaki')->table('ordtra')->where('NroOrden',$order_id)->first();
        $adviser = $this->employeeRepo->getAdviserOldId($result->Codasesor);
        $result->adviser_id = $adviser->id;
        $result->adviser = $adviser->full_name;
        return \Response::json($result);
    }

    /**
     * OBTIENE LOS PROXIMOS SERVICIOS
     */
    public function nextservice()
    {
        $request = \Request::all();
        //Estableciendo las fechas
        if (!isset($request['type'])) {
            $request['type'] = 'call';
        }
        if (!isset($request['datemin'])) {
            $dt1 = \Carbon::now();
        } else {
            $dt1 = \Carbon::createFromFormat('Y-m-d', $request['datemin']);
        }
        if (!isset($request['datemax'])) {
            $dt2 = $dt1;
        } else {
            $dt2 = \Carbon::createFromFormat('Y-m-d', $request['datemax']);
            if ($dt2->lt($dt1)) {
                $dt2 = $dt1;
            }
        }
        $request['datemin'] = $dt1->toDateString();
        $request['datemax'] = $dt2->toDateString();

        //Si es message calcula llamadas para los que cumpliran el km en 2 semanas
        if ($request['type']=='message') {
            $dt1 = $dt1->addWeeks(2);
            $dt2 = $dt2->addWeeks(2);
        }

        $datemin = $dt1->toDateString();
        $datemax = $dt2->toDateString();
        
        //Consulta los vehiculos para proximos preventivos
        $vehicles = \DB::connection('masaki')->table('vehiculo')
            ->join('clientes', 'vehiculo.CodCliente', '=', 'clientes.CodCliente')
            ->where('vehiculo.nextdate','>=',$datemin)
            ->where('vehiculo.nextdate','<=',$datemax)
            ->select('clientes.CodCliente','clientes.NombreRaz','clientes.RUC','clientes.DniExt','clientes.DNI','clientes.Direccion','clientes.Distrito','clientes.Contacto1','clientes.Telefonos','clientes.TelefOficina','clientes.Celular','clientes.Email','vehiculo.Placa','vehiculo.Anofab','vehiculo.AnoModelo','vehiculo.Modelo','vehiculo.Version','vehiculo.Tipo','vehiculo.Color','vehiculo.Serie','vehiculo.Nomotor','vehiculo.NroChasis','vehiculo.date1','vehiculo.date2','vehiculo.date3','vehiculo.km1','vehiculo.km2','vehiculo.km3','vehiculo.preventive1','vehiculo.preventive2','vehiculo.preventive3','vehiculo.obs1','vehiculo.obs2','vehiculo.obs3','vehiculo.speed','vehiculo.nextkm','vehiculo.nextdate','status','send_email')
            ->get();
        if (is_null($vehicles)) {
            $vehicles = [];
        }
        //dd($vehicles);
        return view('autorepair.service_checklists.nextservice', compact('request','vehicles'));
    }
    public function formEmail($plate)
    {
        $vehicle = \DB::connection('masaki')->table('vehiculo')
            ->join('clientes', 'vehiculo.CodCliente', '=', 'clientes.CodCliente')
            ->where('vehiculo.Placa','=',$plate)
            ->select('clientes.CodCliente','clientes.NombreRaz','clientes.RUC','clientes.DniExt','clientes.DNI','clientes.Direccion','clientes.Distrito','clientes.Contacto1','clientes.Telefonos','clientes.TelefOficina','clientes.Celular','clientes.Email','vehiculo.Placa','vehiculo.Anofab','vehiculo.AnoModelo','vehiculo.Modelo','vehiculo.Version','vehiculo.Tipo','vehiculo.Color','vehiculo.Serie','vehiculo.Nomotor','vehiculo.NroChasis','vehiculo.date1','vehiculo.date2','vehiculo.date3','vehiculo.km1','vehiculo.km2','vehiculo.km3','vehiculo.preventive1','vehiculo.preventive2','vehiculo.preventive3','vehiculo.obs1','vehiculo.obs2','vehiculo.obs3','vehiculo.speed','vehiculo.nextkm','vehiculo.nextdate')
            ->first();
        $amber_job = $this->repo->findForPlate($plate);
        $checks = null;
        if ($amber_job) {
            $checks['check_warning'] = $this->repo->checksWarning($amber_job->id);
            $checks['check_danger'] = $this->repo->checksDanger($amber_job->id);
        }
        //dd($checks);
        return view('autorepair.service_reminder.send_email', compact('plate', 'vehicle', 'amber_job', 'checks'));
    }
    public function sendEmail(Request $request)
   {
       $data = $request->all();

       $last_page = $data['last_page'];

       \Mail::send('emails.message', $data, function($message) use ($request)
       {
           $message->to($request->email, $request->name);
           $message->subject($request->subject);
           $message->from(env('CONTACT_MAIL'), env('CONTACT_NAME'));
       });
       \DB::connection('masaki')->table('vehiculo')
            ->where('Placa', $data['plate'])
            ->update(array('send_email' => 1));
       return view('autorepair.service_reminder.success', compact('last_page'));
    }
    public function editStatus($id)
    {
        $model = $this->repo->findOrFail($id);
        return view('autorepair.service_checklists.edit_status', compact('model'));
    }
    public function updateStatus($id)
    {
        $this->repo->save(\Request::all(), $id);
        return \Redirect::route('autorepair.service_checklists.index');
    }
    public function editStatusReminder($plate)
    {
        $vehicle = \DB::connection('masaki')->table('vehiculo')
            ->join('clientes', 'vehiculo.CodCliente', '=', 'clientes.CodCliente')
            ->where('vehiculo.Placa','=',$plate)
            ->select('clientes.CodCliente','clientes.NombreRaz','clientes.RUC','clientes.DniExt','clientes.DNI','clientes.Direccion','clientes.Distrito','clientes.Contacto1','clientes.Telefonos','clientes.TelefOficina','clientes.Celular','clientes.Email','vehiculo.Placa','vehiculo.Anofab','vehiculo.AnoModelo','vehiculo.Modelo','vehiculo.Version','vehiculo.Tipo','vehiculo.Color','vehiculo.Serie','vehiculo.Nomotor','vehiculo.NroChasis','vehiculo.date1','vehiculo.date2','vehiculo.date3','vehiculo.km1','vehiculo.km2','vehiculo.km3','vehiculo.preventive1','vehiculo.preventive2','vehiculo.preventive3','vehiculo.obs1','vehiculo.obs2','vehiculo.obs3','vehiculo.speed','vehiculo.nextkm','vehiculo.nextdate','vehiculo.status')
            ->first();
        $amber_job = $this->repo->findForPlate($plate);
        $checks = null;
        if ($amber_job) {
            $checks['check_warning'] = $this->repo->checksWarning($amber_job->id);
            $checks['check_danger'] = $this->repo->checksDanger($amber_job->id);
        }
        $status_list = [''=>'Seleccionar', 'NO'=>'NO CITA', 'CALL_AGAIN'=>'VOLVER A LLAMAR', 'SI'=>'SI CITA'];
        return view('autorepair.service_reminder.edit_status', compact('vehicle', 'status_list', 'checks'));
    }
    public function updateStatusReminder($plate)
    {
        $data=\Request::all();
        $last_page = $data['last_page'];
        \DB::connection('masaki')->table('vehiculo')
            ->where('Placa', $plate)
            ->update(array('status' => $data['status']));
        return redirect($last_page);
        //return \Redirect::route('autorepair.service_checklists.index');
    }
}