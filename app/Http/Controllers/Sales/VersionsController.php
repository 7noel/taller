<?php namespace App\Http\Controllers\Sales;

use App\Http\Requests;
use App\Http\Controllers\Controller;

use Illuminate\Http\Request;
use App\Modules\Sales\ModelRepo;
use App\Modules\Sales\VersionRepo;

class VersionsController extends Controller {

	protected $repo;
	protected $modelRepo;

	public function __construct(VersionRepo $repo, ModelRepo $modelRepo) {
		$this->repo = $repo;
		$this->modelRepo = $modelRepo;
	}

	public function index()
	{
		$models = $this->repo->index('name', \Request::get('name'));
		return view('partials.index',compact('models'));
	}

	public function create()
	{
		$models = $this->modelRepo->getList();
		return view('partials.create', compact('models'));
	}

	public function store()
	{
		$this->repo->save(\Request::all());
		return \Redirect::route('sales.versions.index');
	}

	public function show($id)
	{
		//
	}

	public function edit($id)
	{
		$model = $this->repo->findOrFail($id);
		$models = $this->modelRepo->getList();
		return view('partials.edit', compact('model', 'models'));
	}

	public function update($id)
	{
		$this->repo->save(\Request::all(), $id);
		return \Redirect::route('sales.versions.index');
	}

	public function destroy($id)
	{
		$model = $this->repo->destroy($id);
		if (\Request::ajax()) {	return $model; }
		return redirect()->route('sales.versions.index');
	}
}