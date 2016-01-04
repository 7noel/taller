<?php namespace App\Modules\AutoRepair;

use App\Modules\Base\BaseRepo;
use App\Modules\AutoRepair\Scheduling;

class SchedulingRepo extends BaseRepo{

	public function getModel(){
		return new Scheduling;
	}
	
}