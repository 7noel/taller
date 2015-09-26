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
		$data = $this->preparedata($data);
		if (isset($data['checkitems'])) {
			$model->checkitems()->sync($data['checkitems']);
		}

		return $model;
	}
	public function prepareData($data)
	{
		foreach ($data['checkitems'] as $key => $checkitem) {
			if (!isset($checkitem['status']) and !isset($checkitem['value']) ) {
				$data['checkitems'][$key] = null;
			}
		}
		if (!isset($data['checkitems'][45])) {
			$data['checkitems'][45] = null;
		}
		if (!isset($data['checkitems'][50])) {
			$data['checkitems'][50] = null;
		}
		if (!isset($data['checkitems'][51])) {
			$data['checkitems'][51] = null;
		}
		return $data;
	}

}