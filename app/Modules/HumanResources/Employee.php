<?php namespace App\Modules\HumanResources;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Employee extends Model {

	use SoftDeletes;

	protected $fillable = [
		'name',
		'paternal_surname',
		'maternal_surname',
		'full_name',
		'id_type_id',
		'doc',
		'gender',
		'address',
		'ubigeo_id',
		'phone',
		'mobile',
		'email_personal',
		'email_company'
	];

	public function id_type()
	{
		return $this->hasOne('App\Modules\Base\IdType');
	}
	public function ubigeo()
	{
		return $this->hasOne('App\Modules\Base\Ubigeo','id','ubigeo_id');
	}
	public function scopeName($query, $name){
		if (trim($name) != "") {
			$query->where('full_name', 'LIKE', "%$name%");
		}
	}
}
