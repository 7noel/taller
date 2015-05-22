<?php namespace App\Modules\Storage;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Warehouse extends Model {

	use SoftDeletes;

	protected $fillable = ['name', 'address', 'ubigeo_id'];

    public function ubigeo()
	{
		return $this->hasOne('App\Modules\Base\Ubigeo','id','ubigeo_id');
	}

	public function scopeName($query, $name){
		if (trim($name) != "") {
			$query->where('name', 'LIKE', "%$name%");
		}
	}
	public function stocks()
	{
		return $this->hasMany('App\Modules\Storage\Stock');
	}
}