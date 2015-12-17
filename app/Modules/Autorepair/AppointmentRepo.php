<?php namespace App\Modules\AutoRepair;

use App\Modules\Base\BaseRepo;
use App\Modules\AutoRepair\Appointment;

class AppointmentRepo extends BaseRepo{

	public function getModel(){
		return new Appointment;
	}
}