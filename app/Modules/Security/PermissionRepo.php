<?php 

namespace App\Modules\Security;

use App\Modules\Base\BaseRepo;
use App\Modules\Security\Permission;

class PermissionRepo extends BaseRepo{

	public function getModel(){
		return new Permission;
	}

	public function index($filter = false, $search = false)
	{
		if ($filter and $search) {
			return Permission::$filter($search)->with('permission_group')->orderBy("$filter", 'ASC')->paginate();
		} else {
			return Permission::with('permission_group')->orderBy('id', 'DESC')->paginate();
		}
	}

	public function save($data, $id=0)
	{
		$data = $this->prepareData($data);
		if ($id>0) {
			$model = Permission::findOrFail($id);
			$model->fill($data);
			$model->save();
			return $model;
		} else {

			if (isset($data['generate']) and ($data['generate'] == true) ) {
				$name = $data['name'];
				$action = $data['action'];

				$data['name'] = $name." Listar";
				$data['action'] = strtolower($action).".index";
				$model = new Permission($data);
				$model->save();

				$data['name'] = $name." Ver";
				$data['action'] = strtolower($action).".show";
				$model = new Permission($data);
				$model->save();

				$data['name'] = $name." Crear";
				$data['action'] = strtolower($action).".create";
				$model = new Permission($data);
				$model->save();

				$data['name'] = $name." Editar";
				$data['action'] = strtolower($action).".edit";
				$model = new Permission($data);
				$model->save();

				$data['name'] = $name." Eliminar";
				$data['action'] = strtolower($action).".destroy";
				$model = new Permission($data);
				$model->save();
				return true;
			} else{
				$model = new Permission($data);
				$model->save();
				return $model;
			}

			//$model = Permission::fill($data);
		}
		if ($model->save()) {
			return $model;
		} else {
			return false;
		}
	}
	
}