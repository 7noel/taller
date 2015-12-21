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
						<div class="col-sm-10">
						{!! Form::text('Direccion', null, ['class'=>'form-control uppercase', 'required']) !!}
						</div>
					</div>
					<div class="form-group form-group-sm">
						{!! Form::label('Telefonos','Telefono Fijo', ['class'=>'col-sm-2 control-label']) !!}
						<div class="col-sm-3">
						{!! Form::text('Telefonos', null, ['class'=>'form-control']) !!}
						</div>
						{!! Form::label('Celular','Celulares', ['class'=>'col-sm-2 control-label']) !!}
						<div class="col-sm-3">
						{!! Form::text('Celular', null, ['class'=>'form-control']) !!}
						</div>
					</div>
					<div class="form-group form-group-sm">
						{!! Form::label('Email','Email', ['class'=>'col-sm-2 control-label']) !!}
						<div class="col-sm-3">
						{!! Form::text('Email', null, ['class'=>'form-control']) !!}
						</div>
					</div>