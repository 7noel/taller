					<table class="table table-hover table-condensed">
						<tr>
							<th>#</th>
							<th>Razón Social</th>
							<th>DNI/RUC</th>
							<th>Acciones</th>
						</tr>
						@foreach($models as $model)
						<tr data-id="{{ $model->id }}">
							<td>{{ $model->id }}</td>
							<td>{{ $model->company_name }} </td>
							<td>{{ $model->doc }} </td>
							<td>
								<a href="{{ route( 'sales.car_quotes.afluencia.index' ) }}" class="btn btn-success btn-xs"><span class="glyphicon glyphicon-user" aria-hidden="true"></span> Afluencia</a>
								<a href="{{ route( str_replace('index', 'edit', Request::route()->getAction()['as']) , $model) }}" class="btn btn-primary btn-xs"><span class="glyphicon glyphicon-pencil" aria-hidden="true"></span> Editar</a>
								<a href="#" class="btn-delete btn btn-danger btn-xs"><span class="glyphicon glyphicon-trash" aria-hidden="true"></span> Eliminar</a>
							</td>
						</tr>
						@endforeach
					</table>