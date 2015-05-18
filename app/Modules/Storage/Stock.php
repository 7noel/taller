<?php namespace App\Modules\Storage;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Stock extends Model {

	use SoftDeletes;

	protected $fillable = ['warehouse_id', 'product_id', 'stock'];

	/*public function scopeName($query, $name){
		if (trim($name) != "") {
			$query->where('name', 'LIKE', "%$name%");
		}
	}*/
	public function warehouse()
	{
		return $this->belongsTo('App\Modules\Storage\WareHouse');
	}
	public function product()
	{
		return $this->belongsTo('App\Modules\Storage\Product');
	}
}
