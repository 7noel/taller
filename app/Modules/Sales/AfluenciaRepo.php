<?php namespace App\Modules\Sales;

use App\Modules\Base\BaseRepo;
use App\Modules\Sales\Afluencia;
use App\Modules\Sales\CatalogCar;
use App\Modules\Sales\Version;

class AfluenciaRepo extends BaseRepo{

	public function getModel(){
		return new Afluencia;
	}
	public function lastAfluencia($cliente_id)
	{
		return Afluencia::where('cliente_id', $cliente_id)->orderBy("id", 'DESC')->first();
	}
	public function prepareData($data)
	{
		//dd($data);
		$car = CatalogCar::find($data['catalog_car_id']);
		$data['modelo'] = $car->version->modelo->name;
		$data['version'] = $car->version->name;
		$data['status'] = 'REGISTRADO';
		$data['fecha'] = date('Y-m-d');
		$data['vendedor'] = \Auth::user()->employee->full_name;
		$data['Fecha1'] = date('Y-m-d');
		$data['Hora1'] = date('H:i:s');
		$data['Usuario1'] = \Auth::user()->employee->full_name;

		if (isset($data['quoted_at'])) {
			if ($data['quoted_at'] == "on") {
				$data['quoted_at'] = date('Y-m-d H:i:s');
			}
			$data['status'] = 'COTIZADO';
		} else {
			$data['quoted_at'] = null;
		}
		if (isset($data['test_drive_at'])) {
			if ($data['test_drive_at'] == "on") {
				$data['test_drive_at'] = date('Y-m-d H:i:s');
			}
			$data['status'] = 'TEST DRIVE';
		} else {
			$data['test_drive_at'] = null;
		}
		if (isset($data['separated_at'])) {
			if ($data['separated_at'] == "on") {
				$data['separated_at'] = date('Y-m-d H:i:s');
			}
			$data['status'] = 'SEPARADO';
		} else {
			$data['separated_at'] = null;
		}
		if (isset($data['canceled_at'])) {
			if ($data['canceled_at'] == "on") {
				$data['canceled_at'] = date('Y-m-d H:i:s');
			}
			$data['status'] = 'CANCELADO';
		} else {
			$data['canceled_at'] = null;
		}
		return $data;
	}
}