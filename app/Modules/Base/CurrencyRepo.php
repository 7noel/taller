<?php 

namespace App\Modules\Base;

use App\Modules\Base\BaseRepo;
use App\Modules\Base\Currency;

class CurrencyRepo extends BaseRepo{

	public function getModel(){
		return new Currency;
	}
	public function getList2($name='symbol', $id='id')
	{
		return [""=>"SELECCIONAR"] + Currency::lists($name, $id);
	}
}