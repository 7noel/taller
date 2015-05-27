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
		$data['price'] = $data['last_purchase'] * (100 + $data['profit_margin']) / 100;
		if (!isset($data['use_set_price'])) {
			$data['use_set_price'] = false;
		}
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
			$stockRepo->syncMany($data['stocks'], ['key'=>'product_id', 'value'=>$model->id], 'warehouse_id');
		}
		return $model;
	}
	public function autocomplete($term, $warehouse_id)
	{
		//return Product::where('name','like',"%$term%")->orWhere('intern_code','like',"%$term%")->orWhere('provider_code','like',"%$term%")->orWhere('manufacturer_code','like',"%$term%")->with('stocks','currency')->get();
		$stockRepo = new StockRepo;
		return $stockRepo->autocomplete($term, $warehouse_id);
	}
	public function ajaxGetData($warehouse_id, $product_id)
	{
		$stockRepo = new StockRepo;
		return $stockRepo->ajaxGetData($warehouse_id, $product_id);
	}
}