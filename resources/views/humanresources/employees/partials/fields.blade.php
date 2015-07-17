					<div class="form-group  form-group-sm">
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
					<div class="form-group  form-group-sm">
						{!! Form::label('doc','Documento', ['class'=>'col-sm-2 control-label']) !!}
						<div class="col-sm-4">
							<div class="form-inline">
							{!! Form::select('id_type_id',$id_types , null, ['class'=>'form-control col-sm-1']) !!}
							{!! Form::text('doc', null, ['class'=>'form-control uppercase']) !!}
							</div>
						</div>
						{!! Form::label('job_id','Cargo', ['class'=>'col-sm-1 control-label']) !!}
						<div class="col-sm-3">
							<div class="form-inline">
							{!! Form::select('job_id',$jobs , null, ['class'=>'form-control col-sm-1']) !!}
							</div>
						</div>
					</div>
					<div class="form-group  form-group-sm">
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
					<div class="form-group  form-group-sm">
						{!! Form::label('address','Direccion', ['class'=>'col-sm-2 control-label']) !!}
						<div class="col-sm-8">
						{!! Form::text('address', null, ['class'=>'form-control uppercase']) !!}
						</div>
					</div>
					<div class="form-group  form-group-sm">
						{!! Form::label('phone_personal','Telefono Personal', ['class'=>'col-sm-2 control-label']) !!}
						<div class="col-sm-3">
						{!! Form::text('phone_personal', null, ['class'=>'form-control']) !!}
						</div>
						{!! Form::label('phone_company','Telefono Empresa', ['class'=>'col-sm-2 control-label']) !!}
						<div class="col-sm-3">
						{!! Form::text('phone_company', null, ['class'=>'form-control']) !!}
						</div>
					</div>
					<div class="form-group  form-group-sm">
						{!! Form::label('mobile_personal','Celulares Personal', ['class'=>'col-sm-2 control-label']) !!}
						<div class="col-sm-3">
						{!! Form::text('mobile_personal', null, ['class'=>'form-control']) !!}
						</div>
						{!! Form::label('mobile_company','Celulares Empresa', ['class'=>'col-sm-2 control-label']) !!}
						<div class="col-sm-3">
						{!! Form::text('mobile_company', null, ['class'=>'form-control']) !!}
						</div>
					</div>
					<div class="form-group  form-group-sm">
						{!! Form::label('email_personal','Email Personal', ['class'=>'col-sm-2 control-label']) !!}
						<div class="col-sm-3">
						{!! Form::text('email_personal', null, ['class'=>'form-control']) !!}
						</div>
						{!! Form::label('email_company','Email Empresa', ['class'=>'col-sm-2 control-label']) !!}
						<div class="col-sm-3">
						{!! Form::text('email_company', null, ['class'=>'form-control']) !!}
						</div>
					</div>
					<div class="form-group  form-group-sm">
						{!! Form::label('user','Usuario', ['class'=>'col-sm-2 control-label']) !!}
						<div class="col-sm-8">
							{!! Form::hidden('user_id', null, ['id'=>'user_id']) !!}
							{!! Form::text('user', ((isset($model->user_id)) ? $model->user->email.' '.$model->user->name : null), ['class'=>'form-control', 'id'=>'txtuser', 'required']) !!}
						</div>
					</div>
					<div class="form-group form-group-sm">
						{!! Form::label('signature','Firma', ['class'=>'col-sm-2 control-label']) !!}
						<div class="col-sm-6">
						{!! Form::file('signature', ['class'=>'form-control file_img_preview', 'accept'=>'image/*']) !!}
						</div>
						<div class="col-sm-2 checkbox">
								<label>
									{!! Form::checkbox('delete_signature', '1') !!} Eliminar
								</label>
						</div>
						<div class="col-sm-offset-1">
							@if(isset($model->signature) and $model->signature!='')
							<img class="img_preview" src="{{ '/storage/img/'.$model->signature }}" alt=""  width="350px">
							@else
							<img class="img_preview" src="" alt=""  width="350px">
							@endif
						</div>
					</div>