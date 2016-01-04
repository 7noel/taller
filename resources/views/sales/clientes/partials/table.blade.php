					<table class="table table-hover table-condensed">
						<thead>
							<tr>
								<th>#</th>
								<th>Razón Social</th>
								<th>DNI/RUC</th>
								<th>Acciones</th>
							</tr>
						</thead>
						<tbody>
							@foreach($models as $model)
							<?php $doc = ($model->RUC == 0) ? $model->DNI : $model->RUC ; ?>
							<tr data-id="{{ $model->CodCliente }}">
								<td>{{ $model->CodCliente }}</td>
								<td>{{ $model->NombreRaz }} </td>
								<td>{{ $doc }} </td>
								<td>
									<a href="{{ route( str_replace('index', 'edit', Request::route()->getAction()['as']) , $model->CodCliente) }}" class="btn btn-primary btn-xs"><span class="glyphicon glyphicon-pencil" aria-hidden="true"></span> Editar</a>
									<a href="#" class="btn-delete btn btn-danger btn-xs"><span class="glyphicon glyphicon-trash" aria-hidden="true"></span> Eliminar</a>
								</td>
							</tr>
							@endforeach
						</tbody>
					</table>