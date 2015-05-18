<?php namespace App\Http\Controllers\Storage;

use App\Http\Requests;
use App\Http\Controllers\Controller;

use Illuminate\Http\Request;
use App\Modules\Storage\UnitRepo;
use App\Modules\Base\UnitTypeRepo;

use App\Http\Requests\Logistics\FormUnitRequest;

class UnitsController extends Controller {

	protected $repo;
	protected $unitTypeRepo;

	public function __construct(UnitRepo $repo, UnitTypeRepo $unitTypeRepo) {
		$this->repo = $repo;
		$this->unitTypeRepo = $unitTypeRepo;
	}

	public function index()
	{
		$models = $this->repo->index('name', \Request::get('name'));
		return view('partials.index',compact('models'));
	}

	public function create()
	{
		$unit_types = $this->unitTypeRepo->getList();
		return view('partials.create', compact('unit_types'));
	}

	public function store(FormUnitRequest $request)
	{
		$this->repo->save(\Request::all());
		return \Redirect::route('storage.units.index');
	}

	public function show($id)
	{
		//
	}

	public function edit($id)
	{
		$unit_types = $this->unitTypeRepo->getList();
		$model = $this->repo->findOrFail($id);
		return view('partials.edit', compact('model', 'unit_types'));
	}

	public function update($id, FormUnitRequest $request)
	{
		$this->repo->save(\Request::all(), $id);
		return \Redirect::route('storage.units.index');
	}

	public function destroy($id)
	{
		$model = $this->repo->destroy($id);
		if (\Request::ajax()) {	return $model; }
		return redirect()->route('storage.units.index');
	}
	public function ajaxList($uni_type_id)
	{
		$ajax = $this->repo->ajaxList($uni_type_id);
		return \Response::json($ajax);
	}
}
