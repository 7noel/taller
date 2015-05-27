<?php namespace App\Modules\Logistics;

use App\Modules\Base\BaseRepo;
use App\Modules\Logistics\Purchase;
use App\Modules\Logistics\PurchaseDetailRepo;

class PurchaseRepo extends BaseRepo{

	public function getModel(){
		return new Purchase;
	}
	public function index($filter = false, $search = false)
	{
		if ($filter and $search) {
			return Purchase::$filter($search)->with('company', 'document_type', 'payment_condition', 'currency', 'purchase_details')->orderBy("$filter", 'ASC')->paginate();
		} else {
			return Purchase::orderBy('id', 'DESC')->with('company', 'document_type', 'payment_condition', 'currency', 'purchase_details')->paginate();
		}
	}
	public function prepareData($data)
	{
		if (isset($data['purchase_details'])) {
			foreach ($data['purchase_details'] as $key => $value) {
				$data['purchase_details'][$key]['total'] = $data['purchase_details'][$key]['price'] * $data['purchase_details'][$key]['quantity'] * (100- $data['purchase_details'][$key]['discount']) / 100;
			}
		}
		return $data;
	}
	public function save($data, $id=0)
	{
		$model = parent::save($data, $id);
		if (isset($data['purchase_details'])) {
			$detailRepo= new PurchaseDetailRepo;
			$detailRepo->syncMany($data['purchase_details'], ['key'=>'purchase_id', 'value'=>$model->id], 'stock_id');
		}
		return $model;
	}
}