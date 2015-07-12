<?php namespace App\Modules\AutoRepair;

use App\Modules\Base\BaseRepo;
use App\Modules\AutoRepair\ServiceChecklist;
use App\Modules\AutoRepair\Checkitem;

class ServiceChecklistRepo extends BaseRepo{

	public function getModel(){
		return new ServiceChecklist;
	}
	public function save($data, $id=0)
	{
		$model = parent::save($data, $id);
		if (isset($data['checkitems'])) {
			$model->checkitems()->sync($data['checkitems']);
		}

		return $model;
	}

}