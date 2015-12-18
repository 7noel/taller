<?php namespace App\Http\Controllers\Sales;

use App\Http\Requests;
use App\Http\Controllers\Controller;

use Illuminate\Http\Request;
use App\Modules\Sales\ClienteRepo;
use App\Modules\Base\UbigeoRepo;

class ClientesController extends Controller {

	protected $repo;
	protected $ubigeoRepo;
	protected $id_types = ['DNI'=>'DNI', 'CEX'=>'CEX', 'PAS'=>'PAS', 'RUC'=>'RUC'];

	public function __construct(ClienteRepo $repo, UbigeoRepo $ubigeoRepo) {
		$this->repo = $repo;
		$this->ubigeoRepo = $ubigeoRepo;
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
		return view('partials.create', compact('id_types','ubigeo'));
	}

	public function store()
	{
		$data = \Request::all();
		dd($data);
		$this->repo->save($data);
		if (isset($data['last_page']) && $data['last_page'] != '') {
			return \Redirect::to($data['last_page']);
		}
		return \Redirect::route('finances.companies.index');
	}

	public function show($id)
	{
		$id_types = $this->idTypeRepo->getList('symbol');
		$model = $this->repo->findOrFail($id);
		$ubigeo = $this->ubigeoRepo->listUbigeo($model->ubigeo_id);
		return view('partials.show', compact('model', 'id_types', 'ubigeo'));
	}

	public function edit($id)
	{
		$id_types = $this->idTypeRepo->getList('symbol');
		$model = $this->repo->findOrFail($id);
		$ubigeo = $this->ubigeoRepo->listUbigeo($model->ubigeo_id);
		return view('partials.edit', compact('model', 'id_types', 'ubigeo'));
	}

	public function update($id, FormCompanyRequest $request)
	{
		$data = \Request::all();
		$this->repo->save($data, $id);
		if (isset($data['last_page']) && $data['last_page'] != '') {
			return \Redirect::to($data['last_page']);
		}
		return \Redirect::route('finances.companies.index');
	}

	public function destroy($id)
	{
		$model = $this->repo->destroy($id);
		if (\Request::ajax()) {	return $model; }
		return redirect()->route('finances.companies.index');
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