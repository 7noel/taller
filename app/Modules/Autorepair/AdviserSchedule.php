<?php

namespace App\Modules\Autorepair;

use Illuminate\Database\Eloquent\Model;

class AdviserSchedule extends Model
{
    protected $fillable = ['date', 'a1', 'a2', 'a3', 'a4', 'a5'];

    public function scopeName($query, $name){
		if (trim($name) != "") {
			$query->where('date', 'LIKE', "%$name%");
		}
	}
}
