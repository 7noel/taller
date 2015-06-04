					<div class="form-group  form-group-sm">
						{!! Form::label('name','Nombre', ['class'=>'col-sm-2 control-label']) !!}
						<div class="col-sm-10">
						{!! Form::text('name', null, ['class'=>'form-control']) !!}
						</div>
					</div>
					<div class="form-group  form-group-sm">
						{!! Form::label('value','Valor', ['class'=>'col-sm-2 control-label']) !!}
						<div class="col-sm-10">
						{!! Form::text('value', null, ['class'=>'form-control']) !!}
						</div>
					</div>
					<div class="form-group  form-group-sm">
						{!! Form::label('feature_group_id','Grupo', ['class'=>'col-sm-2 control-label']) !!}
						<div class="col-sm-10">
							<div class="form-inline">
							{!! Form::select('feature_group_id',$groups,null,['class'=>'form-control']) !!}
							</div>
						</div>
					</div>