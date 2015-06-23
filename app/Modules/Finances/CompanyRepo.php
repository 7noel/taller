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
		return Company::where('company_name','like',"%$term%")->orWhere('doc','like',"%$term%")->with('id_type')->get();
	}
	public function prepareData($data)
	{
		$data['company_name'] = trim($data['company_name']);
		$data['brand_name'] = trim($data['brand_name']);
		$data['paternal_surname'] = trim($data['paternal_surname']);
		$data['maternal_surname'] = trim($data['maternal_surname']);
		if($data['id_type_id'] != 1){
			$data['company_name'] = $data['paternal_surname'].' '.$data['maternal_surname'].' '.$data['name'];
		}
		if(!isset($data['is_provider'])){
			$data['is_provider'] = false;
		}
		return $data;
	}
	
}