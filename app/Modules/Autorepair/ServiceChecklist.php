<?php

namespace App\Modules\Autorepair;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ServiceChecklist extends Model
{
	use SoftDeletes;

	protected $fillable = ['order_id', 'company_name', 'plate', 'date', 'observation', 'adviser_id', 'technician_id', 'status', 'created_by_id', 'updated_by_id'];

	public function checkitems()
	{
		return $this->belongsToMany('App\Modules\Autorepair\Checkitem', 'service_checklist_checkitem')->withPivot( 'status','value' )->withTimestamps();
	}

	public function scopePlate($query, $name){
		if (trim($name) != "") {
			$query->where('plate', 'LIKE', "%$name%")->orWhere('order_id', 'LIKE', "%$name%");
		}
	}
	public function adviser()
    {
        return $this->hasOne('App\Modules\HumanResources\Employee', 'id', 'adviser_id');
    }
    public function technician()
    {
        return $this->hasOne('App\Modules\HumanResources\Employee', 'id', 'technician_id');
    }
}
