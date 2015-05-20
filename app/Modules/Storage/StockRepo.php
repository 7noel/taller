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
			Stock::where('product_id', 16)->where('stock', 0)->whereIn('warehouse_id',$toDelete)->delete();
		}
		
		foreach ($data as $key => $value) {
			if (in_array($value['warehouse_id'], $toSave)) {
				$model= new Stock;
				$model->fill($value);
				$model->save();
			}
		}
	}
}