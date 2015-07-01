					<div class="form-group  form-group-sm">
						{!! Form::label('name','Nombre', ['class'=>'col-sm-2 control-label']) !!}
						<div class="col-sm-10">
						{!! Form::text('name', null, ['class'=>'form-control uppercase']) !!}
						</div>
					</div>
					<div class="form-group  form-group-sm">
						{!! Form::label('email','Correo Electrónico', ['class'=>'col-sm-2 control-label']) !!}
						<div class="col-sm-10">
						{!! Form::text('email', null, ['class'=>'form-control']) !!}
						</div>
					</div>
					<div class="form-group  form-group-sm">
						{!! Form::label('password','Contraseña', ['class'=>'col-sm-2 control-label']) !!}
						<div class="col-sm-10">
						{!! Form::password('password', ['class'=>'form-control']) !!}
						</div>
					</div>
					<div  class="form-group  form-group-sm">
						{!! Form::label('select-roles','Seleccionar Roles', ['class'=>'col-sm-2 control-label']) !!}
						<div class="col-sm-10">
							@if(\Auth::user()->is_superuser)
							<div class="checkbox">
								<label>
									{!! Form::checkbox('is_superuser', '1', null,['class'=>'checkbox']) !!} SUPER USUARIO
								</label>
							</div>
							@endif

							@foreach($roles as $role)
							<div class="checkbox">
								<label>
									@if(isset($model) and isset($role->users()->where('user_id',$model->id)->first()->id))
									{!! Form::checkbox('roles[]', $role->id, true) !!}
									@else
									{!! Form::checkbox('roles[]', $role->id) !!}
									@endif
									{{ $role->name }}
								</label>
							</div>
							@endforeach
						</div>
					</div>