<?php namespace App\Modules\Sales;

use App\Modules\Base\BaseRepo;
use App\Modules\Sales\FeatureGroup;

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
}