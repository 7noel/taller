					<div class="form-group  form-group-sm">
						{!! Form::label('date','Fecha', ['class'=>'col-sm-2 control-label']) !!}
						<div class="col-sm-3">
						<input type="date" name="date" class="form-control" id="_date" value=<?php echo ((isset($model)) ? $model->date : '') ; ?> >
						</div>
					</div>
					<div class="form-group  form-group-sm">
						{!! Form::label('a1','Asesor 1', ['class'=>'col-sm-2 control-label']) !!}
						<div class="col-sm-5">
						{!! Form::select('a1', $asesores, null, ['class'=>'form-control']) !!}
						</div>
					</div>
					<div class="form-group  form-group-sm">
						{!! Form::label('a2','Asesor 2', ['class'=>'col-sm-2 control-label']) !!}
						<div class="col-sm-5">
						{!! Form::select('a2', $asesores, null, ['class'=>'form-control']) !!}
						</div>
					</div>
					<div class="form-group  form-group-sm">
						{!! Form::label('a3','Asesor 3', ['class'=>'col-sm-2 control-label']) !!}
						<div class="col-sm-5">
						{!! Form::select('a3', $asesores, null, ['class'=>'form-control']) !!}
						</div>
					</div>
					<div class="form-group  form-group-sm">
						{!! Form::label('a4','Asesor 4', ['class'=>'col-sm-2 control-label']) !!}
						<div class="col-sm-5">
						{!! Form::select('a4', $asesores, null, ['class'=>'form-control']) !!}
						</div>
					</div>
					<div class="form-group  form-group-sm">
						{!! Form::label('a5','Asesor 5', ['class'=>'col-sm-2 control-label']) !!}
						<div class="col-sm-5">
						{!! Form::select('a5', $asesores, null, ['class'=>'form-control']) !!}
						</div>
					</div>