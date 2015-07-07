<?php

namespace App\Modules\Autorepair;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ServiceChecklist extends Model
{
	use SoftDeletes;

	protected $fillable = ['name'];

	public function scopeName($query, $name){
		if (trim($name) != "") {
			$query->where('name', 'LIKE', "%$name%");
		}
	}
}
