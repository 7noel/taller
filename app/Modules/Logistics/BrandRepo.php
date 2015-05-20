<?php namespace App\Modules\Logistics;

use App\Modules\Base\BaseRepo;
use App\Modules\Logistics\Brand;

class BrandRepo extends BaseRepo{

	public function getModel(){
		return new Brand;
	}
	public function prepareData($data)
	{
		if (!isset($data['is_car'])) {
			$data['is_car'] = false;
		}
		return $data;
	}
}