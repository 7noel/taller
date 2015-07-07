<?php namespace App\Modules\AutoRepair;

use App\Modules\Base\BaseRepo;
use App\Modules\AutoRepair\ServiceChecklist;

class ServiceChecklistRepo extends BaseRepo{

	public function getModel(){
		return new ServiceChecklist;
	}

}