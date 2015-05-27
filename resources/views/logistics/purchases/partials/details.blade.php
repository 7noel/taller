
<table class="table table-condensed">
	<tr>
		<th class="col-md-1">Almacen</th>
		<th class="col-md-1">Codigo</th>
		<th class="col-md-2">Producto</th>
		<th class="">Stock</th>
		<th class="col-md-1">Cantidad</th>
		<th class="col-md-1">Precio</th>
		<th class="col-md-1">Desc</th>
		<th class="col-md-1">total</th>
		<th></th>
	</tr>
	<tr id='row_data'>
		<td class="col-md-1">
			{!! Form::select('warehouse_id',$warehouses , null, ['class'=>'form-control input-sm', 'id'=>'listWarehouses']) !!}
		</td>
		<td class="col-md-1"><label id="code"></label></td>
		<td class="col-md-2"><input type="text" class="form-control input-sm" id="txtproduct"></td>
		<td class=""><label id="stock"></label> <label id="unit"></label></td>
		<td class="col-md-1"><input type="text" class="form-control input-sm" id="txtquantity"></td>
		<td class="col-md-1"><label id="price"></label></td>
		<td class="col-md-1"><input type="text" class="form-control input-sm" id="txtdiscount"></td>
		<td class="col-md-1"><label id="total"></label></td>
		<td>
			<a href="#" class="btn btn-primary btn-sm" id="btnAddProduct"><span class="glyphicon glyphicon-download-alt" aria-hidden="true"></span> Agregar</a>
			<a href="#" class="btn btn-success btn-sm" id="btnUpdateProduct"><span class="glyphicon glyphicon-floppy-disk" aria-hidden="true"></span> Actualizar</a>
			<a href="#" class="btn btn-warning btn-sm" id="btnCancelProduct"><span class="glyphicon glyphicon-remove" aria-hidden="true"></span> Cancelar</a>
		</td>
	</tr>
</table>
<input type="hidden" id="items" value="{{ $items }}">
<table class="table table-condensed" id="tblDetails">
	<tr>
		<th class="text-center">ALM</th>
		<th class="text-center">CODIGO</th>
		<th class="text-left">DESCRIPCION</th>
		<th class="text-right">CANTIDAD</th>
		<th class="text-right">P.UNIT</th>
		<th class="text-right">V.BRUTO</th>
		<th class="text-right">DESC(%)</th>
		<th class="text-right">V.VENTA</th>
		<th class="text-left"></th>
	</tr>
	@foreach($purchase_details as $detail)
	<tr>
		<td class="text-center">{{ $detail->warehouse_id }}</td>
		<td class="text-center">{{ $detail->product->intern_code }}</td>
		<td class="text-left">{{ $detail->product->name }}</td>
		<td class="text-right">{{ $detail->quantity.' '.$detail->product->unit->symbol }}</td>
		<td class="text-right">{{ $detail->price }}</td>
		<td class="text-right">{{ ($detail->price * $detail->quantity) }}</td>
		<td class="text-right">{{ $detail->discount }}</td>
		<td class="text-right">{{ $detail->total }}</td>
		<td>
			<a href="#" class="btn-edit-detail btn btn-primary btn-xs"><span class="glyphicon glyphicon-pencil" aria-hidden="true"></span> Editar</a>
			<a href="#" class="btn-delete-detail btn btn-danger btn-xs"><span class="glyphicon glyphicon-trash" aria-hidden="true"></span> Eliminar</a>
		</td>
	</tr>
	@endforeach
</table>