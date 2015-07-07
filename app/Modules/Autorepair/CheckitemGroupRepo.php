<?php namespace App\Modules\AutoRepair;

use App\Modules\Base\BaseRepo;
use App\Modules\AutoRepair\CheckitemGroup;
use App\Modules\AutoRepair\CheckitemRepo;

class CheckitemGroupRepo extends BaseRepo{

	public function getModel(){
		return new CheckitemGroup;
	}
	public function prepareData($data)
	{
		if (!isset($data['is_double_column'])) {
			$data['is_double_column'] = false;
		}
		return $data;
	}
	public function save($data, $id=0)
	{
		$model = parent::save($data, $id);
		if (isset($data['checkitems'])) {
			$checkitemRepo= new CheckitemRepo;
			$checkitemRepo->saveMany($data['checkitems'], ['key'=>'checkitem_group_id', 'value'=>$model->id]);
		}

		return $model;
	}

}