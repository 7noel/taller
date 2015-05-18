<?php namespace App\Http\Controllers\Finances;

use App\Http\Requests;
use App\Http\Controllers\Controller;

use Illuminate\Http\Request;
use App\Modules\Base\IdTypeRepo;
use App\Modules\Finances\CompanyRepo;
use App\Modules\Base\UbigeoRepo;

use App\Http\Requests\Finances\FormCompanyRequest;

class CompaniesController extends Controller {

	protected $repo;
	protected $ubigeoRepo;
	protected $idTypeRepo;

	public function __construct(CompanyRepo $repo, UbigeoRepo $ubigeoRepo, IdTypeRepo $idTypeRepo) {
		$this->repo = $repo;
		$this->ubigeoRepo = $ubigeoRepo;
		$this->idTypeRepo = $idTypeRepo;
	}

	public function index()
	{
		$models = $this->repo->index('name', \Request::get('name'));
		return view('partials.index',compact('models'));
	}

	public function create()
	{
		$id_types = $this->idTypeRepo->getList('symbol');
		$ubigeo = $this->ubigeoRepo->listUbigeo();
		return view('partials.create', compact('id_types','ubigeo'));
	}

	public function store(FormCompanyRequest $request)
	{
		$this->repo->save(\Request::all());
		return \Redirect::route('finances.companies.index');
	}

	public function show($id)
	{
		//
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
		$this->repo->save(\Request::all(), $id);
		return \Redirect::route('finances.companies.index');
	}

	public function destroy($id)
	{
		$model = $this->repo->destroy($id);
		if (\Request::ajax()) {	return $model; }
		return redirect()->route('finances.companies.index');
	}

}
