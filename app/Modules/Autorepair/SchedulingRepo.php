<?php namespace App\Modules\AutoRepair;

use App\Modules\Base\BaseRepo;
use App\Modules\AutoRepair\Scheduling;

class SchedulingRepo extends BaseRepo{

	public function getModel(){
		return new Scheduling;
	}
	public function getTime($date)
	{
		//return Scheduling::lists('time','time')->toArray();
		$schedulings = Scheduling::all();
		$data="<option value=''>Seleccionar</option>";
		foreach ($schedulings as $key => $scheduling) {
			$data .= "<option value=".$scheduling->time.">".$scheduling->time."</option>";
		}
		return $data;
	}
	public function getAsesor($date, $time)
	{
		$sch = Scheduling::where('time', $time)->first();
		$data="<option value=''>Seleccionar</option>";
		if($sch->a1) {
			$data.="<option value='1'>Asesor 1</option>";
		}
		if($sch->a2) {
			$data.="<option value='1'>Asesor 2</option>";
		}
		if($sch->a3) {
			$data.="<option value='1'>Asesor 3</option>";
		}
		if($sch->a4) {
			$data.="<option value='1'>Asesor 4</option>";
		}
		if($sch->a5) {
			$data.="<option value='1'>Asesor 5</option>";
		}
		return $data;
	}
	
}