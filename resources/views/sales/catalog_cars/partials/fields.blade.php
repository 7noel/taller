<br>
					<div class="form-group  form-group-sm">
						{!! Form::label('manufacture_year','Año Fabricación', ['class'=>'col-sm-2 control-label']) !!}
						<div class="col-sm-3">
						{!! Form::text('manufacture_year', null, ['class'=>'form-control', 'required']) !!}
						</div>
						{!! Form::label('model_year','Año Modelo', ['class'=>'col-sm-2 control-label']) !!}
						<div class="col-sm-3">
						{!! Form::text('model_year', null, ['class'=>'form-control', 'required']) !!}
						</div>
					</div>
					<div class="form-group  form-group-sm">
						{!! Form::label('version_id','Modelo / Versión', ['class'=>'col-sm-2 control-label']) !!}
						<div class="col-sm-3">
							{!! Form::select('version_id',$versions,null,['class'=>'form-control', 'required']) !!}
						</div>
						{!! Form::label('cylinder','Cilindrada', ['class'=>'col-sm-2 control-label']) !!}
						<div class="col-sm-3">
						{!! Form::text('cylinder', null, ['class'=>'form-control', 'required']) !!}
						</div>
					</div>
					<div class="form-group  form-group-sm">
						{!! Form::label('transmission','Transmisión', ['class'=>'col-sm-2 control-label']) !!}
						<div class="col-sm-3">
						{!! Form::text('transmission', null, ['class'=>'form-control', 'required']) !!}
						</div>
						{!! Form::label('seats','Asientos', ['class'=>'col-sm-2 control-label']) !!}
						<div class="col-sm-3">
						{!! Form::text('seats', null, ['class'=>'form-control', 'required']) !!}
						</div>
					</div>
					<div class="form-group  form-group-sm">
						{!! Form::label('fuel','Combustible', ['class'=>'col-sm-2 control-label']) !!}
						<div class="col-sm-3">
						{!! Form::text('fuel', null, ['class'=>'form-control', 'required']) !!}
						</div>
						{!! Form::label('price','Precio', ['class'=>'col-sm-2 control-label']) !!}
						<div class="col-sm-3">
								<div class="input-group">
									<div class="input-group-addon">US$</div>
									<input type="hidden" name='currency_id' value='2'>
									{!! Form::text('price', null, ['class'=>'form-control', 'required']) !!}
								</div>
						</div>
					</div>
					<div class="form-group  form-group-sm">
						{!! Form::label('image','Foto', ['class'=>'col-sm-2 control-label']) !!}
						<div class="col-sm-10">
						{!! Form::file('image', ['class'=>'form-control', 'accept'=>'image/*', 'id'=>'file_img_preview']) !!}
						</div>
					</div>
					<div class="form-group  form-group-sm">
						{!! Form::label('image',' ', ['class'=>'col-sm-2 control-label']) !!}
						<div class="col-sm-10">
							@if(isset($model->image) and $model->image!='')
							<img id="img_preview" src="{{ '/storage/img/'.$model->image }}" alt=""  class="col-sm-10">
							@else
							<img id="img_preview" src="" alt=""  class="col-sm-10">
							@endif
						</div>
					</div>