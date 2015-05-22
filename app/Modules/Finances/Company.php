<?php namespace App\Modules\Finances;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Company extends Model {

	use SoftDeletes;

	protected $fillable = ['company_name', 'brand_name', 'name', 'paternal_surname', 'maternal_surname', 'id_type_id', 'doc', 'address', 'ubigeo_id', 'phone', 'mobile', 'email', 'birth', 'is_provider'];

	public function id_type()
    {
        return $this->hasOne('App\Modules\Base\IdType', 'id', 'id_type_id');
    }
    public function ubigeo()
	{
		return $this->hasOne('App\Modules\Base\Ubigeo','id','ubigeo_id');
	}

	public function scopeName($query, $name){
		if (trim($name) != "") {
			$query->where('company_name', 'LIKE', "%$name%");
		}
	}
}
