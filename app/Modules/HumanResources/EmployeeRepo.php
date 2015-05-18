<?php 

namespace App\Modules\HumanResources;

use App\Modules\Base\BaseRepo;
use App\Modules\HumanResources\Employee;

class EmployeeRepo extends BaseRepo{

	public function getModel(){
		return new Employee;
	}
	public function autocomplete($term)
	{
		return Employee::where('full_name','like',"%$term%")->get();
	}
	public function prepareData($data)
	{
		$data['full_name'] = $data['paternal_surname'].' '.$data['maternal_surname'].' '.$data['name'];
		return $data;
	}
}