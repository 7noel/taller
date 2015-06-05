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