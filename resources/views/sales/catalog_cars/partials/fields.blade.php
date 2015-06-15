					<div class="form-group  form-group-sm">
						{!! Form::label('manufacture_year','Año Fabricación', ['class'=>'col-sm-2 control-label']) !!}
						<div class="col-sm-10">
						{!! Form::text('manufacture_year', null, ['class'=>'form-control']) !!}
						</div>
					</div>
					<div class="form-group  form-group-sm">
						{!! Form::label('model_year','Año Modelo', ['class'=>'col-sm-2 control-label']) !!}
						<div class="col-sm-10">
						{!! Form::text('model_year', null, ['class'=>'form-control']) !!}
						</div>
					</div>
					<div class="form-group  form-group-sm">
						{!! Form::label('version_id','Modelo / Versión', ['class'=>'col-sm-2 control-label']) !!}
						<div class="col-sm-10">
							<div class="form-inline">
							{!! Form::select('version_id',$versions,null,['class'=>'form-control']) !!}
							</div>
						</div>
					</div>
					<div class="form-group  form-group-sm">
						{!! Form::label('cylinder','Cilindrada', ['class'=>'col-sm-2 control-label']) !!}
						<div class="col-sm-10">
						{!! Form::text('cylinder', null, ['class'=>'form-control']) !!}
						</div>
					</div>
					<div class="form-group  form-group-sm">
						{!! Form::label('transmission','Transmisión', ['class'=>'col-sm-2 control-label']) !!}
						<div class="col-sm-10">
						{!! Form::text('transmission', null, ['class'=>'form-control']) !!}
						</div>
					</div>
					<div class="form-group  form-group-sm">
						{!! Form::label('seats','Asientos', ['class'=>'col-sm-2 control-label']) !!}
						<div class="col-sm-10">
						{!! Form::text('seats', null, ['class'=>'form-control']) !!}
						</div>
					</div>
					<div class="form-group  form-group-sm">
						{!! Form::label('fuel','Combustible', ['class'=>'col-sm-2 control-label']) !!}
						<div class="col-sm-10">
						{!! Form::text('fuel', null, ['class'=>'form-control']) !!}
						</div>
					</div>
					<div class="form-group form-group-sm">
						{!! Form::label('price','Precio', ['class'=>'col-sm-2 control-label']) !!}
						<div class="col-sm-3">
								<div class="input-group">
									<div class="input-group-addon">US$</div>
									<input type="hidden" name='currency_id' value='2'>
									{!! Form::text('price', null, ['class'=>'form-control']) !!}
								</div>
						</div>
					</div>
					<div class="form-group  form-group-sm">
						{!! Form::label('image','Foto', ['class'=>'col-sm-2 control-label']) !!}
						<div class="col-sm-10">
						{!! Form::file('image', ['class'=>'form-control', 'accept'=>'image/*']) !!}
						</div>
					</div>