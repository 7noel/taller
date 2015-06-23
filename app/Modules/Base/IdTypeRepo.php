<?php 

namespace App\Modules\Base;

use App\Modules\Base\BaseRepo;
use App\Modules\Base\IdType;

class IdTypeRepo extends BaseRepo{

	public function getModel(){
		return new IdType;
	}
	public function getList2($name='name', $id='id')
	{
		return [""=>"Seleccionar"] + $this->model->where('id','!=',1)->lists($name, $id)->toArray();
	}
}