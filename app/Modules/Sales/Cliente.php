<?php

namespace App\Modules\Sales;

use Illuminate\Database\Eloquent\Model;

class Cliente extends Model
{
	protected $connection = 'masaki';
	protected $table = 'clientes';
	protected $primaryKey = 'codcliente';
	public $timestamps = false;
	protected $fillable = ['NombreRaz', 'Nombre', 'ApellidoPat', 'ApellidoMat', 'RUC', 'DniExt', 'DNI', 'Letra', 'Departam', 'Provincia', 'Distrito', 'Telefonos', 'Celular', 'Email'];

	public function scopeName($query, $name){
		if (trim($name) != "") {
			$query->where('NombreRaz', 'LIKE', "%$name%")->orWhere('DNI', 'LIKE', "%$name%")->orWhere('RUC', 'LIKE', "%$name%");
		}
	}
}
