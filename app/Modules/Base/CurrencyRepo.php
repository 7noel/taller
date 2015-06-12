<?php 

namespace App\Modules\Base;

use App\Modules\Base\BaseRepo;
use App\Modules\Base\Currency;

class CurrencyRepo extends BaseRepo{

	public function getModel(){
		return new Currency;
	}
	
}