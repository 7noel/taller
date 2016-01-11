					<div class="form-group  form-group-sm">
						{!! Form::label('fecha','Fecha', ['class'=>'col-sm-2 control-label']) !!}
						<div class="col-sm-2">
							<input type="date" class="form-control" id="date-cita">
						</div>
						{!! Form::label('hora', 'Hora', ['class'=>'col-sm-1 control-label']) !!}
						<div class="col-sm-2">
						{!! Form::select('time', [''=>'Seleccionar'], null, ['class'=>'form-control', 'id'=>'time-cita']) !!}
						</div>
						{!! Form::label('nasesor', '#Asesor', ['class'=>'col-sm-1 control-label']) !!}
						<div class="col-sm-2">
						{!! Form::select('nasesor', [''=>'Seleccionar'], null, ['class'=>'form-control', 'id'=>'nasesor']) !!}
						</div>
					</div>
					<div class="form-group  form-group-sm">
						{!! Form::label('efectividad', 'efectividad', ['class'=>'col-sm-2 control-label']) !!}
						<div class="col-sm-2">
						{!! Form::select('efectividad', $efectividad, null, ['class'=>'form-control']) !!}
						</div>
						{!! Form::label('asesor', 'asesor', ['class'=>'col-sm-1 control-label']) !!}
						<div class="col-sm-2">
						{!! Form::select('asesor', $asesores, null, ['class'=>'form-control']) !!}
						</div>
					</div>
					<div class="form-group  form-group-sm">
						{!! Form::label('placa', 'Placa', ['class'=>'col-sm-2 control-label']) !!}
						<div class="col-sm-2">
						{!! Form::text('placa', $placa, ['class'=>'form-control']) !!}
						</div>
						{!! Form::label('tipoot', 'tipoot', ['class'=>'col-sm-1 control-label']) !!}
						<div class="col-sm-2">
						{!! Form::select('tipoot', $tipoot, null, ['class'=>'form-control']) !!}
						</div>
						{!! Form::label('preventivode', 'preventivode', ['class'=>'col-sm-1 control-label']) !!}
						<div class="col-sm-2">
						{!! Form::text('preventivode', null, ['class'=>'form-control']) !!}
						</div>
					</div>
					<div class="form-group  form-group-sm">
						{!! Form::label('description','Descripcion', ['class'=>'col-sm-2 control-label']) !!}
						<div class="col-sm-10">
						{!! Form::text('description', null, ['class'=>'form-control']) !!}
						</div>
					</div>