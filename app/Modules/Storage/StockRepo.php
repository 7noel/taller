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
		//dd($data);
		$old = Stock::where('product_id',$product_id)->lists('warehouse_id');
		//dd($old);
		foreach ($data as $key => $value) {
			$new[] = intval($value['warehouse_id']);
		}
		if (!isset($old)) { $old=[]; }
		$toDelete = array_diff($old, $new);
		//$toDelete = array_values($toDelete);
		$toSave = array_diff($new, $old);
		//$toSave = array_values($toSave);
		/*var_dump($old);
		echo "<br><br>";
		var_dump($new);
		echo "<br><br>";
		dd($toDelete);
		dd($toSave);
		dd($toDelete);*/
		if (!empty($toDelete)) {
			//dd($product_id);
			Stock::where('product_id', 16)->whereIn('warehouse_id',$toDelete)->delete();
		}
		$var = 0;
		foreach ($data as $key => $value) {
			if (in_array($value['warehouse_id'], $toSave)) {
				$model= new Stock;
				$model->fill($value);
				$model->save();
				$var++;
			}
		}
		//dd($var);
	}
}