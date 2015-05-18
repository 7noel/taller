<?php namespace App\Modules\Finances;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Company extends Model {

	use SoftDeletes;

	protected $fillable = [
		'company_name',
		'brand_name',
		'name',
		'paternal_surname',
		'maternal_surname',
		'id_type_id',
		'doc',
		'address',
		'ubigeo_id',
		'phone',
		'mobile',
		'email',
		'is_provider'
	];

	public function id_type()
    {
        return $this->hasOne('App\Modules\Base\IdType');
    }
    public function ubigeo()
	{
		return $this->hasOne('App\Modules\Base\Ubigeo');
	}

	public function scopeName($query, $name){
		if (trim($name) != "") {
			$query->where('company_name', 'LIKE', "%$name%");
		}
	}
}
