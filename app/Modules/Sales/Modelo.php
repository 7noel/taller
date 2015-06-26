<?php namespace App\Modules\Sales;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Modelo extends Model {

	use SoftDeletes;

	protected $fillable = ['name', 'description', 'brand_id', 'image'];

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
