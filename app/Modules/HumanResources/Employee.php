<?php namespace App\Modules\HumanResources;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Employee extends Model {

	use SoftDeletes;

	protected $fillable = ['name', 'paternal_surname', 'maternal_surname', 'full_name', 'id_type_id', 'doc', 'job_id', 'gender', 'address', 'ubigeo_id', 'phone_personal', 'mobile_personal', 'phone_company', 'mobile_company', 'email_personal', 'email_company', 'user_id', 'signature','other_id'];

	public function id_type()
	{
		return $this->hasOne('App\Modules\Base\IdType','id','id_type_id');
	}
	public function job()
	{
		return $this->hasOne('App\Modules\HumanResources\Job','id','job_id');
	}
	public function ubigeo()
	{
		return $this->hasOne('App\Modules\Base\Ubigeo','id','ubigeo_id');
	}
	public function user()
	{
		return $this->hasOne('App\Modules\Security\User','id','user_id');
	}
	public function scopeName($query, $name){
		if (trim($name) != "") {
			$query->where('full_name', 'LIKE', "%$name%");
		}
	}
}
