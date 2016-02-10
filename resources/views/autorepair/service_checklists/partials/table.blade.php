					<table class="table table-hover table-condensed">
						<tr>
							<th>#</th>
							<th>NroOrden</th>
							<th>Placa</th>
							<th>Cliente</th>
							<th>Estado</th>
							<th>Acciones</th>
						</tr>
						@foreach($models as $model)
						<tr data-id="{{ $model->id }}">
							<td>{{ $model->id }}</td>
							<td>{{ $model->order_id }}</td>
							<td>{{ $model->plate }}</td>
							<td>{{ $model->company_name }} </td>
							<td>
								@if($model->status=='process')
								<button type="button" class="btn btn-warning btn-xs" disabled="disabled"> {{ strtoupper($model->status) }} </button>
								@else
								<a href="{{ route( 'autorepair.service_checklists.change_status' , $model) }}" class="btn btn-warning btn-xs"> {{ strtoupper($model->status) }} </a>
								@endif
							</td>
							<td>
								@if($model->status=='approved' or $model->status=='printed')
								<button data-print="{{ route('autorepair.service_checklists.print', $model->id) }}" class="btn btn-success btn-xs btn-print"><span class="glyphicon glyphicon-print" aria-hidden="true"></span> </button>
								@else
								<button class="btn btn-success btn-xs btn-print" disabled="disabled"><span class="glyphicon glyphicon-print" aria-hidden="true"></span> </button>
								@endif
								<a href="{{ route( str_replace('index', 'edit', Request::route()->getAction()['as']) , $model) }}" class="btn btn-primary btn-xs"><span class="glyphicon glyphicon-pencil" aria-hidden="true"></span> Editar</a>
								
								<a href="#" class="btn-delete btn btn-danger btn-xs"><span class="glyphicon glyphicon-trash" aria-hidden="true"></span> Eliminar</a>
							</td>
						</tr>
						@endforeach
					</table>