					<br>
					<div class="form-group form-group-sm">
						{!! Form::label('manufacture_year','Año Fabricación', ['class'=>'col-sm-2 control-label']) !!}
						<div class="col-sm-3">
						{!! Form::text('manufacture_year', null, ['class'=>'form-control', 'required']) !!}
						</div>
						{!! Form::label('model_year','Año Modelo', ['class'=>'col-sm-2 control-label']) !!}
						<div class="col-sm-3">
						{!! Form::text('model_year', null, ['class'=>'form-control', 'required']) !!}
						</div>
					</div>
					<div class="form-group form-group-sm">
						{!! Form::label('version_id','Modelo / Versión', ['class'=>'col-sm-2 control-label']) !!}
						<div class="col-sm-3">
							{!! Form::select('version_id',$versions,null,['class'=>'form-control', 'required']) !!}
						</div>
						{!! Form::label('cylinder','Cilindrada', ['class'=>'col-sm-2 control-label']) !!}
						<div class="col-sm-3">
						{!! Form::text('cylinder', null, ['class'=>'form-control', 'required']) !!}
						</div>
					</div>
					<div class="form-group form-group-sm">
						{!! Form::label('transmission','Transmisión', ['class'=>'col-sm-2 control-label']) !!}
						<div class="col-sm-3">
						{!! Form::text('transmission', null, ['class'=>'form-control', 'required']) !!}
						</div>
						{!! Form::label('seats','Asientos', ['class'=>'col-sm-2 control-label']) !!}
						<div class="col-sm-3">
						{!! Form::text('seats', null, ['class'=>'form-control', 'required']) !!}
						</div>
					</div>
					<div class="form-group form-group-sm">
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
					<div class="form-group form-group-sm">
						{!! Form::label('image1','Foto', ['class'=>'col-sm-2 control-label']) !!}
						<div class="col-sm-8">
						{!! Form::file('image1', ['class'=>'form-control file_img_preview', 'accept'=>'image/*']) !!}
						</div>
						<div class="col-sm-offset-1">
							@if(isset($model->image) and $model->image1!='')
							<img class="img_preview" src="{{ '/storage/img/'.$model->image1 }}" alt=""  width="350px">
							@else
							<img class="img_preview" src="" alt=""  width="350px">
							@endif
						</div>
					</div>
					<div class="form-group form-group-sm">
						{!! Form::label('image2','Foto', ['class'=>'col-sm-2 control-label']) !!}
						<div class="col-sm-6">
						{!! Form::file('image2', ['class'=>'form-control file_img_preview', 'accept'=>'image/*']) !!}
						</div>
						<div class="col-sm-2 checkbox">
								<label>
									{!! Form::checkbox('delete_image2', '1') !!} Eliminar
								</label>
						</div>
						<div class="col-sm-offset-1">
							@if(isset($model->image) and $model->image2!='')
							<img class="img_preview" src="{{ '/storage/img/'.$model->image2 }}" alt=""  width="350px">
							@else
							<img class="img_preview" src="" alt=""  width="350px">
							@endif
						</div>
					</div>
					<div class="form-group form-group-sm">
						{!! Form::label('description_image3','Descripcion 03', ['class'=>'col-sm-2 control-label']) !!}
						<div class="col-sm-3">
						{!! Form::text('description_image3', null, ['class'=>'form-control', 'required']) !!}
						</div>
					</div>
					<div class="form-group form-group-sm">
						{!! Form::label('image3','Foto Caracteristica', ['class'=>'col-sm-2 control-label']) !!}
						<div class="col-sm-8">
						{!! Form::file('image3', ['class'=>'form-control file_img_preview', 'accept'=>'image/*']) !!}
						</div>
						<div class="col-sm-offset-1">
							@if(isset($model->image) and $model->image3!='')
							<img class="img_preview" src="{{ '/storage/img/'.$model->image3 }}" alt=""  width="350px">
							@else
							<img class="img_preview" src="" alt=""  width="350px">
							@endif
						</div>
					</div>
					<div class="form-group form-group-sm">
						{!! Form::label('description_image4','Descripcion 04', ['class'=>'col-sm-2 control-label']) !!}
						<div class="col-sm-3">
						{!! Form::text('description_image4', null, ['class'=>'form-control', 'required']) !!}
						</div>
					</div>
					<div class="form-group form-group-sm">
						{!! Form::label('image4','Foto Caracteristica', ['class'=>'col-sm-2 control-label']) !!}
						<div class="col-sm-8">
						{!! Form::file('image4', ['class'=>'form-control file_img_preview', 'accept'=>'image/*']) !!}
						</div>
						<div class="col-sm-offset-1">
							@if(isset($model->image) and $model->image4!='')
							<img class="img_preview" src="{{ '/storage/img/'.$model->image4 }}" alt=""  width="350px">
							@else
							<img class="img_preview" src="" alt=""  width="350px">
							@endif
						</div>
					</div>