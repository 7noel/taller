<?php 

namespace App\Modules\Security;

use App\Modules\Base\BaseRepo;
use App\Modules\Security\User;

class UserRepo extends BaseRepo{

	public function getModel(){
		return new User;
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
			$model->roles()->sync($data['roles']);
			return $model;
		} else {
			return false;
		}
	}
	public function prepareData($data)
	{
		if (!isset($data['is_superuser'])) { $data['is_superuser'] = false; }
		if (!isset($data['roles'])) { $data['roles'] = []; }
		return $data;
	}
	public function autocomplete($term)
	{
		return User::where('name','like',"%$term%")->orWhere('email','like',"%$term%")->get();
	}
    public function allPermissions()
    {
    	return \DB::table('permissions')
    	->join('role_permissions', 'permissions.id', '=', 'role_permissions.permission_id')
    	->join('user_roles', 'role_permissions.role_id', '=', 'user_roles.role_id')
    	->where('user_roles.user_id',\Auth::user()->id)
    	->select('permissions.action')
    	->groupBy('permissions.action')
    	->lists('action');
    }
}

