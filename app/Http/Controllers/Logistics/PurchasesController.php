<?php namespace App\Http\Controllers\Logistics;

use App\Http\Requests;
use App\Http\Controllers\Controller;

use Illuminate\Http\Request;
use App\Modules\Logistics\PurchaseRepo;
use App\Modules\Base\DocumentTypeRepo;
use App\Modules\Base\CurrencyRepo;
use App\Modules\Finances\CompanyRepo;
use App\Modules\Finances\PaymentConditionRepo;
use App\Modules\Storage\WarehouseRepo;

class PurchasesController extends Controller {

	protected $repo;
	protected $documentTypeRepo;
	protected $currencyRepo;
	protected $companyRepo;
	protected $paymentConditionRepo;
	protected $warehouseRepo;

	public function __construct(PurchaseRepo $repo, DocumentTypeRepo $documentTypeRepo, CurrencyRepo $currencyRepo, CompanyRepo $companyRepo, PaymentConditionRepo $paymentConditionRepo, WarehouseRepo $warehouseRepo) {
		$this->repo = $repo;
		$this->documentTypeRepo = $documentTypeRepo;
		$this->currencyRepo = $currencyRepo;
		$this->companyRepo = $companyRepo;
		$this->paymentConditionRepo = $paymentConditionRepo;
		$this->warehouseRepo = $warehouseRepo;
	}

	public function index()
	{
		$models = $this->repo->index('date', \Request::get('name'));
		return view('partials.index',compact('models'));
	}

	public function create()
	{
		$document_types = $this->documentTypeRepo->getList();
		$currencies = $this->currencyRepo->getList();
		$payment_conditions = $this->paymentConditionRepo->getList();
		$warehouses = $this->warehouseRepo->getList('id','id');
		$items = 0;
		$purchase_details = [];
		return view('logistics.purchases.create', compact('document_types', 'currencies', 'payment_conditions', 'warehouses','items','purchase_details'));
	}

	public function store()
	{
		$this->repo->save(\Request::all());
		return \Redirect::route('logistics.purchases.index');
	}

	public function show($id)
	{
		//
	}

	public function edit($id)
	{
		$model = $this->repo->findOrFail($id);
		$document_types = $this->documentTypeRepo->getList();
		$currencies = $this->currencyRepo->getList();
		$payment_conditions = $this->paymentConditionRepo->getList();
		$warehouses = $this->warehouseRepo->getList('id','id');
		$items = 0;
		$purchase_details = [];
		return view('logistics.purchases.edit', compact('model','document_types', 'currencies', 'payment_conditions', 'warehouses','items','purchase_details'));
	}

	public function update($id)
	{
		$this->repo->save(\Request::all(), $id);
		return \Redirect::route('logistics.purchases.index');
	}

	public function destroy($id)
	{
		$model = $this->repo->destroy($id);
		if (\Request::ajax()) {	return $model; }
		return redirect()->route('logistics.purchases.index');
	}
}