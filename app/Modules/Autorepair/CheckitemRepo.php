<?php namespace App\Modules\AutoRepair;

use App\Modules\Base\BaseRepo;
use App\Modules\AutoRepair\Checkitem;

class CheckitemRepo extends BaseRepo{

	public function getModel(){
		return new Checkitem;
	}
	public function prepareData($data)
	{
		if (!isset($data['with_status'])) {
			$data['with_status'] = false;
		}
		if (!isset($data['with_value'])) {
			$data['with_value'] = false;
		}
		if (!isset($data['with_check'])) {
			$data['with_check'] = false;
		}
		if (!isset($data['column_two'])) {
			$data['column_two'] = false;
		}
		return $data;
	}
}