<?php namespace App\Http\Controllers\Sales;

use App\Http\Requests;
use App\Http\Controllers\Controller;

use Illuminate\Http\Request;
use App\Modules\Sales\ClienteRepo;
use App\Modules\Sales\Cliente;
use App\Modules\Base\UbigeoRepo;
use App\Modules\Sales\CatalogCarRepo;
use App\Modules\Sales\AfluenciaRepo;

class ClientesController extends Controller {

	protected $repo;
	protected $ubigeoRepo;
	protected $afluenciaRepo;
	protected $id_types = ['DNI'=>'DNI', 'CEX'=>'CEX', 'PAS'=>'PAS', 'RUC'=>'RUC'];
	protected $canals = array(
			'' => 'Seleccionar',
			'CLIENTE MASAKI' => 'CLIENTE MASAKI',
			'CLIENTE VINO DE OTRO CONCESIONARIO' => 'CLIENTE VINO DE OTRO CONCESIONARIO',
			'CONTACTO WEB HONDA' => 'CONTACTO WEB HONDA',
			'CONTACTO WEB MASAKI' => 'CONTACTO WEB MASAKI',
			'DIARIO' => 'DIARIO',
			'E-MAILING MASAKI' => 'E-MAILING MASAKI',
			'LLAMADA TELEFONICA' => 'LLAMADA TELEFONICA',
			'OTROS' => 'OTROS',
			'POR EVENTO' => 'POR EVENTO',
			'RECOMENDACION CLIENTE' => 'RECOMENDACION CLIENTE',
			'REFERIDOS DIRECTORIO/GERENCIA' => 'REFERIDOS DIRECTORIO/GERENCIA',
			'REFERIDOS HONDA' => 'REFERIDOS HONDA',
			'REVISTAS' => 'REVISTAS',
			'TRABAJO CERCA' => 'TRABAJO CERCA',
			'VISITO ZONA AUTOS' => 'VISITO ZONA AUTOS',
			'VIVO CERCA' => 'VIVO CERCA',
			'VOLANTEO' => 'VOLANTEO'
			);

	public function __construct(ClienteRepo $repo, UbigeoRepo $ubigeoRepo, CatalogCarRepo $carRepo, AfluenciaRepo $afluenciaRepo) {
		$this->repo = $repo;
		$this->ubigeoRepo = $ubigeoRepo;
		$this->carRepo = $carRepo;
		$this->afluenciaRepo = $afluenciaRepo;
	}

	public function index()
	{
		$models = $this->repo->index('name', \Request::get('name'));
		return view('partials.index',compact('models'));
	}

	public function create()
	{
		$id_types = $this->id_types;
		$ubigeo = $this->ubigeoRepo->listUbigeo2();
		$versions = $this->carRepo->getListVersions();
		$canals = $this->canals;
		$afluencia = null;
		return view('partials.create', compact('id_types','ubigeo','versions', 'canals', 'afluencia'));
	}

	public function store()
	{
		$data = \Request::all();
		if($data['DniExt'] != 'RUC') {
			$data['NombreRaz'] = $data['ApellidoPat'].' '.$data['ApellidoMat'].' '.$data['Nombre'];
		}
		$data['of_sales'] = true;
		$data['Letra'] = $data['NombreRaz'][0];
		$data['Rubroneg'] = 'NO ESPECIFICADO';
		$data['Profesion'] = 'NO ESPECIFICADO';
		$data['Fecha1'] = date('Y-m-d');
		$data['Hora1'] = date('H:i:s');
		$data['Usuario1'] = \Auth::user()->name;
		$this->repo->save($data);
		return \Redirect::route('sales.clientes.index');
	}

	public function show($id)
	{
		//
	}

	public function edit($id)
	{
		//dd(\Auth::user()->employee->full_name);
		$id_types = $this->id_types;
		$model = $this->repo->findOrFail($id);
		$ubigeo = $this->ubigeoRepo->listUbigeo2($model->Departam, $model->Provincia, $model->Distrito);
		if ($model->RUC > 0) {
			$model->DniExt = 'RUC';
			$model->DNI = $model->RUC;
		}
		$versions = $this->carRepo->getListVersions();
		$canals = $this->canals;
		//$afluencia = $this->afluenciaRepo->lastAfluencia($model->CodCliente);
		$afluencias = $this->afluenciaRepo->byCliente($model->CodCliente);
		$tipo = ['PRESENCIAL'=>false, 'TELEFONICA'=>false, 'VIRTUAL'=>false];
		$formapago = ['CONTADO'=>false, 'CREDITO'=>false, 'LEASING'=>false];
		$years = [''=>'Seleccionar'];
		if (isset($afluencias[0])) {
			$years = $this->carRepo->getListYears($afluencias[0]->version_id);
			$tipo[$afluencias[0]->tipo] = true;
			$formapago[$afluencias[0]->formapago] = true;
		}
		return view('sales.clientes.edit', compact('model', 'id_types', 'ubigeo','versions', 'canals', 'years', 'tipo', 'afluencias', 'formapago'));
	}

	public function update($id)
	{
		$data = \Request::all();
		if($data['DniExt'] != 'RUC') {
			$data['NombreRaz'] = $data['ApellidoPat'].' '.$data['ApellidoMat'].' '.$data['Nombre'];
		} else {
			$data['RUC'] = $data['DNI'];
		}
		$data['Letra'] = $data['NombreRaz'][0];
		$data['Rubroneg'] = 'NO ESPECIFICADO';
		$data['Profesion'] = 'NO ESPECIFICADO';
		$data['Fecha2'] = date('Y-m-d');
		$data['Hora2'] = date('H:i:s');
		$data['Usuario2'] = \Auth::user()->name;
		$this->repo->save($data, $id);
		return \Redirect::route('sales.clientes.index');
	}

	public function destroy($id)
	{
		$model = $this->repo->destroy($id);
		if (\Request::ajax()) {	return $model; }
		return redirect()->route('sales.clientes.index');
	}
	public function ajaxAutocomplete()
	{
		$term = \Input::get('term');
		$models = $this->repo->autocomplete($term);
		$result = [];
		foreach ($models as $model) {
			$result[]=[
				'value' => $model->company_name,
				'id' => $model->id,
				'label' => $model->id_type->symbol.' '.$model->doc.' '.$model->company_name
			];
		}
		return \Response::json($result);
	}
}