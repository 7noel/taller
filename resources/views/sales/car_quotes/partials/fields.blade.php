					<div class="form-group  form-group-sm">
						{!! Form::label('company','Empresa', ['class'=>'col-sm-2 control-label']) !!}
						<div class="col-sm-10">
							<div class="input-group">
								{!! Form::hidden('company_id', null, ['id'=>'company_id']) !!}
								{!! Form::text('company', ((isset($model->company_id)) ? $model->company->company_name : null), ['class'=>'form-control', 'id'=>'txtcompany', 'required']) !!}
								<a href="{{ route('finances.companies.show',':_ID') }}" class="btn btn-info btn-xs input-group-addon edit" aria-label="Left Align">
									<span class="glyphicon glyphicon-eye-open" aria-hidden="true"></span>
								</a>
								<a href="{{ route('finances.companies.edit',':_ID') }}" class="btn btn-primary btn-xs input-group-addon edit" aria-label="Left Align">
									<span class="glyphicon glyphicon-pencil" aria-hidden="true"></span>
								</a>
								<a href="{{ route('finances.companies.create') }}" class="btn btn-success btn-xs input-group-addon" aria-label="Left Align">
									<div href="#"><span class="glyphicon glyphicon-plus" aria-hidden="true"></span></div>
								</a>
							</div>
						</div>
					</div>
					<div class="form-group  form-group-sm">
						{!! Form::label('attention','Atención', ['class'=>'col-sm-2 control-label']) !!}
						<div class="col-sm-10">
						{!! Form::text('attention', null, ['class'=>'form-control']) !!}
						</div>
					</div>
					<div class="form-group  form-group-sm">
						{!! Form::label('version_id','Modelo/Version', ['class'=>'col-sm-2 control-label']) !!}
						<div class="col-sm-3">
							{!! Form::select('version_id',$versions, ((isset($model->catalog_car_id)) ? $model->catalog_car->version_id : null),['class'=>'form-control', 'id'=>'lstVersions', 'required']) !!}
						</div>
						{!! Form::label('catalog_car_id','Fabricación/Modelo', ['class'=>'col-sm-2 control-label']) !!}
						<div class="col-sm-3">
							{!! Form::select('catalog_car_id', ((isset($years)) ? $years : [''=>'Seleccionar']), null,['class'=>'form-control', 'id'=>'lstYears', 'required']); !!}
						</div>
					</div>
					<div class="form-group form-group-sm">
						{!! Form::label('price','Precio', ['class'=>'col-sm-2 control-label']) !!}
						<div class="col-sm-3">
							<div class="input-group">
								<div class="input-group-addon currency">{{ (isset($model)) ? $model->currency->symbol : 'US$' }}</div>
								{!! Form::hidden('currency_id', null, ['id'=>'currency_id']) !!}
								{!! Form::text('price', null, ['class'=>'form-control', 'readonly']) !!}
							</div>
						</div>
						{!! Form::label('set_price','Precio Especial', ['class'=>'col-sm-2 control-label']) !!}
						<div class="col-sm-3">
							<div class="input-group">
								<div class="input-group-addon currency">{{ (isset($model)) ? $model->currency->symbol : 'US$' }}</div>
								<input type="hidden" name='currency_id' value='2'>
								{!! Form::text('set_price', null, ['class'=>'form-control', 'required']) !!}
							</div>
						</div>
					</div>
