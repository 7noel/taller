<?php namespace App\Http\Controllers\Sales;

use App\Http\Requests;
use App\Http\Controllers\Controller;

use Illuminate\Http\Request;
use App\Modules\Logistics\BrandRepo;
use App\Modules\Sales\ModeloRepo;

class ModelosController extends Controller {

	protected $repo;
	protected $brandRepo;

	public function __construct(ModeloRepo $repo, BrandRepo $brandRepo) {
		$this->repo = $repo;
		$this->brandRepo = $brandRepo;
	}

	public function index()
	{
		$models = $this->repo->index('name', \Request::get('name'));
		return view('partials.index',compact('models'));
	}

	public function create()
	{
		$brands = $this->brandRepo->getList();
		return view('partials.create', compact('brands'));
	}

	public function store()
	{
		$this->repo->save(\Request::all());
		return \Redirect::route('sales.modelos.index');
	}

	public function show($id)
	{
		//
	}

	public function edit($id)
	{
		$model = $this->repo->findOrFail($id);
		$brands = $this->brandRepo->getList();
		return view('partials.edit', compact('model', 'brands'));
	}

	public function update($id)
	{
		$this->repo->save(\Request::all(), $id);
		return \Redirect::route('sales.modelos.index');
	}

	public function destroy($id)
	{
		$model = $this->repo->destroy($id);
		if (\Request::ajax()) {	return $model; }
		return redirect()->route('sales.modelos.index');
	}
}