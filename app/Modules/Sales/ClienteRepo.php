<?php namespace App\Modules\Sales;

use App\Modules\Base\BaseRepo;
use App\Modules\Sales\Cliente;
use App\Modules\Sales\AfluenciaRepo;

class ClienteRepo extends BaseRepo{

	public function getModel(){
		return new Cliente;
	}
	public function index($filter = false, $search = false)
	{
		if ($filter and $search) {
			return Cliente::$filter($search)->orderBy("CodCliente", 'ASC')->paginate();
		} else {
			return Cliente::orderBy('CodCliente', 'DESC')->paginate();
		}
	}
	public function save($data, $id=0)
	{
		$model = parent::save($data, $id);
		$afluenciaRepo = new AfluenciaRepo;
		if ( isset($data['tipo']) and $data['canal'] != '' ) {
			$cliente = Cliente::findOrFail($model->CodCliente);
			$data['cliente_id'] = $cliente->CodCliente;
			$data['cliente'] = $cliente->NombreRaz;
			$afluenciaRepo->save($data, $data['id']);
		}
	}
}