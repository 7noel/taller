<?php namespace App\Modules\Finances;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PaymentCondition extends Model {

	use SoftDeletes;

	protected $fillable = ['name', 'description', 'to_sales', 'to_purchases'];

	public function scopeName($query, $name){
		if (trim($name) != "") {
			$query->where('name', 'LIKE', "%$name%");
		}
	}
}
