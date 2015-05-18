<?php namespace App\Http\Controllers\Storage;

use App\Http\Requests;
use App\Http\Controllers\Controller;

use Illuminate\Http\Request;
use App\Modules\Storage\WarehouseRepo;
use App\Modules\Base\UbigeoRepo;

use App\Http\Requests\Storage\FormWarehouseRequest;

class WarehousesController extends Controller {

	protected $repo;
	protected $ubigeoRepo;

	public function __construct(WarehouseRepo $repo, UbigeoRepo $ubigeoRepo) {
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
		$ubigeo = $this->ubigeoRepo->listUbigeo();
		return view('partials.create', compact('ubigeo'));
	}

	public function store(FormWarehouseRequest $request)
	{
		$this->repo->save(\Request::all());
		return \Redirect::route('storage.warehouses.index');
	}

	public function show($id)
	{
		//
	}

	public function edit($id)
	{
		$model = $this->repo->findOrFail($id);
		$ubigeo = $this->ubigeoRepo->listUbigeo($model->ubigeo_id);
		return view('partials.edit', compact('model', 'ubigeo'));
	}

	public function update($id, FormWarehouseRequest $request)
	{
		$this->repo->save(\Request::all(), $id);
		return \Redirect::route('storage.warehouses.index');
	}

	public function destroy($id)
	{
		$model = $this->repo->destroy($id);
		if (\Request::ajax()) {	return $model; }
		return redirect()->route('storage.warehouses.index');
	}
	public function ajaxList()
	{
		$ajax = $this->repo->ajaxList();
		return \Response::json($ajax);
	}
}
