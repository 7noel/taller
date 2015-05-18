<?php 

namespace App\Modules\Finances;

use App\Modules\Base\BaseRepo;
use App\Modules\Finances\Exchange;

class ExchangeRepo extends BaseRepo{

	public function getModel(){
		return new Exchange;
	}
	public function index($filter = false, $search = false)
	{
		if ($filter and $search) {
			return Exchange::$filter($search)->with('currency')->orderBy("$filter", 'ASC')->paginate();
		} else {
			return Exchange::with('currency')->orderBy('id', 'DESC')->paginate();
		}
	}
	public function prepareData($data)
	{
		$data['date'] = \Carbon::createFromFormat('d/m/Y', $data['date'], 'America/Lima')->toDateString();
		return $data;
	}
}