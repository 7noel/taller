<?php namespace App\Modules\Sales;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Feature extends Model {

	use SoftDeletes;

	protected $fillable = ['name', 'value', 'feature_group_id', 'catalog_car_id'];

	public function scopeName($query, $name){
		if (trim($name) != "") {
			$query->where('name', 'LIKE', "%$name%");
		}
	}

	public function feature_group()
	{
		return $this->belongsto('App\Modules\Sales\FeatureGroup');
	}

	public function catalog_car()
	{
		return $this->belongsto('App\Modules\Sales\CatalogCar');
	}

}
