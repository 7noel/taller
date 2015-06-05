<?php namespace App\Modules\Sales;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CarQuote extends Model {

	use SoftDeletes;

	protected $fillable = [''];

	public function scopeYear($query, $name){
		if (trim($name) != "") {
			$query->where('name', 'LIKE', "%$name%");
		}
	}

	public function catalog_car()
	{
		return $this->belongsto('App\Modules\Sales\CatalogCar');
	}

}
