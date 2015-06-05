					<div class="form-group  form-group-sm">
						{!! Form::label('company','Empresa', ['class'=>'col-sm-2 control-label']) !!}
						<div class="col-sm-10">
							<div class="input-group">
								{!! Form::hidden('company_id', null, ['id'=>'company_id']) !!}
								{!! Form::text('company', null, ['class'=>'form-control', 'id'=>'txtcompany']) !!}
								<a href="#" type="button" class="btn btn-info btn-xs input-group-addon" aria-label="Left Align">
									<span class="glyphicon glyphicon-eye-open" aria-hidden="true"></span>
								</a>
								<a href="#" type="button" class="btn btn-success btn-xs input-group-addon" aria-label="Left Align">
									<span class="glyphicon glyphicon-plus" aria-hidden="true"></span>
								</a>
							</div>
						</div>
					</div>
					<div class="form-group  form-group-sm">
						{!! Form::label('version_id','Modelo/Version', ['class'=>'col-sm-2 control-label']) !!}
						<div class="col-sm-10">
							<div class="form-inline">
							{!! Form::select('version_id',$versions,null,['class'=>'form-control', 'id'=>'lstVersions']) !!}
							</div>
						</div>
					</div>
					<div class="form-group  form-group-sm">
						{!! Form::label('catalog_car_id','Fabricación/Modelo', ['class'=>'col-sm-2 control-label']) !!}
						<div class="col-sm-10">
							<div class="form-inline">
							{!! Form::select('catalog_car_id', ((isset($years)) ? $years : [''=>'Seleccionar']), null,['class'=>'form-control', 'id'=>'lstYears']); !!}
							</div>
						</div>
					</div>
					<div class="form-group form-group-sm">
						{!! Form::label('price','Precio', ['class'=>'col-sm-2 control-label']) !!}
						<div class="col-sm-3">
								<div class="input-group">
									<div class="input-group-addon currency">{{ (isset($model)) ? $model->currency->symbol : 'US$' }}</div>
									{!! Form::hidden('currency_id', null, ['id'=>'currency_id']) !!}
									{!! Form::text('price', null, ['class'=>'form-control', 'readonly'=>'readonly']) !!}
								</div>
						</div>
					</div>
					<div class="form-group form-group-sm">
						{!! Form::label('set_price','Precio Especial', ['class'=>'col-sm-2 control-label']) !!}
						<div class="col-sm-3">
								<div class="input-group">
									<div class="input-group-addon currency">{{ (isset($model)) ? $model->currency->symbol : 'US$' }}</div>
									<input type="hidden" name='currency_id' value='2'>
									{!! Form::text('set_price', null, ['class'=>'form-control']) !!}
								</div>
						</div>
					</div>