<?php namespace App\Modules\Sales;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CatalogCar extends Model {

	use SoftDeletes;

	protected $fillable = ['manufacture_year', 'model_year', 'version_id'];

	public function scopeYear($query, $name){
		if (trim($name) != "") {
			$query->where('manufacture_year', 'LIKE', "%$name%")->orWhere('model_year', 'LIKE', "%$name%");
		}
	}

	public function version()
	{
		return $this->belongsto('App\Modules\Sales\Version');
	}
}