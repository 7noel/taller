					<div class="form-group  form-group-sm">
						{!! Form::label('name','Nombre', ['class'=>'col-sm-2 control-label']) !!}
						<div class="col-sm-10">
						{!! Form::text('name', null, ['class'=>'form-control uppercase']) !!}
						</div>
					</div>
					<div class="form-group  form-group-sm">
						{!! Form::label('action','Accion', ['class'=>'col-sm-2 control-label']) !!}
						<div class="col-sm-10">
						{!! Form::text('action', null, ['class'=>'form-control']) !!}
						</div>
					</div>
					<div class="form-group  form-group-sm">
						{!! Form::label('description','Descripcion', ['class'=>'col-sm-2 control-label']) !!}
						<div class="col-sm-10">
						{!! Form::text('description', null, ['class'=>'form-control']) !!}
						</div>
					</div>
					<div class="form-group  form-group-sm">
						{!! Form::label('permission_group_id','Grupo', ['class'=>'col-sm-2 control-label']) !!}
						<div class="col-sm-10">
						{!! Form::select('permission_group_id', $groups, null,['class'=>'form-control']); !!}
						</div>
					</div>
					<div class="form-group  form-group-sm">
						<label class='col-sm-2 control-label'> *</label>
						<div class="checkbox">
							<label>
								{!! Form::checkbox('generate', null); !!}
								Generar Permisos para Listar, Ver, Crear, Editar y Eliminar
							</label>
						</div>
					</div>