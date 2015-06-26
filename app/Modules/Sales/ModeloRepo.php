<?php namespace App\Modules\Sales;

use App\Modules\Base\BaseRepo;
use App\Modules\Sales\Modelo;

class ModeloRepo extends BaseRepo{

	public function getModel(){
		return new Modelo;
	}
	public function index($filter = false, $search = false)
	{
		if ($filter and $search) {
			return Modelo::$filter($search)->with('brand')->orderBy("$filter", 'ASC')->paginate();
		} else {
			return Modelo::with('brand')->orderBy('id', 'DESC')->paginate();
		}
	}
	public function save($data, $id=0)
	{
		$model = parent::save($data, $id);
		if (isset($data['image'])) {
			$this->saveFile('img', $data['image']);
		}

		return $model;
	}
	public function prepareData($data)
	{
		/*if (isset($data['image'])) {
			$data['image'] = $data['image']->getClientOriginalName();
		} else {
			unset($data['image']);
		}*/
		$data = $this->prepareDataImage($data, ['image']);
		
		return $data;
	}
}