<?php namespace App\Modules\Sales;

use App\Modules\Base\BaseRepo;
use App\Modules\Sales\Color;

class ColorRepo extends BaseRepo{

	public function getModel(){
		return new Color;
	}
	public function prepareData($data)
	{
		if (!isset($data['in'])) {
			$data['in'] = false;
		}
		if (!isset($data['out'])) {
			$data['out'] = false;
		}
		return $data;
	}
	public function getListIn($name='name', $id='id')
	{
		return [""=>"Seleccionar"] + Color::where('in',true)->lists($name, $id)->toArray();
	}
	public function getListOut($name='name', $id='id')
	{
		return [""=>"Seleccionar"] + Color::where('out',true)->lists($name, $id)->toArray();
	}
}