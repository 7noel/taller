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
		if (isset($data['checkitems'])) {
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
		}
		return $data;
	}
	public function index($filter = false, $search = false)
	{
		if (\Auth::user()->employee->job_id==4) {
			if ($filter and $search) {
				return $this->model->$filter($search)->orderBy("$filter", 'ASC')->paginate();
			} else {
				return $this->model->orderBy('id', 'DESC')->paginate();
			}
		} elseif (\Auth::user()->employee->job_id==6) {
			if ($filter and $search) {
				return $this->model->$filter($search)->orderBy("$filter", 'ASC')->paginate();
			} else {
				return $this->model->orderBy('id', 'DESC')->paginate();
			}
		} else {
			if ($filter and $search) {
				return $this->model->$filter($search)->orderBy("$filter", 'ASC')->paginate();
			} else {
				return $this->model->orderBy('id', 'DESC')->paginate();
			}
		}
	}
	public function findForPlate($plate='')
	{
		return ServiceChecklist::where('plate',$plate)->orderBy('id', 'DESC')->first();
	}
	public function checksWarning($id)
	{
		return ServiceChecklist::find($id)->checkitems()->wherePivot('status','warning')->get();
	}
	public function checksDanger($id)
	{
		return ServiceChecklist::find($id)->checkitems()->wherePivot('status','danger')->get();
	}

}