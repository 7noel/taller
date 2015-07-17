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

					
					<div class="form-group">
						<div class="col-sm-4 col-sm-offset-1">
							<label>Permisos No Autorizados</label>
							<select name="origen[]" id="origen" multiple size="10" class="form-control">
								@foreach($permissions as $permission)
								@if(!isset($model) or !isset($permission->roles()->where('role_id',$model->id)->first()->id))
								<option value="{{ $permission->id }}" class="groupx group_{{ $permission->permission_group_id }}">{{ $permission->name }}</option>
								@endif
								@endforeach
							</select>
						</div>
						<div class="col-sm-3 btn-group-vertical" role="group">
							<select name="" id="groups" class="form-control">
								<option value="">TODOS LOS GRUPOS</option>
								@foreach($groups as $group)
								<option value="{{ $group->id }}">{{ $group->name }}</option>
								@endforeach			
							</select>
							<br>
							<input type="button" class="btn btn-default pasar izq" value="Pasar »">
							<input type="button" class="btn btn-default quitar der" value="« Quitar">
							<input type="button" class="btn btn-default pasartodos izq" value="Todos »">
							<input type="button" class="btn btn-default quitartodos der" value="« Ninguno">
						</div>
						<div class="col-sm-4">
							<label>Permisos Autorizados</label>
							<select name="permissions[]" id="destino" multiple size="10" class="form-control">
								@if(isset($model))
									@foreach($model->permissions as $permi)
									<option value="{{ $permi->id }}" class="groupx group_{{ $permi->permission_group_id }}">{{ $permi->name }}</option>
									@endforeach
								@endif
							</select>
						</div>
					</div>