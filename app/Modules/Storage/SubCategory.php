<?php namespace App\Modules\Storage;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class SubCategory extends Model {

	use SoftDeletes;

	protected $fillable = ['name', 'description', 'category_id'];

	public function scopeName($query, $name){
		if (trim($name) != "") {
			$query->where('name', 'LIKE', "%$name%");
		}
	}
	public function category()
	{
		return $this->belongsTo('App\Modules\Storage\Category');
	}
	public function products()
	{
		return $this->hasMany('App\Modules\Storage\Product');
	}
}
