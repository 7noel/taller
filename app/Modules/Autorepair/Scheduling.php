<?php

namespace App\Modules\Autorepair;

use Illuminate\Database\Eloquent\Model;

class Scheduling extends Model
{
	protected $fillable = ['is_saturday', 'time', 'A1', 'A2', 'A3', 'A4', 'A5'];

	public function scopeName($query, $name){
		if (trim($name) != "") {
			$query->where('name', 'LIKE', "%$name%");
		}
	}
}
