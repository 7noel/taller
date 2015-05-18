<?php namespace App\Modules\Base;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Currency extends Model {

	use SoftDeletes;

	protected $fillable = ['name', 'symbol'];

	public function scopeName($query, $name){
		if (trim($name) != "") {
			$query->where('name', 'LIKE', "%$name%");
		}
	}
	public function exchanges()
	{
		return $this->hasMany('App\Modules\Finances\Exchange');
	}
}
