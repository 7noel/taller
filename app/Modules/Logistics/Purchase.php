<?php namespace App\\Modules\Logistics;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Purchase extends Model {

	use SoftDeletes;

	//protected $fillable = ['name', 'symbol'];

	/*public function scopeName($query, $name){
		if (trim($name) != "") {
			$query->where('name', 'LIKE', "%$name%");
		}
	}*/

}
