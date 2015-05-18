<?php 

namespace App\Modules\Storage;

use App\Modules\Base\BaseRepo;
use App\Modules\Storage\Product;
use App\Modules\Storage\StockRepo;

class ProductRepo extends BaseRepo{

	public function getModel(){
		return new Product;
	}
	public function index($filter = false, $search = false)
	{
		if ($filter and $search) {
			return Product::$filter($search)->with('unit', 'sub_category', 'sub_category.category', 'stocks')->orderBy("$filter", 'ASC')->paginate();
		} else {
			return Product::orderBy('id', 'DESC')->with('unit', 'sub_category', 'sub_category.category', 'stocks')->paginate();
		}
	}
	public function prepareData($data)
	{
		if (isset($data['stocks'])) {
			foreach ($data['stocks'] as $key => $value) {
				$data['stocks'][$key]['product_id'] = $data['id'];
			}
		}
		return $data;
	}
	public function save($data, $id=0)
	{
		$model = parent::save($data, $id);
		if (isset($data['stocks'])) {
			$stockRepo= new StockRepo;
			$stockRepo->savefull($data['stocks'], $data['id']);
		}
		return $model;
	}
}