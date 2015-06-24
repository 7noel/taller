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
		$model = parent::save($data,$id);
		if (!isset($data['permissions'])) {
			$data['permissions'] = [];
		}
		$model->permissions()->sync($data['permissions']);
		
		return $model;
	}
}