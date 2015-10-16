<?php namespace App\Http\Middleware;

use Closure;

class Permissions {

	/**
	 * Handle an incoming request.
	 *
	 * @param  \Illuminate\Http\Request  $request
	 * @param  \Closure  $next
	 * @return mixed
	 */
	public function handle($request, Closure $next)
	{
		if (\Auth::user()->is_superuser) {
			return $next($request);
		} else {
		$actPar = $request->route()->getAction();
		$action = $actPar['as'];
		if (substr($action, -6) == '.store') { $action = str_replace('.store',	'.create', $action); }
		if (substr($action, -7) == '.update') { $action = str_replace('.update','.edit', $action); }
		$roles = \Auth::user()->roles()->get();
		foreach ($roles as $key => $role) {
			$permission = $role->permissions()->where('action', $action)->first();
			if(isset($permission)){
				return $next($request);
			}
		}
		//return redirect()->to('home');
		return view('errors.access_denied');
		}
	}

}
