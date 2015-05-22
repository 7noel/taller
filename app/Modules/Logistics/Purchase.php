<?php namespace App\Modules\Logistics;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Purchase extends Model {

	use SoftDeletes;

	protected $fillable = ['date', 'document_type_id', 'series', 'number', 'dispatch_note_date', 'dispatch_note_series', 'dispatch_note_number', 'company_id', 'payment_condition_id', 'due_date', 'currency_id', 'subtotal', 'igv', 'total'];

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
	public function purchase_details()
	{
		return $this->hasMany('App\Modules\Logistics\PurchaseDetail');
	}
	public function scopeDate($query, $name){
		if (trim($name) != "") {
			$query->where('date', 'LIKE', "%$name%");
		}
	}

}
