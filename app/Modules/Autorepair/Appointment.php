<?php

namespace App\Modules\Autorepair;

use Illuminate\Database\Eloquent\Model;

class Appointment extends Model
{
	protected $connection = 'masaki';
	protected $table = 't_citas';
	protected $primaryKey = 'idcita';
	public $timestamps = false;
	protected $fillable = ['fecha', 'Ano', 'Mes', 'Dia', 'hora', 'CodCliente', 'ruc_clie','nom_clie', 'RUC', 'DniExt', 'DNI', 'Direccion', 'Urbanizacion', 'distrito', 'Provincia', 'Telefonos', 'Celular', 'Contacto1', 'Email', 'placa', 'Marca', 'Modelo', 'Version', 'tipo', 'Color', 'Serie', 'NoMotor', 'Anofab', 'NroChasis', 'AnoModelo', 'orderasesor', 'asesor', 'nomasesor', 'TipoOrden', 'Preventivode', 'observa1', 'observa2', 'movilidad', 'Distritomov', 'NroOrden', 'Carga', 'destino', 'efectividad', 'Recomenda1', 'Recomenda2'];

	public function scopeName($query, $name){
		if (trim($name) != "") {
			$query->where('name', 'LIKE', "%$name%")->where('reserva', '1');
		}
	}
}
