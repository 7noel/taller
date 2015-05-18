<?php 

namespace App\Modules\Base;

use App\Modules\Base\BaseRepo;
use App\Modules\Base\UnitType;

class UnitTypeRepo extends BaseRepo{

	public function getModel(){
		return new UnitType;
	}
	
}