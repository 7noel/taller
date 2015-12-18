<?php namespace App\Modules\Sales;

use App\Modules\Base\BaseRepo;
use App\Modules\Sales\Cliente;

class ClienteRepo extends BaseRepo{

	public function getModel(){
		return new Cliente;
	}
	public function index($filter = false, $search = false)
	{
		if ($filter and $search) {
			return Cliente::$filter($search)->orderBy("codcliente", 'ASC')->paginate();
		} else {
			return Cliente::orderBy('codcliente', 'DESC')->paginate();
		}
	}
}