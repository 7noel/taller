<?php 

namespace App\Modules\Finances;

use App\Modules\Base\BaseRepo;
use App\Modules\Finances\Company;

class CompanyRepo extends BaseRepo{

	public function getModel(){
		return new Company;
	}
	public function autocomplete($term)
	{
		return Employee::where('name','like',"%$term%")->get();
	}
	
}