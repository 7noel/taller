<?php namespace App\Modules\Sales;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CatalogCar extends Model {

	use SoftDeletes;

	protected $fillable = ['manufacture_year', 'model_year', 'version_id', 'cylinder', 'transmission', 'seats', 'fuel'];

	public function scopeYear($query, $name){
		if (trim($name) != "") {
			$query->where('manufacture_year', 'LIKE', "%$name%")->orWhere('model_year', 'LIKE', "%$name%");
		}
	}

	public function version()
	{
		return $this->belongsto('App\Modules\Sales\Version');
	}

	public function features()
	{
		return $this->hasMany('App\Modules\Sales\Feature');
	}

	public function car_quotes()
	{
		return $this->hasMany('App\Modules\Sales\CarQuote');
	}
}