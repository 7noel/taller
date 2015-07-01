					<div class="form-group  form-group-sm">
						{!! Form::label('name','Nombre', ['class'=>'col-sm-2 control-label']) !!}
						<div class="col-sm-10">
						{!! Form::text('name', null, ['class'=>'form-control uppercase']) !!}
						</div>
					</div>
					<div class="form-group  form-group-sm">
						{!! Form::label('description','Descripcion', ['class'=>'col-sm-2 control-label']) !!}
						<div class="col-sm-10">
						{!! Form::text('description', null, ['class'=>'form-control']) !!}
						</div>
					</div>
					<div class="form-group  form-group-sm">
						{!! Form::label('brand_id','Marca', ['class'=>'col-sm-2 control-label']) !!}
						<div class="col-sm-10">
							<div class="form-inline">
							{!! Form::select('brand_id',$brands,null,['class'=>'form-control']) !!}
							</div>
						</div>
					</div>
					<div class="form-group form-group-sm">
						{!! Form::label('image','Foto', ['class'=>'col-sm-2 control-label']) !!}
						<div class="col-sm-8">
						{!! Form::file('image', ['class'=>'form-control file_img_preview', 'accept'=>'image/*']) !!}
						</div>
						<div class="col-sm-offset-1">
							@if(isset($model->image) and $model->image!='')
							<img class="img_preview" src="{{ '/storage/img/'.$model->image }}" alt=""  width="350px">
							@else
							<img class="img_preview" src="" alt=""  width="350px">
							@endif
						</div>
					</div>