<?php namespace App\Modules\Sales;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class FeatureGroup extends Model {

	use SoftDeletes;

	protected $fillable = ['name', 'template'];

	public function scopeName($query, $name){
		if (trim($name) != "") {
			$query->where('name', 'LIKE', "%$name%");
		}
	}

	public function features()
	{
		return $this->hasMany('App\Modules\Sales\Feature');
	}

}
