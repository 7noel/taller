<?php namespace App\Modules\Sales;

use App\Modules\Base\BaseRepo;
use App\Modules\Sales\Color;

class ColorRepo extends BaseRepo{

	public function getModel(){
		return new Color;
	}
}