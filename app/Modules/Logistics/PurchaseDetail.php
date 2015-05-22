<?php namespace App\Modules\Logistics;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PurchaseDetail extends Model {

	use SoftDeletes;

	protected $fillable = ['purchase_id', 'product_id', 'unit_id', 'quantity', 'discount', 'price', 'total'];

	public function purchase()
	{
		return $this->belongsto('App\Modules\Logistics\Purchase');
	}
	public function stock()
	{
		return $this->belongsto('App\Modules\Storage\Stock');
	}
}
