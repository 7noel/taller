<?php namespace App\Modules\Logistics;

use App\Modules\Base\BaseRepo;
use App\Modules\Logistics\PurchaseDetail;

class PurchaseDetailRepo extends BaseRepo{

	public function getModel(){
		return new PurchaseDetail;
	}
}