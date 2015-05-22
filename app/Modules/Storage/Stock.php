<?php namespace App\Modules\Storage;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Stock extends Model {

	use SoftDeletes;

	protected $fillable = ['warehouse_id', 'product_id', 'stock', 'currency_id', 'avarege_value'];

	public function warehouse()
	{
		return $this->belongsTo('App\Modules\Storage\WareHouse');
	}
	public function product()
	{
		return $this->belongsTo('App\Modules\Storage\Product');
	}
	public function purchase_details()
	{
		return $this->hasMany('App\Modules\Logistics\PurchaseDetail');
	}
}
