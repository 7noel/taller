					<div class="form-group  form-group-sm">
						{!! Form::label('fecha','Fecha', ['class'=>'col-sm-2 control-label']) !!}
						<div class="col-sm-2">
							<input type="date" name="fecha" class="form-control" id="date-cita">
						</div>
						{!! Form::label('hora', 'Hora', ['class'=>'col-sm-1 control-label']) !!}
						<div class="col-sm-2">
						{!! Form::select('time', [''=>'Seleccionar'], null, ['class'=>'form-control', 'id'=>'time-cita']) !!}
						</div>
						{!! Form::label('orderasesor', '#Asesor', ['class'=>'col-sm-1 control-label']) !!}
						<div class="col-sm-2">
						{!! Form::select('orderasesor', [''=>'Seleccionar'], null, ['class'=>'form-control', 'id'=>'orderasesor']) !!}
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
					{!! Form::hidden('CodCliente', null, ['id'=>'CodCliente']) !!}
					{!! Form::hidden('ruc_clie', null, ['id'=>'ruc_clie']) !!}
					{!! Form::hidden('nom_clie', null, ['id'=>'nom_clie']) !!}
					{!! Form::hidden('RUC', null, ['id'=>'RUC']) !!}
					{!! Form::hidden('DniExt', null, ['id'=>'DniExt']) !!}
					{!! Form::hidden('DNI', null, ['id'=>'DNI']) !!}
					{!! Form::hidden('Direccion', null, ['id'=>'Direccion']) !!}
					{!! Form::hidden('Urbanizacion', null, ['id'=>'Urbanizacion']) !!}
					{!! Form::hidden('distrito', null, ['id'=>'distrito']) !!}
					{!! Form::hidden('Provincia', null, ['id'=>'Provincia']) !!}
					{!! Form::hidden('Telefonos', null, ['id'=>'Telefonos']) !!}
					{!! Form::hidden('Celular', null, ['id'=>'Celular']) !!}
					{!! Form::hidden('Contacto1', null, ['id'=>'Contacto1']) !!}
					{!! Form::hidden('Email', null, ['id'=>'Email']) !!}
					{!! Form::hidden('Marca', null, ['id'=>'Marca']) !!}
					{!! Form::hidden('Modelo', null, ['id'=>'Modelo']) !!}
					{!! Form::hidden('Version', null, ['id'=>'Version']) !!}
					{!! Form::hidden('tipo', null, ['id'=>'tipo']) !!}
					{!! Form::hidden('Color', null, ['id'=>'Color']) !!}
					{!! Form::hidden('Serie', null, ['id'=>'Serie']) !!}
					{!! Form::hidden('NoMotor', null, ['id'=>'NoMotor']) !!}
					{!! Form::hidden('Anofab', null, ['id'=>'Anofab']) !!}
					{!! Form::hidden('NroChasis', null, ['id'=>'NroChasis']) !!}
					{!! Form::hidden('AnoModelo', null, ['id'=>'AnoModelo']) !!}