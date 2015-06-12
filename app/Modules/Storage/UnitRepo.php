<?php 

namespace App\Modules\Storage;

use App\Modules\Base\BaseRepo;
use App\Modules\Storage\Unit;

class UnitRepo extends BaseRepo{

	public function getModel(){
		return new Unit;
	}
	public function autocomplete($term)
	{
		return Unit::where('name','like',"%$term%")->get();
	}
	public function index($filter = false, $search = false)
	{
		if ($filter and $search) {
			return Unit::$filter($search)->with('unit_type')->orderBy("$filter", 'ASC')->paginate();
		} else {
			return Unit::orderBy('id', 'DESC')->with('unit_type')->paginate();
		}
	}
	public function getList2($unit_type)
	{
		return [''=>'SELECCIONAR'] + Unit::where('unit_type_id',$unit_type)->lists('name', 'id')->toArray();
	}
	public function ajaxList($unit_type_id)
	{
		$ajax = Unit::select('id','name')->where('unit_type_id','=',$unit_type_id)->get();
		return $ajax;
	}
}