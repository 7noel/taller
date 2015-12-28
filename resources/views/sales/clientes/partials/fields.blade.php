					<div class="form-group form-group-sm">
						{!! Form::label('DNI','Documento', ['class'=>'col-sm-2 control-label']) !!}
						<div class="col-sm-10">
							<div class="form-inline">
							{!! Form::select('DniExt',$id_types , null, ['class'=>'form-control col-sm-1', 'id'=>'listDoc', 'required']) !!}
							{!! Form::text('DNI', null, ['class'=>'form-control uppercase', 'required']) !!}
							</div>
						</div>
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

					@if(isset($afluencia))
					{!! Form::hidden('id', $afluencia->id) !!}
					<div class="form-group form-group-sm">
						{!! Form::label('tipo','Tipo Afluencia:', ['class'=>'col-sm-2 control-label']) !!}
						<div class="col-sm-4">
							<label class="radio-inline">
								{!! Form::radio('tipo', 'PRESENCIAL', $afluencia->tipo, ['required']) !!} Presencial
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
							{!! Form::select('canal',$canals, $afluencia->canal,['class'=>'form-control', 'required']) !!}
						</div>
					</div>
					<div class="form-group form-group-sm">
						{!! Form::label('version_id','Modelo/Version', ['class'=>'col-sm-2 control-label']) !!}
						<div class="col-sm-3">
							{!! Form::select('version_id',$versions, $afluencia->version_id,['class'=>'form-control', 'id'=>'lstVersions', 'required']) !!}
						</div>
						{!! Form::label('catalog_car_id','Fabricación/Modelo', ['class'=>'col-sm-2 control-label']) !!}
						<div class="col-sm-3">
							{!! Form::select('catalog_car_id', [''=>'Seleccionar'], $afluencia->catalog_car_id,['class'=>'form-control', 'id'=>'lstYears', 'required']); !!}
						</div>
					</div>
					<div class="form-group form-group-sm">
						{!! Form::label('status','Status:', ['class'=>'col-sm-2 control-label']) !!}
						<div class="col-sm-8">
							<label class="checkbox-inline">
								{!! Form::checkbox('is_registered', '1', $afluencia->is_registered) !!} Registrado
							</label>
							<label class="checkbox-inline">
								{!! Form::checkbox('is_quoted', '1', $afluencia->is_quoted) !!} Cotizado
							</label>
							<label class="checkbox-inline">
								{!! Form::checkbox('is_test_drive', '1', $afluencia->is_test_drive) !!} Test Drive
							</label>
							<label class="checkbox-inline">
								{!! Form::checkbox('is_separated', '1', $afluencia->is_separated) !!} Separado
							</label>
							<label class="checkbox-inline">
								{!! Form::checkbox('is_canceled', '1', $afluencia->is_canceled) !!} Cancelado
							</label>
						</div>
					</div>
					@else
					{!! Form::hidden('id', '0') !!}
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
								{!! Form::checkbox('is_quoted', '1') !!} Cotizado
							</label>
							<label class="checkbox-inline">
								{!! Form::checkbox('is_test_drive', '1') !!} Test Drive
							</label>
							<label class="checkbox-inline">
								{!! Form::checkbox('is_separated', '1') !!} Separado
							</label>
							<label class="checkbox-inline">
								{!! Form::checkbox('is_canceled', '1') !!} Cancelado
							</label>
						</div>
					</div>
					@endif
					<div class="form-group form-group-sm">
						<div class= "col-sm-offset-2 col-sm-5">
							<button type="button" class="btn btn-warning btn-xs"><span class="glyphicon glyphicon-refresh" aria-hidden="true"></span> Ultima Afluencia</button> 
							<button type="button" class="btn btn-info btn-xs"><span class="glyphicon glyphicon-plus-sign" aria-hidden="true"></span> Nueva Afluencia</button>
						</div>
					</div>