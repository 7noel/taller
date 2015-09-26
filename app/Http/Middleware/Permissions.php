<?php namespace App\Http\Middleware;

use Closure;
use App\Modules\Security\UserRepo;

class Permissions {

	protected $repo;

    public function __construct(UserRepo $repo) {
        $this->repo = $repo;
    }
	/**
	 * Handle an incoming request.
	 *
	 * @param  \Illuminate\Http\Request  $request
	 * @param  \Closure  $next
	 * @return mixed
	 */
	public function handle($request, Closure $next)
	{
		$route = $this->getRoute($request);

		if ( $this->repo->is_authorized($route) ) {
			return $next($request);
		}
		return redirect()->to('home');
		
	}

	/**
	 * Obtiene la ruta solicitada
	 * @return string devuelve el nombre de la ruta a la que se intento acceder
	 */
	public function getRoute($request)
	{
		$actPar = $request->route()->getAction();
		//$action: Contiene el nombre de la ruta solicitada
		$action = $actPar['as'];

		if (substr($action, -6) == '.store') { $action = str_replace('.store',	'.create', $action); }
		if (substr($action, -7) == '.update') { $action = str_replace('.update','.edit', $action); }

		return $action;
	}

}
