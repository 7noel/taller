<?php namespace App\Modules\Storage;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Unit extends Model {

	use SoftDeletes;

	protected $fillable = ['name', 'symbol', 'unit_type_id', 'value', 'description'];

	public function scopeName($query, $name){
		if (trim($name) != "") {
			$query->where('name', 'LIKE', "%$name%");
		}
	}
	public function unit_type()
	{
		return $this->belongsTo('App\Modules\Base\UnitType');
	}

}
