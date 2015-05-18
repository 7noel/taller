					<div class="form-group  form-group-sm">
						{!! Form::label('doc','Documento', ['class'=>'col-sm-2 control-label']) !!}
						<div class="col-sm-10">
							<div class="form-inline">
							{!! Form::select('id_type_id',$id_types , null, ['class'=>'form-control col-sm-1']) !!}
							{!! Form::text('doc', null, ['class'=>'form-control']) !!}
							</div>
						</div>
					</div>
					<div class="form-group  form-group-sm">
						{!! Form::label('company_name','Razón Social', ['class'=>'col-sm-2 control-label']) !!}
						<div class="col-sm-10">
						{!! Form::text('company_name', null, ['class'=>'form-control']) !!}
						</div>
					</div>
					<div class="form-group  form-group-sm">
						{!! Form::label('brand_name','Nombre Comercial', ['class'=>'col-sm-2 control-label']) !!}
						<div class="col-sm-10">
						{!! Form::text('brand_name', null, ['class'=>'form-control']) !!}
						</div>
					</div>
					<div class="form-group  form-group-sm">
						{!! Form::label('name','Nombre Completo', ['class'=>'col-sm-2 control-label']) !!}
						<div class="col-sm-10">
							<div class="form-inline">
							{!! Form::text('paternal_surname', null, ['class'=>'form-control', 'placeholder'=>'Apellido Paterno']) !!}
							{!! Form::text('maternal_surname', null, ['class'=>'form-control', 'placeholder'=>'Apellido Materno']) !!}
							{!! Form::text('name', null, ['class'=>'form-control', 'placeholder'=>'Nombre']) !!}
							</div>
						</div>
					</div>
					<div class="form-group  form-group-sm">
						{!! Form::label('ubigeo_id','Distrito', ['class'=>'col-sm-2 control-label']) !!}
						<div class="col-sm-10">
							<div class="form-inline">
							{!! Form::hidden('ubigeo_id', null, ['class'=>'form-control']) !!}
								{!! Form::select('departamento',$ubigeo['departamento'],$ubigeo['value']['departamento'],['class'=>'form-control','id'=>'lstDepartamento','required'=>'required']) !!}
								{!! Form::select('provincia',$ubigeo['provincia'],$ubigeo['value']['provincia'],['class'=>'form-control','id'=>'lstProvincia','required'=>'required']) !!}
								{!! Form::select('ubigeo_id',$ubigeo['distrito'],$ubigeo['value']['distrito'],['class'=>'form-control','id'=>'lstDistrito','required'=>'required']) !!}
							</div>
						</div>
					</div>
					<div class="form-group  form-group-sm">
						{!! Form::label('address','Direccion', ['class'=>'col-sm-2 control-label']) !!}
						<div class="col-sm-10">
						{!! Form::text('address', null, ['class'=>'form-control']) !!}
						</div>
					</div>
					<div class="form-group  form-group-sm">
						{!! Form::label('phone','Telefono Fijo', ['class'=>'col-sm-2 control-label']) !!}
						<div class="col-sm-10">
						{!! Form::text('phone', null, ['class'=>'form-control']) !!}
						</div>
					</div>
					<div class="form-group  form-group-sm">
						{!! Form::label('mobilephone','Celulares', ['class'=>'col-sm-2 control-label']) !!}
						<div class="col-sm-10">
						{!! Form::text('mobilephone', null, ['class'=>'form-control']) !!}
						</div>
					</div>
					<div class="form-group  form-group-sm">
						{!! Form::label('email','Email', ['class'=>'col-sm-2 control-label']) !!}
						<div class="col-sm-10">
						{!! Form::text('email', null, ['class'=>'form-control']) !!}
						</div>
					</div>
					<div class="form-group  form-group-sm">
						{!! Form::label('birth','Nacimiento', ['class'=>'col-sm-2 control-label']) !!}
						<div class="col-sm-10">
						{!! Form::text('birth', null, ['class'=>'form-control date']) !!}
						</div>
					</div>
					<div class="form-group  form-group-sm">
						{!! Form::label('is_provider','Proveedor', ['class'=>'col-sm-2 control-label']) !!}
						<label class="radio-inline">
							{!! Form::radio('is_provider', '1', null, ['id'=>'is_provider1']) !!} SI
						</label>
						<label class="radio-inline">
							{!! Form::radio('is_provider', '0', null, ['id'=>'is_provider2']) !!} NO
						</label>
					</div>
					