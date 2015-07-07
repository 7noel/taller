<?php

namespace App\Modules\Autorepair;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CheckitemGroup extends Model
{
	use SoftDeletes;

	protected $fillable = ['name', 'order', 'is_double_column'];

	public function scopeName($query, $name){
		if (trim($name) != "") {
			$query->where('name', 'LIKE', "%$name%");
		}
	}
	public function checkitems()
	{
		return $this->hasMany('App\Modules\Autorepair\Checkitem');
	}
}
