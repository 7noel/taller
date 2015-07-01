					<div class="form-group  form-group-sm">
						{!! Form::label('name','Nombre', ['class'=>'col-sm-2 control-label']) !!}
						<div class="col-sm-10">
						{!! Form::text('name', null, ['class'=>'form-control uppercase']) !!}
						</div>
					</div>
					<div class="form-group  form-group-sm">
						{!! Form::label('','Opciones:', ['class'=>'col-sm-2 control-label']) !!}
						<div class="col-sm-10">
							<label class="checkbox-inline">
								{!! Form::checkbox('in', '1') !!} interior
							</label>
							<label class="checkbox-inline">
								{!! Form::checkbox('out', '1') !!} exterior
							</label>
						</div>
					</div>