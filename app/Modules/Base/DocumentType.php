<?php namespace App\Modules\Base;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class DocumentType extends Model {

	use SoftDeletes;

	protected $fillable = ['name', 'description', 'to_sales', 'to_purchases'];

	public function scopeName($query, $name){
		if (trim($name) != "") {
			$query->where('name', 'LIKE', "%$name%");
		}
	}
}
