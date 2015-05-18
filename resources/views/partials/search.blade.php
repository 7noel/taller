					{!! Form::model(Request::all() ,['route'=>Request::route()->getAction()['as'], 'method'=>'GET', 'class'=>'navbar-form navbar-left pull-right', 'role'=>'search']) !!}
					<div class="form-group">
						{!! Form::text('name', null, ['class'=>'form-control', 'placeholder'=>'']) !!}
					</div>
					<button type="submit" class="btn btn-default">Buscar</button>
					{!! Form::close() !!}
