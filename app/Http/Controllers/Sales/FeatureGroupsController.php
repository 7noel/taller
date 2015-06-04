<?php namespace App\Http\Controllers\Sales;

use App\Http\Requests;
use App\Http\Controllers\Controller;

use Illuminate\Http\Request;
use App\Modules\Sales\FeatureGroupRepo;

class FeatureGroupsController extends Controller {

	protected $repo;

	public function __construct(FeatureGroupRepo $repo) {
		$this->repo = $repo;
	}

	public function index()
	{
		$models = $this->repo->index('name', \Request::get('name'));
		return view('partials.index',compact('models'));
	}

	public function create()
	{
		$templates = $this->repo->getListTemplates();
		return view('partials.create', compact('templates'));
	}

	public function store()
	{
		$this->repo->save(\Request::all());
		return \Redirect::route('sales.feature_groups.index');
	}

	public function show($id)
	{
		//
	}

	public function edit($id)
	{
		$model = $this->repo->findOrFail($id);
		$templates = $this->repo->getListTemplates();
		return view('partials.edit', compact('model', 'templates'));
	}

	public function update($id)
	{
		$this->repo->save(\Request::all(), $id);
		return \Redirect::route('sales.feature_groups.index');
	}

	public function destroy($id)
	{
		$model = $this->repo->destroy($id);
		if (\Request::ajax()) {	return $model; }
		return redirect()->route('sales.feature_groups.index');
	}
}