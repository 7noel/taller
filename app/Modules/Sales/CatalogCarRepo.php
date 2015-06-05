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
		//dd($data['features']);
		$model = parent::save($data, $id);
		if (isset($data['features'])) {
			$featureRepo= new FeatureRepo;
			$featureRepo->saveMany($data['features'], ['key'=>'catalog_car_id', 'value'=>$model->id]);
		}
		return $model;
	}
}