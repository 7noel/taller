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

	public function efectividad()
	{
		return \DB::connection('masaki')->table('efectivi')->lists('Nombre', 'Nombre');
	}
	public function asesores()
	{
		return \DB::connection('masaki')->table('asesores')->lists('Nombre', 'Codigo');
	}

	public function tipoot()
	{
		return \DB::connection('masaki')->table('tipoord')->lists('Nombre', 'Nombre');
	}

}