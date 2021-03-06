					<div class="form-group  form-group-sm">
						{!! Form::label('fecha','Fecha', ['class'=>'col-sm-2 control-label']) !!}
						<div class="col-sm-2">
							<input type="date" name="fecha" class="form-control _date" id="date-cita" value= <?php echo (isset($model)) ? $model->fecha : null ; ?> >
						</div>
						{!! Form::label('hora', 'Hora', ['class'=>'col-sm-1 control-label']) !!}
						<div class="col-sm-2">
						{!! Form::select('hora', $times, null, ['class'=>'form-control', 'id'=>'time-cita']) !!}
						</div>
						{!! Form::label('orderasesor', '#Asesor', ['class'=>'col-sm-1 control-label']) !!}
						<div class="col-sm-2">
						{!! Form::select('orderasesor', $orderasesores, null, ['class'=>'form-control', 'id'=>'orderasesor']) !!}
						</div>
					</div>
					<div class="form-group  form-group-sm">
						{!! Form::label('efectividad', 'efectividad', ['class'=>'col-sm-2 control-label']) !!}
						<div class="col-sm-2">
						{!! Form::select('efectividad', $efectividad, null, ['class'=>'form-control']) !!}
						</div>
						{!! Form::label('nomasesor', 'asesor', ['class'=>'col-sm-1 control-label']) !!}
						<div class="col-sm-3">
						{!! Form::text('nomasesor', null, ['class'=>'form-control', 'readonly'=>'readonly']) !!}
						</div>
					</div>
					<div class="form-group  form-group-sm">
						{!! Form::label('placa', 'Placa', ['class'=>'col-sm-2 control-label']) !!}
						<div class="col-sm-2">
						{!! Form::text('placa', isset($model) ? null : $placa, ['class'=>'form-control']) !!}
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
					{!! Form::hidden('asesor', null, ['id'=>'asesor']) !!}
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