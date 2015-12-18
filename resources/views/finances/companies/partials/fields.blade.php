					<div class="form-group form-group-sm">
						{!! Form::label('doc','Documento', ['class'=>'col-sm-2 control-label']) !!}
						<div class="col-sm-10">
							<div class="form-inline">
							{!! Form::select('id_type_id',$id_types , null, ['class'=>'form-control col-sm-1', 'id'=>'listDoc', 'required']) !!}
							{!! Form::text('doc', null, ['class'=>'form-control uppercase', 'required']) !!}
							</div>
						</div>
					</div>
					<div class="form-group form-group-sm div_ruc">
						{!! Form::label('company_name','Razón Social', ['class'=>'col-sm-2 control-label']) !!}
						<div class="col-sm-4">
						{!! Form::text('company_name', null, ['class'=>'form-control uppercase']) !!}
						</div>
						{!! Form::label('brand_name','Nombre Comercial', ['class'=>'col-sm-2 control-label']) !!}
						<div class="col-sm-4">
						{!! Form::text('brand_name', null, ['class'=>'form-control uppercase']) !!}
						</div>
					</div>
					<div class="form-group form-group-sm div_dni">
						{!! Form::label('name','Nombre Completo', ['class'=>'col-sm-2 control-label']) !!}
						<div class="col-sm-3">
							{!! Form::text('paternal_surname', null, ['class'=>'form-control uppercase', 'placeholder'=>'Apellido Paterno']) !!}
						</div>
						<div class="col-sm-3">
							{!! Form::text('maternal_surname', null, ['class'=>'form-control uppercase', 'placeholder'=>'Apellido Materno']) !!}
						</div>
						<div class="col-sm-3">
							{!! Form::text('name', null, ['class'=>'form-control uppercase', 'placeholder'=>'Nombre']) !!}
						</div>
					</div>
					<div class="form-group form-group-sm">
						{!! Form::label('ubigeo_id','Distrito', ['class'=>'col-sm-2 control-label']) !!}
						<div class="col-sm-3">
								{!! Form::select('departamento',$ubigeo['departamento'],$ubigeo['value']['departamento'],['class'=>'form-control','id'=>'lstDepartamento','required'=>'required']) !!}
						</div>
						<div class="col-sm-3">
								{!! Form::select('provincia',$ubigeo['provincia'],$ubigeo['value']['provincia'],['class'=>'form-control','id'=>'lstProvincia','required'=>'required']) !!}
						</div>
						<div class="col-sm-3">
								{!! Form::select('ubigeo_id',$ubigeo['distrito'],$ubigeo['value']['distrito'],['class'=>'form-control','id'=>'lstDistrito','required'=>'required']) !!}
						</div>
					</div>
					<div class="form-group form-group-sm">
						{!! Form::label('address','Direccion', ['class'=>'col-sm-2 control-label']) !!}
						<div class="col-sm-10">
						{!! Form::text('address', null, ['class'=>'form-control uppercase', 'required']) !!}
						</div>
					</div>
					<div class="form-group form-group-sm">
						{!! Form::label('phone','Telefono Fijo', ['class'=>'col-sm-2 control-label']) !!}
						<div class="col-sm-3">
						{!! Form::text('phone', null, ['class'=>'form-control']) !!}
						</div>
						{!! Form::label('mobilephone','Celulares', ['class'=>'col-sm-2 control-label']) !!}
						<div class="col-sm-3">
						{!! Form::text('mobilephone', null, ['class'=>'form-control']) !!}
						</div>
					</div>
					<div class="form-group form-group-sm">
						{!! Form::label('email','Email', ['class'=>'col-sm-2 control-label']) !!}
						<div class="col-sm-3">
						{!! Form::text('email', null, ['class'=>'form-control']) !!}
						</div>
						{!! Form::label('birth','Nacimiento', ['class'=>'col-sm-2 control-label']) !!}
						<div class="col-sm-3">
						{!! Form::text('birth', null, ['class'=>'form-control date']) !!}
						</div>
					</div>
					<div class="form-group form-group-sm">
						{!! Form::label('is_provider','Opciones', ['class'=>'col-sm-2 control-label']) !!}
						<div class="col-sm-10">
							<div class="checkbox">
								<label>
									{!! Form::checkbox('is_provider', '1', null,['class'=>'checkbox']) !!} Proveedor
								</label>
							</div>
						</div>
					</div>