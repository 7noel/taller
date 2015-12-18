<?php namespace App\Http\Controllers\Admin;

use App\Http\Requests;
use App\Http\Controllers\Controller;

use App\Modules\Base\UbigeoRepo;

class UbigeosController extends Controller{

	protected $ubigeoRepo;

	public function __construct(UbigeoRepo $ubigeoRepo) {
		$this->ubigeoRepo = $ubigeoRepo;
	}
	public function ajaxProvincias($departamento)
	{
		$provincias = $this->ubigeoRepo->ajaxProvincias($departamento);
		return \Response::json($provincias);
	}
	public function ajaxDistritos($departamento,$provincia)
	{
		$distritos = $this->ubigeoRepo->ajaxDistritos($provincia);
		return \Response::json($distritos);
	}
	public function ajaxDistritos2($departamento,$provincia)
	{
		$distritos = $this->ubigeoRepo->ajaxDistritos2($provincia);
		return \Response::json($distritos);
	}
}