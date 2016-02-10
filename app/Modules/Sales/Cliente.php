<?php

namespace App\Modules\Sales;

use Illuminate\Database\Eloquent\Model;

class Cliente extends Model
{
	protected $connection = 'masaki';
	protected $table = 'clientes';
	protected $primaryKey = 'CodCliente';
	public $timestamps = false;
	//protected $fillable = ['NombreRaz', 'Nombre', 'ApellidoPat', 'ApellidoMat', 'RUC', 'DniExt', 'DNI', 'Letra', 'Departam', 'Provincia', 'Distrito', 'Direccion', 'Telefonos', 'Celular', 'Email', 'Rubroneg', 'Profesion','Fecha1','Hora1','Usuario1','Fecha2','Hora2','Usuario2', 'of_sales'];
	protected $fillable = ['NombreRaz', 'Nombre', 'ApellidoPat', 'ApellidoMat', 'RUC', 'DniExt', 'DNI', 'Letra', 'Telefonos', 'Celular', 'Email', 'Rubroneg', 'Profesion','Fecha1','Hora1','Usuario1','Fecha2','Hora2','Usuario2', 'of_sales'];

	public function scopeName($query, $name){
		if (trim($name) != "") {
			$query->where('NombreRaz', 'LIKE', "%$name%")->orWhere('DNI', 'LIKE', "%$name%")->orWhere('RUC', 'LIKE', "%$name%");
		}
	}
	public function afluencias()
	{
		return $this->hasMany('App\Modules\Sales\Afluencia', 'cliente_id', 'CodCliente');
	}
}
