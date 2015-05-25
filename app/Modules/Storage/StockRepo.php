<?php 

namespace App\Modules\Storage;

use App\Modules\Base\BaseRepo;
use App\Modules\Storage\Stock;

class StockRepo extends BaseRepo{

	public function getModel(){
		return new Stock;
	}
	public function savefull($data, $product_id)
	{
		$old = Stock::where('product_id',$product_id)->lists('warehouse_id');
		foreach ($data as $key => $value) {
			$new[] = intval($value['warehouse_id']);
		}
		if (!isset($old)) { $old=[]; }
		$toDelete = array_diff($old, $new);
		$toSave = array_diff($new, $old);
		if (!empty($toDelete)) {
			//Stock::where('product_id', 16)->whereIn('warehouse_id',$toDelete)->delete();
			Stock::where('product_id', $product_id)->where('stock', 0)->whereIn('warehouse_id',$toDelete)->delete();
		}
		foreach ($data as $key => $value) {
			if (in_array($value['warehouse_id'], $toSave)) {
				$model= new Stock;
				$model->fill($value);
				$model->save();
			}
		}
	}
	public function autocomplete($term, $warehouse_id)
	{
		return \DB::table('stocks')
			->join('products', 'stocks.product_id', '=', 'products.id')
			->join('units','products.unit_id','=','units.id')
			->where('stocks.warehouse_id','=',$warehouse_id)
			->where(function($q) use ($term){
				$q->where('products.name', 'like', "%$term%")
				->orWhere('products.intern_code', 'like', "%$term%")
				->orWhere('products.provider_code', 'like', "%$term%")
				->orWhere('products.manufacturer_code', 'like', "%$term%");
			})
			->select('products.*', 'stocks.product_id', 'stocks.stock', 'units.symbol as unit_symbol', 'units.value as unit_value','stocks.id as stock_id','stocks.warehouse_id')
			->get();
	}
	public function ajaxGetData($warehouse_id, $product_id)
	{
		return Stock::where('warehouse_id', $warehouse_id)->where('product_id',$product_id)->with('product','product.unit')->first();
	}
}