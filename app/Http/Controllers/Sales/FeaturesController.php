<?php namespace App\Http\Controllers\Sales;

use App\Http\Requests;
use App\Http\Controllers\Controller;

use Illuminate\Http\Request;
use App\Modules\Sales\FeatureRepo;
use App\Modules\Sales\FeatureGroupRepo;

class FeaturesController extends Controller {

	protected $repo;
	protected $groupRepo;

	public function __construct(FeatureRepo $repo, FeatureGroupRepo $groupRepo) {
		$this->repo = $repo;
		$this->groupRepo = $groupRepo;
	}

	public function index()
	{
		$models = $this->repo->index('name', \Request::get('name'));
		return view('partials.index',compact('models'));
	}

	public function create()
	{
		$groups = $this->groupRepo->getList();
		return view('partials.create', compact('groups'));
	}

	public function store()
	{
		$this->repo->save(\Request::all());
		return \Redirect::route('sales.features.index');
	}

	public function show($id)
	{
		//
	}

	public function edit($id)
	{
		$model = $this->repo->findOrFail($id);
		$groups = $this->groupRepo->getList();
		return view('partials.edit', compact('model', 'groups'));
	}

	public function update($id)
	{
		$this->repo->save(\Request::all(), $id);
		return \Redirect::route('sales.features.index');
	}

	public function destroy($id)
	{
		$model = $this->repo->destroy($id);
		if (\Request::ajax()) {	return $model; }
		return redirect()->route('sales.features.index');
	}
}