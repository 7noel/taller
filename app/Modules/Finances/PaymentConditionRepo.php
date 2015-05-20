<?php 

namespace App\Modules\Finances;

use App\Modules\Base\BaseRepo;
use App\Modules\Finances\PaymentCondition;

class PaymentConditionRepo extends BaseRepo{

	public function getModel(){
		return new PaymentCondition;
	}
	public function prepareData($data)
	{
		if (!isset($data['to_sales'])) {
			$data['to_sales'] = false;
		}
		if (!isset($data['to_purchases'])) {
			$data['to_purchases'] = false;
		}
		return $data;
	}
}