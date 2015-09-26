<?php namespace App\Modules\Security;

use Illuminate\Auth\Authenticatable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Auth\Passwords\CanResetPassword;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Contracts\Auth\CanResetPassword as CanResetPasswordContract;
use Illuminate\Database\Eloquent\SoftDeletes;

class User extends Model implements AuthenticatableContract, CanResetPasswordContract {

	use Authenticatable, CanResetPassword, SoftDeletes;

	/**
	 * The database table used by the model.
	 *
	 * @var string
	 */
	protected $table = 'users';

	/**
	 * The attributes that are mass assignable.
	 *
	 * @var array
	 */
	protected $fillable = ['name', 'email', 'password', 'remember_token', 'is_superuser'];

	/**
	 * The attributes excluded from the model's JSON form.
	 *
	 * @var array
	 */
	protected $hidden = ['password', 'remember_token'];

	public function setPasswordAttribute($value){
		if (!empty($value)) {
			if (\Hash::needsRehash($value)){
				$this->attributes['password'] = \Hash::make($value);
			}
		}
	}

	public function scopeName($query, $name){
		if (trim($name) != "") {
			$query->where('name', 'LIKE', "%$name%");
		}
	}
	public function roles()
	{
		return $this->belongsToMany('App\Modules\Security\Role', 'user_roles')->withTimestamps();
	}
	public function permissions()
	{
		return $this->hasManyThrough('App\Modules\Security\Permission', 'App\Modules\Security\Role', 'user_id', 'role_id');
	}
	public function employee()
	{
		return $this->belongsto('App\Modules\HumanResources\Employee', 'id', 'user_id');
	}
	public function action_allowed($action)
    {
    	$result = \DB::table('permissions')
    	->join('role_permissions', 'permissions.id', '=', 'role_permissions.permission_id')
    	->join('user_roles', 'role_permissions.role_id', '=', 'user_roles.role_id')
    	->where('user_roles.user_id',\Auth::user()->id)
    	->where('permissions.action',$action)
    	->first();
    	if (is_null($result)) {
    		return false;
    	}

    	return true;
    }
}
