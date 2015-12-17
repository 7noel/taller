<?php namespace App\Modules\Sales;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CarQuote extends Model {

	use SoftDeletes;

	protected $fillable = ['catalog_car_id', 'company_id', 'attention', 'price', 'set_price', 'currency_id', 'observations', 'employee_id'];

	public function scopeAttention($query, $name){
		if (trim($name) != "") {
			$query->where('attention', 'LIKE', "%$name%");
		}
	}

	public function catalog_car()
	{
		return $this->belongsto('App\Modules\Sales\CatalogCar');
	}
	
	public function currency()
	{
		return $this->hasOne('App\Modules\Base\Currency','id','currency_id');
	}
	public function company()
	{
		return $this->hasOne('App\Modules\Finances\Company','id','company_id');
	}
	public function employee()
	{
		return $this->hasOne('App\Modules\HumanResources\Employee', 'id', 'employee_id');
	}

}
