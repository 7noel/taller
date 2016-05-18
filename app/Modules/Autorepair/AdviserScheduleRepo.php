<?php namespace App\Modules\AutoRepair;

use App\Modules\Base\BaseRepo;
use App\Modules\AutoRepair\AdviserSchedule;

class AdviserScheduleRepo extends BaseRepo{

	public function getModel(){
		return new AdviserSchedule;
	}
	public function getAsesorByOrder($order,$date)
	{
		$asesor = AdviserSchedule::where('date','<=',$date)->orderBy('date','desc')->first();
		if ($order==1) {
			$codigo = $asesor->a1;
		} else if ($order==2){
			$codigo = $asesor->a2;
		} else if ($order==3){
			$codigo = $asesor->a3;
		} else if ($order==4){
			$codigo = $asesor->a4;
		} else if ($order==5){
			$codigo = $asesor->a5;
		}
		return \DB::connection('masaki')->table('asesores')->select('Codigo','Nombre')->where('Codigo',$codigo)->first();
	}
}