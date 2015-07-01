<?php namespace App\Modules\Sales;

use App\Modules\Base\BaseRepo;
use App\Modules\Sales\CarQuote;

class CarQuoteRepo extends BaseRepo{

	public function getModel(){
		return new CarQuote;
	}
	public function prepareData($data)
	{
		
		return $data;
	}
}