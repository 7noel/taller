<?php

namespace App\Modules\Sales;

use Illuminate\Database\Eloquent\Model;

class Afluencia extends Model
{
	protected $connection = 'masaki';
	protected $table = 'atencionventa';
	public $timestamps = false;
	protected $fillable = ['cliente_id', 'cliente', 'fecha', 'modelo', 'version', 'tipo', 'canal', 'version_id', 'catalog_car_id', 'observaciones', 'evento', 'otros', 'status', 'quoted_at', 'test_drive_at', 'separated_at', 'canceled_at', 'Fecha1', 'Hora1', 'Usuario1', 'vendedor_id', 'vendedor_cod', 'vendedor', 'formapago', 'dato1'];

	public function clientee()
	{
		return $this->belongsto('App\Modules\Sales\Cliente');
	}
}
