<?php namespace App\Http\Controllers\Sales;

use App\Http\Requests;
use App\Http\Controllers\Controller;

use Illuminate\Http\Request;
use App\Modules\Sales\CatalogCarRepo;
use App\Modules\Sales\VersionRepo;
use App\Modules\Sales\FeatureGroupRepo;
use App\Modules\Base\CurrencyRepo;

class CatalogCarsController extends Controller {

	protected $repo;
	protected $versionRepo;
	protected $featureGroupRepo;
	protected $currencyRepo;

	public function __construct(CatalogCarRepo $repo, VersionRepo $versionRepo, FeatureGroupRepo $featureGroupRepo, CurrencyRepo $currencyRepo) {
		$this->repo = $repo;
		$this->versionRepo = $versionRepo;
		$this->featureGroupRepo = $featureGroupRepo;
		$this->currencyRepo = $currencyRepo;
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
		return view('sales.catalog_cars.create', compact('versions', 'groups', 'currencies'));
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
		$groups = $this->featureGroupRepo->all();
		return view('sales.catalog_cars.edit', compact('model', 'versions', 'groups', 'currencies'));
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

	public function ajaxList($version_id)
	{
		$ajax = $this->repo->ajaxList($version_id);
		return \Response::json($ajax);
	}
	public function ajaxGetCar($catalog_car_id)
	{
		$model = $this->repo->getmodel();
		return \Response::json($model->with('currency')->findOrFail($catalog_car_id));
	}
}