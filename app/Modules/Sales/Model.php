<?php namespace App\Modules\Sales;

use Illuminate\Database\Eloquent\Model as BaseModel;
use Illuminate\Database\Eloquent\SoftDeletes;

class Model extends BaseModel {

	use SoftDeletes;

	protected $fillable = ['name', 'description', 'brand_id'];

	public function scopeName($query, $name){
		if (trim($name) != "") {
			$query->where('name', 'LIKE', "%$name%");
		}
	}
	
	public function brand()
	{
		return $this->belongsto('App\Modules\Logistics\Brand');
	}

	public function versions()
	{
		return $this->hasMany('App\Modules\Sales\Version');
	}

}
