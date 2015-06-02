<?php namespace App\Http\Controllers\Finances;

use App\Http\Requests;
use App\Http\Controllers\Controller;

use Illuminate\Http\Request;
use App\Modules\Base\Exchange;
use App\Modules\Base\IdType;
use App\Modules\Finances\ExchangeRepo;
use App\Modules\Base\CurrencyRepo;

use App\Http\Requests\Finances\FormExchangeRequest;

class ExchangesController extends Controller {

	protected $repo;
	protected $currencyRepo;

	public function __construct(CurrencyRepo $currencyRepo, ExchangeRepo $repo) {
		$this->repo = $repo;
		$this->currencyRepo = $currencyRepo;
	}
	
	public function index()
	{
		$models = $this->repo->index('name', \Request::get('name'));
		return view('partials.index',compact('models'));
	}

	public function create()
	{
		$currencies = $this->currencyRepo->getList();
		return view('partials.create', compact('currencies'));
	}

	public function store(FormExchangeRequest $request)
	 
	{
		$this->repo->save(\Request::all());
		return \Redirect::route('finances.exchanges.index');
	}

	public function show($id)
	{
		//
	}

	public function edit($id)
	{
		$model = $this->repo->findOrFail($id);
		$currencies = $this->currencyRepo->getList();
		return view('partials.edit', compact('model', 'currencies'));
	}

	public function update($id, FormExchangeRequest $request)
	{
		$this->repo->save(\Request::all(), $id);
		return \Redirect::route('finances.exchanges.index');
	}

	public function destroy($id)
	{
		$model = $this->repo->destroy($id);
		if (\Request::ajax()) {	return $model; }
		return redirect()->route('finances.exchanges.index');
	}

}
