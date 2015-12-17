					<table class="table table-hover table-condensed">
						<tr>
							<th>#</th>
							<th>Fecha</th>
							<th>Placa</th>
							<th>Modelo</th>
							<th>Cliente</th>
							<th>Acciones</th>
						</tr>
						@foreach($models as $model)
						<tr data-id="{{ $model->id }}">
							<td>{{ $model->idcita }}</td>
							<td>{{ $model->fecha }}</td>
							<td>{{ $model->placa }}</td>
							<td>{{ $model->Modelo }}</td>
							<td>{{ $model->nom_clie }}</td>
							<td>
								<a href="{{ route( str_replace('index', 'edit', Request::route()->getAction()['as']) , $model) }}" class="btn btn-primary btn-xs"><span class="glyphicon glyphicon-pencil" aria-hidden="true"></span> Editar</a>
								<a href="#" class="btn-delete btn btn-danger btn-xs"><span class="glyphicon glyphicon-trash" aria-hidden="true"></span> Eliminar</a>
							</td>
						</tr>
						@endforeach
					</table>