<?php

namespace App\Modules\Autorepair;

use Illuminate\Database\Eloquent\Model;

class Scheduling extends Model
{
	protected $fillable = ['is_saturday', 'time', 'a1', 'a2', 'a3', 'a4', 'a5'];

	public function scopeName($query, $name){
		if (trim($name) != "") {
			$query->where('name', 'LIKE', "%$name%");
		}
	}
}
