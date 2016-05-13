<?php namespace App\Modules\AutoRepair;

use App\Modules\Base\BaseRepo;
use App\Modules\AutoRepair\AdviserSchedule;

class AdviserScheduleRepo extends BaseRepo{

	public function getModel(){
		return new AdviserSchedule;
	}
	
}