<?php namespace App\Modules\Sales;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Version extends Model {

	use SoftDeletes;

	protected $fillable = ['name', 'description', 'modelo_id'];

	public function scopeName($query, $name){
		if (trim($name) != "") {
			$query->where('name', 'LIKE', "%$name%");
		}
	}

	public function modelo()
	{
		return $this->belongsto('App\Modules\Sales\Modelo');
	}

	public function catalog_cars()
	{
		return $this->hasMany('App\Modules\Sales\CatalogCar');
	}
}
