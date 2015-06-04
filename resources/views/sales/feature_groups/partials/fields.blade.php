					<div class="form-group  form-group-sm">
						{!! Form::label('name','Nombre', ['class'=>'col-sm-2 control-label']) !!}
						<div class="col-sm-10">
						{!! Form::text('name', null, ['class'=>'form-control']) !!}
						</div>
					</div>
					<div class="form-group  form-group-sm">
						{!! Form::label('template','Plantilla', ['class'=>'col-sm-2 control-label']) !!}
						<div class="col-sm-10">
							<div class="form-inline">
							{!! Form::select('template',$templates,null,['class'=>'form-control']) !!}
							</div>
						</div>
					</div>