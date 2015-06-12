<?php 

namespace App\Modules\Storage;

use App\Modules\Base\BaseRepo;
use App\Modules\Storage\SubCategory;

class SubCategoryRepo extends BaseRepo{

	public function getModel(){
		return new SubCategory;
	}
	public function index($filter = false, $search = false)
	{
		if ($filter and $search) {
			return SubCategory::$filter($search)->with('category')->orderBy("$filter", 'ASC')->paginate();
		} else {
			return SubCategory::orderBy('id', 'DESC')->with('category')->paginate();
		}
	}
	public function getList2($category_id)
	{
		return [''=>'SELECCIONAR'] + SubCategory::where('category_id',$category_id)->lists('name', 'id')->toArray();
	}
	public function ajaxList($category_id)
	{
		$ajax = SubCategory::select('id','name')->where('category_id','=',$category_id)->get();
		return $ajax;
	}
}