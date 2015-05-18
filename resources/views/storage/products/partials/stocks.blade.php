					<div class="form-group  form-group-sm">
						<label class="control-label col-md-offset-1">Stock en Almacenes</label><button type="button" class="btn btn-default btn-sm col-sm-offset-1" data-toggle="modal" data-target="#ModalStock" id="btnNewStock">Nuevo</button>
					</div>
					<input type="hidden" value="{{ $model->stocks->count() }}" id="items">
					<table class="table table-hover table-condensed" id="tableStocks">
						<tr>
							<th>Almacen</th>
							<th>stock</th>
							<th>Acciones</th>
						</tr>
						@foreach($model->stocks as $key => $stock)
						<tr data-id="{{ $stock->id }}">
							<td>{{ $stock->warehouse_id }} </td>
							<td>{{ $stock->stock }} </td>
							<td>
								<a href="#" class="btn-delete-stock btn btn-danger btn-xs">Eliminar</a><input type="hidden" name="stocks[{{ $key }}][warehouse_id]" value="{{$stock->warehouse_id }}"><input type="hidden" name="stocks[{{ $key }}][stock]" value="{{ $stock->stock }}">
							</td>
						</tr>
						@endforeach
					</table>

					<div class="modal fade" id="ModalStock" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
						<div class="modal-dialog">
							<div class="modal-content">
								<div class="modal-header">
									<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
									<h4 class="modal-title" id="myModalLabel">Registrar Stock</h4>
								</div>
								<div class="modal-body">
									<div class="form-horizontal">
										<div class="form-group  form-group-sm">
											<label class="col-sm-2 control-label">Almacén</label>
											<div class="col-sm-2">
												<select class="col-sm-3 form-control" id="listWarehouses">
												</select>
											</div>
											<label class="col-sm-3 control-label">Stock inicial</label>
											<div class="col-sm-2">
												<input type="text" id="stockini" class="col-sm-3 form-control">
											</div>
										</div>
									</div>
								</div>
								<div class="modal-footer">
									<button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
									<button type="button" class="btn btn-primary"  data-dismiss="modal" id="btnSaveStock">Guardar</button>
								</div>
							</div>
						</div>
					</div>
