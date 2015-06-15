<?php namespace App\Modules\Sales;

use App\Modules\Base\BaseRepo;
use App\Modules\Sales\CatalogCar;
use App\Modules\Sales\FeatureRepo;

class CatalogCarRepo extends BaseRepo{

	public function getModel(){
		return new CatalogCar;
	}
	public function save($data, $id=0)
	{
		/*if ($id>0) {
			$nameOld = CatalogCar::find($id)->image;
		}*/
		$nameOld = ($id>0) ? CatalogCar::find($id)->image : '' ;
		$model = parent::save($data, $id);
		if (isset($data['features'])) {
			$featureRepo= new FeatureRepo;
			$featureRepo->saveMany($data['features'], ['key'=>'catalog_car_id', 'value'=>$model->id]);
		}
		if (isset($data['image'])) {
			$this->saveFile('img', $data['image'], $nameOld);
		}

		return $model;
	}

	public function getListVersions()
	{
		$cars = CatalogCar::select('version_id')->with('version','version.modelo')->get();
		$data[''] = 'Seleccionar';
		foreach ($cars as $key => $car) {
			$data[$car->version_id] = $car->version->modelo->name.' '.$car->version->name;
		}
		return $data;
	}

	public function ajaxList($version_id)
	{
		$ajax = CatalogCar::select('id','manufacture_year','model_year')->where('version_id','=',$version_id)->get();
		return $ajax;
	}

	public function prepareData($data)
	{
		if (isset($data['image'])) {
			$data['image'] = $data['image']->getClientOriginalName();
		} else {
			unset($data['image']);
		}
		
		return $data;
	}
}