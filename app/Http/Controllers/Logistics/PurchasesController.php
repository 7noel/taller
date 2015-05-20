<?php namespace App\Http\Controllers\Logistics;

use App\Http\Requests;
use App\Http\Controllers\Controller;

use Illuminate\Http\Request;
use App\Modules\Logistics\PurchaseRepo;
use App\Modules\Base\DocumentTypeRepo;
use App\Modules\Base\CurrencyRepo;
use App\Modules\Finances\CompanyRepo;
use App\Modules\Finances\PaymentConditionRepo;

class PurchasesController extends Controller {

	protected $repo;
	protected $documentTypeRepo;
	protected $currencyRepo;
	protected $companyRepo;
	protected $paymentConditionRepo;

	public function __construct(PurchaseRepo $repo, DocumentTypeRepo $documentTypeRepo, CurrencyRepo $currencyRepo, CompanyRepo $companyRepo, PaymentConditionRepo $paymentConditionRepo) {
		$this->repo = $repo;
		$this->documentTypeRepo = $documentTypeRepo;
		$this->currencyRepo = $currencyRepo;
		$this->companyRepo = $companyRepo;
		$this->paymentConditionRepo = $paymentConditionRepo;
	}

	public function index()
	{
		$models = $this->repo->index('date', \Request::get('name'));
		return view('partials.index',compact('models'));
	}

	public function create()
	{
		$document_types = $this->documentTypeRepo->getList();
		$currencies = $this->currencyRepo->getList2();
		$payment_conditions = $this->paymentConditionRepo->getList();
		return view('partials.create', compact('document_types', 'currencies', 'payment_conditions'));
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
		return view('partials.edit', compact('model'));
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