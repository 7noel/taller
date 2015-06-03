<?php namespace App\Modules\Sales;

use App\Modules\Base\BaseRepo;
use App\Modules\Sales\CatalogCar;

class CatalogCarRepo extends BaseRepo{

	public function getModel(){
		return new CatalogCar;
	}
}