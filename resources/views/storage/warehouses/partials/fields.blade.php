					<div class="form-group  form-group-sm">
						{!! Form::label('name','Nombres', ['class'=>'col-sm-2 control-label']) !!}
						<div class="col-sm-10">
						{!! Form::text('name', null, ['class'=>'form-control uppercase']) !!}
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
						{!! Form::text('address', null, ['class'=>'form-control uppercase']) !!}
						</div>
					</div>
