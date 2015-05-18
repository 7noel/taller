<?php namespace App\Modules\Storage;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Stock extends Model {

	use SoftDeletes;

	protected $fillable = ['warehouse_id', 'product_id', 'stock', 'avarege_value'];

	public function warehouse()
	{
		return $this->belongsTo('App\Modules\Storage\WareHouse');
	}
	public function product()
	{
		return $this->belongsTo('App\Modules\Storage\Product');
	}
}
