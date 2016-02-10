					<?php $v = '';
					if (isset($model)) {
						if (isset($afluencias[0])) {
							$v = 'Vendedor: '.$afluencias[0]->vendedor;
						} else {
							$v = 'Vendedor: '.$model->Usuario1;
						}
					}
					 ?>
					<div class="form-group form-group-sm">
						{!! Form::label('DNI','Documento', ['class'=>'col-sm-2 control-label']) !!}
						<div class="col-sm-4">
							<div class="form-inline">
							{!! Form::select('DniExt',$id_types , null, ['class'=>'form-control col-sm-1', 'id'=>'listDoc', 'required']) !!}
							{!! Form::text('DNI', null, ['class'=>'form-control uppercase', 'required']) !!}
							</div>
						</div>
						{!! Form::label('vendedor',$v, ['class'=>'col-sm-4 control-label']) !!}
					</div>
					<div class="form-group form-group-sm div_ruc">
						{!! Form::label('NombreRaz','Razón Social', ['class'=>'col-sm-2 control-label']) !!}
						<div class="col-sm-4">
						{!! Form::text('NombreRaz', null, ['class'=>'form-control uppercase']) !!}
						</div>
					</div>
					<div class="form-group form-group-sm div_dni">
						{!! Form::label('ApellidoPat','Nombre Completo', ['class'=>'col-sm-2 control-label']) !!}
						<div class="col-sm-3">
							{!! Form::text('ApellidoPat', null, ['class'=>'form-control uppercase', 'placeholder'=>'Apellido Paterno']) !!}
						</div>
						<div class="col-sm-3">
							{!! Form::text('ApellidoMat', null, ['class'=>'form-control uppercase', 'placeholder'=>'Apellido Materno']) !!}
						</div>
						<div class="col-sm-3">
							{!! Form::text('Nombre', null, ['class'=>'form-control uppercase', 'placeholder'=>'Nombre']) !!}
						</div>
					</div>
					@if(1==0)
					<div class="form-group form-group-sm">
						{!! Form::label('ubigeo_id','Distrito', ['class'=>'col-sm-2 control-label']) !!}
						<div class="col-sm-3">
								{!! Form::select('Departam',$ubigeo['departamento'],$ubigeo['value']['departamento'],['class'=>'form-control','id'=>'lstDepartamento','required'=>'required']) !!}
						</div>
						<div class="col-sm-3">
								{!! Form::select('Provincia',$ubigeo['provincia'],$ubigeo['value']['provincia'],['class'=>'form-control','id'=>'lstProvincia','required'=>'required']) !!}
						</div>
						<div class="col-sm-3">
								{!! Form::select('Distrito',$ubigeo['distrito'],$ubigeo['value']['distrito'],['class'=>'form-control','id'=>'lstDistrito','required'=>'required']) !!}
						</div>
					</div>
					<div class="form-group form-group-sm">
						{!! Form::label('Direccion','Direccion', ['class'=>'col-sm-2 control-label']) !!}
						<div class="col-sm-8">
						{!! Form::text('Direccion', null, ['class'=>'form-control uppercase', 'required']) !!}
						</div>
					</div>
					@endif
					<div class="form-group form-group-sm">
						{!! Form::label('Telefonos','Telefono Fijo', ['class'=>'col-sm-2 control-label']) !!}
						<div class="col-sm-2">
						{!! Form::text('Telefonos', null, ['class'=>'form-control']) !!}
						</div>
						{!! Form::label('Celular','Celulares', ['class'=>'col-sm-1 control-label']) !!}
						<div class="col-sm-2">
						{!! Form::text('Celular', null, ['class'=>'form-control']) !!}
						</div>
						{!! Form::label('Email','Email', ['class'=>'col-sm-1 control-label']) !!}
						<div class="col-sm-3">
						{!! Form::text('Email', null, ['class'=>'form-control']) !!}
						</div>
					</div>

					{!! Form::hidden('id', '0') !!}
					@if(isset($afluencias[0]))
					<?php $afluencia =$afluencias[0]; ?>
					<div class="form-group form-group-sm">
						{!! Form::label('tipo','Tipo Afluencia:', ['class'=>'col-sm-2 control-label']) !!}
						<div class="col-sm-4 tipo-radio">
							<label class="radio-inline">
								{!! Form::radio('tipo', 'PRESENCIAL', $tipo['PRESENCIAL'], ['id'=>'tipo_1', 'required']) !!} Presencial
							</label>
							<label class="radio-inline">
								{!! Form::radio('tipo', 'TELEFONICA', $tipo['TELEFONICA'], ['id'=>'tipo_2']) !!} Telefónica
							</label>
							<label class="radio-inline">
								{!! Form::radio('tipo', 'VIRTUAL', $tipo['VIRTUAL'], ['id'=>'tipo_3']) !!} Virtual
							</label>
						</div>
						{!! Form::label('canal','Canal', ['class'=>'col-sm-1 control-label']) !!}
						<div class="col-sm-3">
							{!! Form::select('canal', $canals, $afluencia->canal, ['class'=>'form-control', 'id'=>'canal', 'required']) !!}
						</div>
					</div>
					<div class="form-group form-group-sm">
						{!! Form::label('formapago','Forma de Pago:', ['class'=>'col-sm-2 control-label']) !!}
						<div class="col-sm-4 formapago-radio">
							<label class="radio-inline">
								{!! Form::radio('formapago', 'CONTADO', $formapago['CONTADO'], ['id'=>'formapago_1', 'required']) !!} Contado
							</label>
							<label class="radio-inline">
								{!! Form::radio('formapago', 'CREDITO', $formapago['CREDITO'], ['id'=>'formapago_2']) !!} Credito
							</label>
							<label class="radio-inline">
								{!! Form::radio('formapago', 'LEASING', $formapago['LEASING'], ['id'=>'formapago_3']) !!} Leasing
							</label>
						</div>
						{!! Form::label('evento','Evento', ['class'=>'col-sm-1 control-label evento']) !!}
						{!! Form::label('otros','Otros', ['class'=>'col-sm-1 control-label otros']) !!}
						<div class="col-sm-3">
							{!! Form::text('evento', $afluencia->evento, ['class'=>'form-control evento', 'rows'=>3]) !!}
							{!! Form::text('otros', $afluencia->otros, ['class'=>'form-control otros', 'rows'=>3]) !!}
						</div>
					</div>
					<div class="form-group form-group-sm">
						{!! Form::label('version_id','Modelo/Version', ['class'=>'col-sm-2 control-label']) !!}
						<div class="col-sm-3">
							{!! Form::select('version_id',$versions, $afluencia->version_id,['class'=>'form-control', 'id'=>'lstVersions', 'required']) !!}
						</div>
						{!! Form::label('catalog_car_id','Fabricación/Modelo', ['class'=>'col-sm-2 control-label']) !!}
						<div class="col-sm-3">
							{!! Form::select('catalog_car_id', $years, $afluencia->catalog_car_id,['class'=>'form-control', 'id'=>'lstYears', 'required']); !!}
						</div>
					</div>
					<div class="form-group form-group-sm">
						{!! Form::label('status','Status:', ['class'=>'col-sm-2 control-label']) !!}
						<div class="col-sm-8 status-checked">
							<label class="checkbox-inline">
								{!! Form::hidden('registered_at2', $afluencia->registered_at, ['id'=>'registered_at2']) !!}
								{!! Form::checkbox('registered_at', '1', $afluencia->registered_at) !!} Registrado
							</label>
							<label class="checkbox-inline">
								{!! Form::hidden('quoted_at2', $afluencia->quoted_at, ['id'=>'quoted_at2']) !!}
								{!! Form::checkbox('quoted_at', $afluencia->quoted_at, $afluencia->quoted_at) !!} Cotizado
							</label>
							<label class="checkbox-inline">
								{!! Form::hidden('test_drive_at2', $afluencia->test_drive_at, ['id'=>'test_drive_at2']) !!}
								{!! Form::checkbox('test_drive_at', $afluencia->test_drive_at, $afluencia->test_drive_at) !!} Test Drive
							</label>
							<label class="checkbox-inline">
								{!! Form::hidden('separated_at2', $afluencia->separated_at, ['id'=>'separated_at2']) !!}
								{!! Form::checkbox('separated_at', $afluencia->separated_at, $afluencia->separated_at) !!} Separado
							</label>
							<label class="checkbox-inline">
								{!! Form::hidden('canceled_at2', $afluencia->canceled_at, ['id'=>'canceled_at2']) !!}
								{!! Form::checkbox('canceled_at', $afluencia->canceled_at, $afluencia->canceled_at) !!} Cancelado
							</label>
						</div>
					</div>
					<div class="form-group form-group-sm">
						{!! Form::label('observaciones','Observaciones:', ['class'=>'col-sm-2 control-label']) !!}
						<div class="col-sm-8">
							{!! Form::textarea('observaciones', $afluencia->observaciones, ['class'=>'form-control', 'rows'=>3]) !!}
						</div>
					</div>
					@else
					<div class="form-group form-group-sm">
						{!! Form::label('tipo','Tipo Afluencia:', ['class'=>'col-sm-2 control-label']) !!}
						<div class="col-sm-4">
							<label class="radio-inline">
								{!! Form::radio('tipo', 'PRESENCIAL',null, ['required']) !!} Presencial
							</label>
							<label class="radio-inline">
								{!! Form::radio('tipo', 'TELEFONICA') !!} Telefónica
							</label>
							<label class="radio-inline">
								{!! Form::radio('tipo', 'VIRTUAL') !!} Virtual
							</label>
						</div>
						{!! Form::label('canal','Canal', ['class'=>'col-sm-1 control-label']) !!}
						<div class="col-sm-3">
							{!! Form::select('canal',$canals, null,['class'=>'form-control', 'required']) !!}
						</div>
					</div>
					<div class="form-group form-group-sm">
						{!! Form::label('version_id','Modelo/Version', ['class'=>'col-sm-2 control-label']) !!}
						<div class="col-sm-3">
							{!! Form::select('version_id',$versions, null,['class'=>'form-control', 'id'=>'lstVersions', 'required']) !!}
						</div>
						{!! Form::label('catalog_car_id','Fabricación/Modelo', ['class'=>'col-sm-2 control-label']) !!}
						<div class="col-sm-3">
							{!! Form::select('catalog_car_id', [''=>'Seleccionar'], null,['class'=>'form-control', 'id'=>'lstYears', 'required']); !!}
						</div>
					</div>
					<div class="form-group form-group-sm">
						{!! Form::label('status','Status:', ['class'=>'col-sm-2 control-label']) !!}
						<div class="col-sm-8">
							<label class="checkbox-inline">
								{!! Form::checkbox('registered_at', 'on') !!} Registrado
							</label>
							<label class="checkbox-inline">
								{!! Form::checkbox('quoted_at', 'on') !!} Cotizado
							</label>
							<label class="checkbox-inline">
								{!! Form::checkbox('test_drive_at', 'on') !!} Test Drive
							</label>
							<label class="checkbox-inline">
								{!! Form::checkbox('separated_at', 'on') !!} Separado
							</label>
							<label class="checkbox-inline">
								{!! Form::checkbox('canceled_at', 'on') !!} Cancelado
							</label>
						</div>
					</div>
					<div class="form-group form-group-sm">
						{!! Form::label('observaciones','Observaciones:', ['class'=>'col-sm-2 control-label']) !!}
						<div class="col-sm-8">
							{!! Form::textarea('observaciones', null, ['class'=>'form-control', 'rows'=>3]) !!}
						</div>
					</div>
					@endif
					<div class="form-group form-group-sm">
						<div class= "col-sm-offset-2 col-sm-5">
							<button type="button" id="btn-new-afluencia" class="btn btn-warning btn-xs"><span class="glyphicon glyphicon-plus-sign" aria-hidden="true"></span> Nueva Afluencia</button>
							<button type="button" id="xbtn-historial-afluencia" class="btn btn-info btn-xs"  data-toggle="modal" data-target="#myModal"><span class="glyphicon glyphicon-list-alt" aria-hidden="true"></span> Historial</button>
						</div>
					</div>
					@if(isset($afluencias))
					<!-- Modal -->
					<div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
						<div class="modal-dialog modal-lg" role="document">
							<div class="modal-content">
								<div class="modal-header">
									<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
									<h4 class="modal-title" id="myModalLabel">Historial: {{ $model->NombreRaz }}</h4>
								</div>
								<div class="modal-body">
									<table class="table table-striped">
										<thead>
											<tr>
												<td>Modelo</td>
												<td>Version</td>
												<td>Fecha</td>
												<td>Vendedor</td>
												<td>Status</td>
												<td>Tipo</td>
												<td>Canal</td>
											</tr>
										</thead>
										<tbody>
											@foreach($afluencias as $afluencia)
											<tr>
												<td>{{ $afluencia->modelo }}</td>
												<td>{{ $afluencia->version }}</td>
												<td>{{ $afluencia->fecha }}</td>
												<td>{{ $afluencia->vendedor }}</td>
												<td>{{ $afluencia->status }}</td>
												<td>{{ $afluencia->tipo }}</td>
												<td>{{ $afluencia->canal }}</td>
											</tr>
											@endforeach
										</tbody>
									</table>
								</div>
								<div class="modal-footer">
									<button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
								</div>
							</div>
						</div>
					</div>
					@endif