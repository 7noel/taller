<?php 

namespace App\Modules\Storage;

use App\Modules\Base\BaseRepo;
use App\Modules\Storage\Warehouse;

class WarehouseRepo extends BaseRepo{

	public function getModel(){
		return new Warehouse;
	}
	public function index($filter = false, $search = false)
	{
		if ($filter and $search) {
			return Warehouse::$filter($search)->with('ubigeo')->orderBy("$filter", 'ASC')->paginate();
		} else {
			return Warehouse::with('ubigeo')->orderBy('id', 'DESC')->paginate();
		}
	}
	public function ajaxList()
	{
		$ajax = Warehouse::select('id','name')->get();
		return $ajax;
	}
	public function getList($name='name', $id='id')
	{
		return [""=>"Alm"] + $this->model->lists($name, $id)->toArray();
	}
}