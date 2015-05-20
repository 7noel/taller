<?php namespace App\Modules\Logistics;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Brand extends Model {

	use SoftDeletes;

	protected $fillable = ['name', 'description', 'is_car'];

	public function scopeName($query, $name){
		if (trim($name) != "") {
			$query->where('name', 'LIKE', "%$name%");
		}
	}
}
