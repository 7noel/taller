<?php namespace App\Http\Controllers\Sales;

use App\Http\Requests;
use App\Http\Controllers\Controller;

use Illuminate\Http\Request;
use App\Modules\Sales\CarQuoteRepo;
use App\Modules\Sales\CatalogCarRepo;
use App\Modules\Base\CurrencyRepo;

class CarQuotesController extends Controller {

	protected $repo;
	protected $carRepo;
	protected $currencyRepo;

	public function __construct(CarQuoteRepo $repo, CatalogCarRepo $carRepo, CurrencyRepo $currencyRepo) {
		$this->repo = $repo;
		$this->carRepo = $carRepo;
		$this->currencyRepo = $currencyRepo;
	}

	public function index()
	{
		$models = $this->repo->index('name', \Request::get('name'));
		return view('partials.index',compact('models'));
	}

	public function create()
	{
		$versions = $this->carRepo->getListVersions();
		$currencies = $this->currencyRepo->getList2();
		return view('partials.create', compact('versions', 'currencies'));
	}

	public function store()
	{
		$this->repo->save(\Request::all());
		return \Redirect::route('sales.car_quotes.index');
	}

	public function show($id)
	{
		//
	}

	public function edit($id)
	{
		$model = $this->repo->findOrFail($id);
		$cars = $this->carRepo->getListVersions();
		$currencies = $this->currencyRepo->getList2();
		return view('partials.edit', compact('model', 'cars', 'currencies'));
	}

	public function update($id)
	{
		$this->repo->save(\Request::all(), $id);
		return \Redirect::route('sales.car_quotes.index');
	}

	public function destroy($id)
	{
		$model = $this->repo->destroy($id);
		if (\Request::ajax()) {	return $model; }
		return redirect()->route('sales.car_quotes.index');
	}
}