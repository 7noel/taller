					<div class="form-group  form-group-sm">
						{!! Form::label('name','Documento', ['class'=>'col-sm-2 control-label']) !!}
						<div class="col-sm-10">
						{!! Form::text('name', null, ['class'=>'form-control']) !!}
						</div>
					</div>
					<div class="form-group  form-group-sm">
						{!! Form::label('description','Descripcion', ['class'=>'col-sm-2 control-label']) !!}
						<div class="col-sm-10">
						{!! Form::text('description', null, ['class'=>'form-control']) !!}
						</div>
					</div>
					<div class="form-group  form-group-sm">
						{!! Form::label('is_car','Opciones:', ['class'=>'col-sm-2 control-label']) !!}
						<div class="col-sm-10">
							<label class="checkbox-inline">
								{!! Form::checkbox('is_car', '1') !!} Vehículo
							</label>
						</div>
					</div>