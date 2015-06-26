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
		$model = parent::save($data, $id);
		if (isset($data['features'])) {
			$featureRepo= new FeatureRepo;
			$featureRepo->saveMany($data['features'], ['key'=>'catalog_car_id', 'value'=>$model->id]);
		}
		if (isset($data['image'])) {
			$this->saveFile('img', $data['image']);
		}
		if (isset($data['image1'])) {
			$this->saveFile('img', $data['image1']);
		}
		if (isset($data['image2'])) {
			$this->saveFile('img', $data['image2']);
		}
		if (isset($data['image3'])) {
			$this->saveFile('img', $data['image3']);
		}
		if (isset($data['image4'])) {
			$this->saveFile('img', $data['image4']);
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

	public function getListYears($version_id)
	{
		$cars = CatalogCar::where('version_id',$version_id)->get();
		$data[''] = 'Seleccionar';
		foreach ($cars as $key => $car) {
			$data[$car->id] = $car->manufacture_year.' / '.$car->model_year;
		}
		return $data;
	}

	public function prepareData($data)
	{
		/*if (isset($data['image'])) {
			$data['image'] = $data['image']->getClientOriginalName();
		} else {
			unset($data['image']);
		}*/
		$data = $this->prepareDataImage($data, ['image', 'image1', 'image2', 'image3', 'image4']);
		
		return $data;
	}
}