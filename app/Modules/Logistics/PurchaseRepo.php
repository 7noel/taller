<?php namespace App\Modules\Logistics;

use App\Modules\Base\BaseRepo;
use App\Modules\Logistics\Purchase;

class PurchaseRepo extends BaseRepo{

	public function getModel(){
		return new Purchase;
	}
	public function index($filter = false, $search = false)
	{
		if ($filter and $search) {
			return Purchase::$filter($search)->with('company', 'document_type', 'payment_condition', 'currency')->orderBy("$filter", 'ASC')->paginate();
		} else {
			return Purchase::orderBy('id', 'DESC')->with('company', 'document_type', 'payment_condition', 'currency')->paginate();
		}
	}
}