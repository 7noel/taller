					<table class="table table-hover">
						<tr>
							<th>#</th>
							<th>Nombre</th>
							<th>Símbolo</th>
							<th>Tipo</th>
							<th class="text-right">Valor</th>
							<th>Acciones</th>
						</tr>
						@foreach($models as $model)
						<tr data-id="{{ $model->id }}">
							<td>{{ $model->id }}</td>
							<td>{{ $model->name }}</td>
							<td>{{ $model->symbol }}</td>
							<td>{{ $model->unit_type->name }}</td>
							<td class="text-right">{{ $model->value }}</td>
							<td>
								<a href="{{ route( str_replace('index', 'edit', Request::route()->getAction()['as']) , $model) }}" class="btn btn-primary btn-xs"><span class="glyphicon glyphicon-pencil" aria-hidden="true"></span> Editar</a>
								<a href="#" class="btn-delete btn btn-danger btn-xs"><span class="glyphicon glyphicon-trash" aria-hidden="true"></span> Eliminar</a>
							</td>
						</tr>
						@endforeach
					</table>