<?php namespace App\Modules\Sales;

use App\Modules\Base\BaseRepo;
use App\Modules\Sales\FeatureGroup;
use App\Modules\Sales\Feature;

class FeatureGroupRepo extends BaseRepo{

	public function getModel(){
		return new FeatureGroup;
	}

	public function getListTemplates()
	{
		return [""=>"Seleccionar",
				"primaryLeft"=>"PRINCIPAL 1",
				"primaryRight"=>"PRINCIPAL 2",
				"in"=>"INTERIOR",
				"out"=>"EXTERIOR"
			];
	}

	public function byCatalogCar($catalog_car_id)
	{
		$ids = Feature::select('feature_group_id')->where('catalog_car_id', $catalog_car_id)->groupBy('feature_group_id')->lists('feature_group_id');
		return FeatureGroup::whereIn('id', $ids)->get();
	}
}