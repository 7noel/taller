<?php namespace App\Modules\Sales;

use App\Modules\Base\BaseRepo;
use App\Modules\Sales\Afluencia;

class AfluenciaRepo extends BaseRepo{

	public function getModel(){
		return new Afluencia;
	}
}