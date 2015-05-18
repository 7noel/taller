<?php namespace App\Modules\Storage;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Product extends Model {

	use SoftDeletes;

	protected $fillable = ['intern_code', 'provider_code', 'manufacturer_code', 'name', 'description', 'sub_category_id', 'unit_id', 'purchase_currency', 'sale_currency', 'last_purchase', 'profit_mragin', 'price', 'set_price', 'use_set_price'];

	public function scopeName($query, $name){
		if (trim($name) != "") {
			$query->where('name', 'LIKE', "%$name%");
		}
	}
	public function sub_category()
	{
		return $this->belongsTo('App\Modules\Storage\SubCategory');
	}
	public function unit()
	{
		return $this->hasOne('App\Modules\Storage\Unit','id','unit_id');
	}
	public function stocks()
	{
		return $this->hasMany('App\Modules\Storage\Stock');
	}
}
