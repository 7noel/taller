<?php 

namespace App\Modules\Security;

use App\Modules\Base\BaseRepo;
use App\Modules\Security\Role;

class RoleRepo extends BaseRepo{

	public function getModel(){
		return new Role;
	}
	public function save($data, $id=0)
	{
		$data = $this->prepareData($data);
		if ($id>0) {
			$model = $this->model->findOrFail($id);
			$model->fill($data);
		} else {
			$model = $this->model->fill($data);			
		}
		if ($model->save()) {
			if (!isset($data['permissions'])) { $data['permissions'] = []; }
			$role->permissions()->sync($data['permissions']);
			return $model;
		} else {
			return false;
		}
	}
}