<?php namespace App\Http\Controllers\Sales;

use App\Http\Requests;
use App\Http\Controllers\Controller;

use Illuminate\Http\Request;
use App\Modules\Sales\CarQuoteRepo;
use App\Modules\Sales\CatalogCarRepo;
use App\Modules\Base\CurrencyRepo;
use App\Modules\Sales\FeatureGroupRepo;

class CarQuotesController extends Controller {

	protected $repo;
	protected $carRepo;
	protected $currencyRepo;
	protected $featureGroupRepo;

	public function __construct(CarQuoteRepo $repo, CatalogCarRepo $carRepo, CurrencyRepo $currencyRepo, FeatureGroupRepo $featureGroupRepo) {
		$this->repo = $repo;
		$this->carRepo = $carRepo;
		$this->currencyRepo = $currencyRepo;
		$this->featureGroupRepo = $featureGroupRepo;
	}

	public function index()
	{
		$models = $this->repo->index('attention', \Request::get('name'));
		return view('partials.index',compact('models'));
	}

	public function create()
	{
		$versions = $this->carRepo->getListVersions();
		$currencies = $this->currencyRepo->getList();
		return view('partials.create', compact('versions', 'currencies'));
	}

	public function store()
	{
		$data = \Request::all();
		$data['employee_id'] = \Auth::user()->employee->id;
		$this->repo->save($data);
		return \Redirect::route('sales.car_quotes.index');
	}

	public function show($id)
	{
		//
	}

	public function edit($id)
	{
		$model = $this->repo->findOrFail($id);
		$versions = $this->carRepo->getListVersions();
		$years = $this->carRepo->getListYears($model->catalog_car->version_id);
		return view('partials.edit', compact('model', 'versions', 'years'));
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

	/**
	 * CREA UN PDF EN EL NAVEGADOR
	 * @param  [integer] $id [Es el id de la cotizacion]
	 * @return [pdf]     [Retorna un pdf]
	 */
	public function print_out($id)
	{
		$quote = $this->repo->findOrFail($id);
		$groups = $this->featureGroupRepo->byCatalogCar($quote->catalog_car_id);
		$pdf = \PDF::loadView('pdfs.car_quote', compact('quote', 'groups'));

		return $pdf->stream();
	}
	/**
	 * [afluencia description]
	 * @return [type] [description]
	 */
	public function afluencia()
	{
		return view('sales.car_quotes.afluencia');
	}
	public function afluencia_form()
	{
		$versions = $this->carRepo->getListVersions();
		$currencies = $this->currencyRepo->getList();
		$canals = array('CLIENTE MASAKI',
			'CLIENTE VINO DE OTRO CONCESIONARIO',
			'CONTACTO WEB HONDA',
			'CONTACTO WEB MASAKI',
			'DIARIO',
			'E-MAILING MASAKI',
			'LLAMADA TELEFONICA',
			'OTROS',
			'POR EVENTO',
			'RECOMENDACION CLIENTE',
			'REFERIDOS DIRECTORIO/GERENCIA',
			'REFERIDOS HONDA',
			'REVISTAS',
			'TRABAJO CERCA',
			'VISITO ZONA AUTOS',
			'VIVO CERCA',
			'VOLANTEO'

			);
		return view('sales.car_quotes.afluencia_form', compact('versions', 'currencies', 'canals'));
	}
	public function byCompany($company_id)
	{
		$models = $this->repo->byCompany($company_id);
		return view('partials.index',compact('models'));
	}
}