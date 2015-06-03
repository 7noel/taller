<?php namespace App\Modules\Sales;

use Illuminate\Database\Eloquent\Model as BaseModel;
use Illuminate\Database\Eloquent\SoftDeletes;

class Version extends BaseModel {

	use SoftDeletes;

	protected $fillable = ['name', 'description', 'model_id'];

	public function scopeName($query, $name){
		if (trim($name) != "") {
			$query->where('name', 'LIKE', "%$name%");
		}
	}

	public function model()
	{
		return $this->belongsto('App\Modules\Sales\Model');
	}

	public function catalog_cars()
	{
		return $this->hasMany('App\Modules\Sales\CatalogCar');
	}
}
