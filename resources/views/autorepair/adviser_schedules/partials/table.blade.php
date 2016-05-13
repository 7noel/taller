					<table class="table table-hover table-condensed">
						<tr>
							<th>#</th>
							<th>Fecha</th>
							<th>Asesor 1</th>
							<th>Asesor 2</th>
							<th>Asesor 3</th>
							<th>Asesor 4</th>
							<th>Asesor 5</th>
							<th>Acciones</th>
						</tr>
						@foreach($models as $model)
						<tr data-id="{{ $model->id }}">
							<td>{{ $model->id }}</td>
							<td>{{ $model->date }}</td>
							<td>{{ $model->a1 }}</td>
							<td>{{ $model->a2 }}</td>
							<td>{{ $model->a3 }}</td>
							<td>{{ $model->a4 }}</td>
							<td>{{ $model->a5 }}</td>
							<td>
								<a href="{{ route( str_replace('index', 'edit', Request::route()->getAction()['as']) , $model) }}" class="btn btn-primary btn-xs"><span class="glyphicon glyphicon-pencil" aria-hidden="true"></span> Editar</a>
								<a href="#" class="btn-delete btn btn-danger btn-xs"><span class="glyphicon glyphicon-trash" aria-hidden="true"></span> Eliminar</a>
							</td>
						</tr>
						@endforeach
					</table>