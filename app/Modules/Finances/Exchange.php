<?php namespace App\Modules\Finances;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Exchange extends Model {

	use SoftDeletes;

	protected $fillable = ['date', 'currency_id', 'sales', 'purchase'];

	public function scopeDate($query, $name){
		if (trim($name) != "") {
			$query->where('date', 'LIKE', "%$name%");
		}
	}
	public function currency()
	{
		return $this->belongsto('App\Modules\Base\Currency');
	}

}
