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
	public function index($filter = false, $search = false)
	{
			if ($filter and $search) {
				return $this->model->$filter($search)->orderBy("$filter", 'ASC')->paginate();
			} else {
				return $this->model->orderBy('id', 'DESC')->paginate();
			}
	}
}