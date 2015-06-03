					<table class="table table-hover table-condensed">
						<tr>
							<th>#</th>
							<th>Nombre</th>
							<th class="text-center">Interior</th>
							<th class="text-center">Exterior</th>
							<th>Acciones</th>
						</tr>
						@foreach($models as $model)
						<tr data-id="{{ $model->id }}">
							<td>{{ $model->id }}</td>
							<td>{{ $model->name }} </td>
							<td class="text-center">
								@if($model->in)
								<span class="glyphicon glyphicon-ok"></span>
								@endif
							</td>
							<td class="text-center">
								@if($model->out)
								<span class="glyphicon glyphicon-ok"></span>
								@endif
							</td>
							<td>
								<a href="{{ route( str_replace('index', 'edit', Request::route()->getAction()['as']) , $model) }}" class="btn btn-primary btn-xs"><span class="glyphicon glyphicon-pencil" aria-hidden="true"></span> Editar</a>
								<a href="#" class="btn-delete btn btn-danger btn-xs"><span class="glyphicon glyphicon-trash" aria-hidden="true"></span> Eliminar</a>
							</td>
						</tr>
						@endforeach
					</table>