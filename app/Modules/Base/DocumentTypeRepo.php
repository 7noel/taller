<?php 

namespace App\Modules\Base;

use App\Modules\Base\BaseRepo;
use App\Modules\Base\DocumentType;

class DocumentTypeRepo extends BaseRepo{

	public function getModel(){
		return new DocumentType;
	}
	public function prepareData($data)
	{
		if (!isset($data['to_sales'])) {
			$data['to_sales'] = false;
		}
		if (!isset($data['to_purchases'])) {
			$data['to_purchases'] = false;
		}
		return $data;
	}
}