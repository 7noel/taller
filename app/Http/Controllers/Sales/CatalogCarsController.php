<?php namespace App\Http\Controllers\Sales;

use App\Http\Requests;
use App\Http\Controllers\Controller;

use Illuminate\Http\Request;
use App\Modules\Sales\CatalogCarRepo;
use App\Modules\Sales\VersionRepo;
use App\Modules\Sales\FeatureGroupRepo;

class CatalogCarsController extends Controller {

	protected $repo;
	protected $versionRepo;
	protected $featureGroupRepo;

	public function __construct(CatalogCarRepo $repo, VersionRepo $versionRepo, FeatureGroupRepo $featureGroupRepo) {
		$this->repo = $repo;
		$this->versionRepo = $versionRepo;
		$this->featureGroupRepo = $featureGroupRepo;
	}

	public function index()
	{
		$models = $this->repo->index('name', \Request::get('name'));
		return view('partials.index',compact('models'));
	}

	public function create()
	{
		$versions = $this->versionRepo->getList();
		$groups = $this->featureGroupRepo->all();
		return view('sales.catalog_cars.create', compact('versions', 'groups'));
	}

	public function store()
	{
		$this->repo->save(\Request::all());
		return \Redirect::route('sales.catalog_cars.index');
	}

	public function show($id)
	{
		//
	}

	public function edit($id)
	{
		$model = $this->repo->findOrFail($id);
		$versions = $this->versionRepo->getList();
		return view('partials.edit', compact('model', 'versions'));
	}

	public function update($id)
	{
		$this->repo->save(\Request::all(), $id);
		return \Redirect::route('sales.catalog_cars.index');
	}

	public function destroy($id)
	{
		$model = $this->repo->destroy($id);
		if (\Request::ajax()) {	return $model; }
		return redirect()->route('sales.catalog_cars.index');
	}
}