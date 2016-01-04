<?php namespace App\Modules\AutoRepair;

use App\Modules\Base\BaseRepo;
use App\Modules\AutoRepair\Appointment;

class AppointmentRepo extends BaseRepo{

	public function getModel(){
		return new Appointment;
	}
	public function index($filter = false, $search = false)
	{
		if ($filter and $search) {
			return Appointment::$filter($search)->orderBy("idcita", 'ASC')->paginate();
		} else {
			return Appointment::orderBy('idcita', 'DESC')->paginate();
		}
	}
}