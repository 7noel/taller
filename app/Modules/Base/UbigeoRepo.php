<?php 

namespace App\Modules\Base;

use App\Modules\Base\Ubigeo;

class UbigeoRepo extends BaseRepo{

	public function getModel(){
		return new Ubigeo;
	}
	public function listDepartamentos()
	{
		return Ubigeo::groupBy('departamento')->lists('departamento','departamento')->toArray();
	}
	public function listProvincias($departamento='LIMA')
	{
		$provincias = Ubigeo::where('departamento','=',$departamento)->groupBy('provincia')->lists('provincia','provincia')->toArray();
		return $provincias;
	}
	public function listDistritos($provincia='LIMA')
	{
		$distritos = Ubigeo::where('provincia','=',$provincia)->groupBy('distrito')->lists('distrito','id')->toArray();
		return $distritos;
	}
	public function listDistritos2($provincia='LIMA')
	{
		$distritos = Ubigeo::where('provincia','=',$provincia)->groupBy('distrito')->lists('distrito','distrito')->toArray();
		return $distritos;
	}
	public function listUbigeo($id=0)
	{
		$ubi=Ubigeo::find($id);
		if (is_null($ubi)) {
			$ubigeo['value']['departamento'] = 'LIMA';
			$ubigeo['value']['provincia'] = 'LIMA';
			$ubigeo['value']['distrito'] = '';
			$ubigeo['departamento'] = $this->listDepartamentos();
			$ubigeo['provincia'] = $this->listProvincias();
			$ubigeo['distrito'] = $this->listDistritos();
		} else {
			$ubigeo['value']['departamento'] = $ubi->departamento;
			$ubigeo['value']['provincia'] = $ubi->provincia;
			$ubigeo['value']['distrito'] = $ubi->id;
			$ubigeo['departamento'] = $this->listDepartamentos();
			$ubigeo['provincia'] = $this->listProvincias($ubi->departamento);
			$ubigeo['distrito'] = $this->listDistritos($ubi->provincia);
		}
		$ubigeo['departamento'] = ['' => 'Seleccionar'] + $ubigeo['departamento'];
		$ubigeo['provincia'] = ['' => 'Seleccionar'] + $ubigeo['provincia'];
		$ubigeo['distrito'] = ['' => 'Seleccionar'] + $ubigeo['distrito'];
		return $ubigeo;
	}
	public function listUbigeo2($departamento='LIMA',$provincia='LIMA',$distrito='')
	{
		$ubi=Ubigeo::where('departamento',$departamento)->where('provincia',$provincia)->where('distrito',$distrito)->first();
		if (is_null($ubi)) {
			$ubigeo['value']['departamento'] = $departamento;
			$ubigeo['value']['provincia'] = $provincia;
			$ubigeo['value']['distrito'] = '';
			$ubigeo['departamento'] = $this->listDepartamentos();
			$ubigeo['provincia'] = $this->listProvincias();
			$ubigeo['distrito'] = $this->listDistritos2();
		} else {
			$ubigeo['value']['departamento'] = $ubi->departamento;
			$ubigeo['value']['provincia'] = $ubi->provincia;
			$ubigeo['value']['distrito'] = $ubi->distrito;
			$ubigeo['departamento'] = $this->listDepartamentos();
			$ubigeo['provincia'] = $this->listProvincias($ubi->departamento);
			$ubigeo['distrito'] = $this->listDistritos2($ubi->provincia);
		}
		$ubigeo['departamento'] = ['' => 'Seleccionar'] + $ubigeo['departamento'];
		$ubigeo['provincia'] = ['' => 'Seleccionar'] + $ubigeo['provincia'];
		$ubigeo['distrito'] = ['' => 'Seleccionar'] + $ubigeo['distrito'];
		return $ubigeo;
	}
	public function ajaxProvincias($departamento)
	{
		$provincias = Ubigeo::select('provincia')->where('departamento','=',$departamento)->groupBy('provincia')->get();
		return $provincias;
	}
	public function ajaxDistritos($provincia)
	{
		$distritos = Ubigeo::select('id','distrito')->where('provincia','=',$provincia)->get();
		return $distritos;
	}
	public function ajaxDistritos2($provincia)
	{
		$distritos = Ubigeo::select('distrito')->where('provincia','=',$provincia)->get();
		return $distritos;
	}
}