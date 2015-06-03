<?php namespace App\Modules\Sales;

use App\Modules\Base\BaseRepo;
use App\Modules\Sales\Model;

class ModelRepo extends BaseRepo{

	public function getModel(){
		return new Model;
	}
	public function index($filter = false, $search = false)
	{
		if ($filter and $search) {
			return Model::$filter($search)->with('brand')->orderBy("$filter", 'ASC')->paginate();
		} else {
			return Model::with('brand')->orderBy('id', 'DESC')->paginate();
		}
	}
}