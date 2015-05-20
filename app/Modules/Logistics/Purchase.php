<?php namespace App\Modules\Logistics;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Purchase extends Model {

	use SoftDeletes;

	protected $guarded = ['id', 'created_at', 'updated_at', 'deleted_at'];

	public function document_type()
	{
		return $this->hasOne('App\Modules\Base\DocumentType','id','document_type_id');
	}
	public function payment_condition()
	{
		return $this->hasOne('App\Modules\Finances\PaymentCondition','id','payment_condition_id');
	}
	public function currency()
	{
		return $this->hasOne('App\Modules\Base\Currency','id','currency_id');
	}
	public function company()
	{
		return $this->hasOne('App\Modules\Finances\Company','id','company_id');
	}
	public function scopeDate($query, $name){
		if (trim($name) != "") {
			$query->where('date', 'LIKE', "%$name%");
		}
	}

}
