<?php

namespace App\Modules\Autorepair;

use Illuminate\Database\Eloquent\Model;

class Appointment extends Model
{
	protected $connection = 'masaki';
	protected $table = 't_citas';
	protected $primaryKey = 'idcita';
	public $timestamps = false;
	protected $orderBy = 'idcita';
	//protected $fillable = [''];

	public function scopeName($query, $name){
		if (trim($name) != "") {
			$query->where('name', 'LIKE', "%$name%");
		}
	}
}
