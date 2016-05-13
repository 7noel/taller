<?php namespace App\Modules\AutoRepair;

use App\Modules\Base\BaseRepo;
use App\Modules\AutoRepair\Scheduling;
use App\Modules\AutoRepair\Appointment;

class SchedulingRepo extends BaseRepo{

	public function getModel(){
		return new Scheduling;
	}
	public function getTime($date)
	{
		//$dayofweek = date('w', strtotime($date));
		$schedulings = Scheduling::all();
		$appointments = Appointment::select('hora','orderasesor')->where('fecha', $date)->where('reserva', 1)->get()->toArray();
		$data = "<option value=''>Seleccionar</option>";
		foreach ($schedulings as $key => $scheduling) {
			$scheduling = $this->matchp($scheduling, $appointments);
			if ($scheduling->a1==1 or $scheduling->a2==1 or $scheduling->a3==1 or $scheduling->a4==1 or $scheduling->a5==1) {
				$data .= "<option value=".$scheduling->time.">".$scheduling->time."</option>";
			}
		}
		return $data;
	}
	public function getAsesor($date, $time)
	{
		$sch = Scheduling::where('time', $time)->first();
		$appointments = Appointment::where('fecha', $date)->where('hora', $time)->get()->toArray();
		$sch = $this->matchp($sch, $appointments);

		$data="<option value=''>Seleccionar</option>";
		if($sch->a1) {
			$data.="<option value='1'>Asesor 1</option>";
		}
		if($sch->a2) {
			$data.="<option value='2'>Asesor 2</option>";
		}
		if($sch->a3) {
			$data.="<option value='3'>Asesor 3</option>";
		}
		if($sch->a4) {
			$data.="<option value='4'>Asesor 4</option>";
		}
		if($sch->a5) {
			$data.="<option value='5'>Asesor 5</option>";
		}
		return $data;
	}

	public function matchp($horario, $reservas)
	{
		foreach ($reservas as $key => $reserva) {
			if ($reserva['hora'] == $horario->time) {
				if ($reserva['orderasesor']==1) {
					$horario->a1 = 0;
				} elseif ($reserva['orderasesor']==2) {
					$horario->a2 = 0;
				} elseif ($reserva['orderasesor']==3) {
					$horario->a3 = 0;
				} elseif ($reserva['orderasesor']==4) {
					$horario->a4 = 0;
				} elseif ($reserva['orderasesor']==5) {
					$horario->a5 = 0;
				}
			}
		}
		return $horario;
	}
	
	public function getClienteByPlate($plate)
	{
		$data = \DB::connection('masaki')->table('vehiculo')->where('placa',$plate)->first();
		return response()->json($data);
	}
}