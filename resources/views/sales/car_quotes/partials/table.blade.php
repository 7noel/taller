					<table class="table table-hover table-condensed">
						<tr>
							<th>#</th>
							<th>Vehicule</th>
							<th>Precio</th>
							<th>Acciones</th>
						</tr>
						@foreach($models as $model)
						<tr data-id="{{ $model->id }}">
							<td>{{ $model->id }}</td>
							<td>{{ $model->catalog_car->version->modelo->name.' '.$model->catalog_car->version->name.' '.$model->catalog_car->manufacture_year.'/'.$model->catalog_car->model_year }} </td>
							<td>{{ $model->precio }} </td>
							<td>
								<a href="{{ route( str_replace('index', 'edit', Request::route()->getAction()['as']) , $model) }}" class="btn btn-primary btn-xs"><span class="glyphicon glyphicon-pencil" aria-hidden="true"></span> Editar</a>
								<a href="#" class="btn-delete btn btn-danger btn-xs"><span class="glyphicon glyphicon-trash" aria-hidden="true"></span> Eliminar</a>
							</td>
						</tr>
						@endforeach
					</table>