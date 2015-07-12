<?php

namespace App\Modules\Autorepair;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Checkitem extends Model
{
	use SoftDeletes;

	protected $fillable = ['name', 'checkitem_group_id', 'with_status', 'with_value', 'column_two', 'pre_value', 'post_value'];

	public function scopeName($query, $name){
		if (trim($name) != "") {
			$query->where('name', 'LIKE', "%$name%");
		}
	}
	public function checkitem_group()
	{
		return $this->belongsto('App\Modules\Autorepair\CheckitemGroup');
	}
	public function service_checklists()
	{
		return $this->belongsToMany('App\Modules\Autorepair\ServiceChecklist', 'service_checklist_checkitem')->withPivot( 'status','value' )->withTimestamps();
	}
}
