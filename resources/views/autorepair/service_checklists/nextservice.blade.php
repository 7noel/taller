@extends('app')

@section('content')
<div class="container">

	<div class="row">
		<div class="col-md-12">
			<div class="panel panel-default">
				<div class="panel-heading panel-heading-custom">PROXIMOS SERVICIOS</div>

				<div class="panel-body">

					{!! Form::model($request, ['route'=>[ 'autorepair.vehicles.nextservice' ], 'method'=>'GET', 'class'=>'form-horizontal']) !!}
					<div class="form-group form-group-sm">
						{!! Form::label('datemin','Fecha Inicial', ['class'=>'col-sm-1 control-label']) !!}
						<div class="col-sm-2">
							<input type="date" name="datemin" value="{{$request['datemin']}}" class="form-control uppercase" required>
						</div>
						{!! Form::label('datemax','Fecha Final', ['class'=>'col-sm-1 control-label']) !!}
						<div class="col-sm-2">
							<input type="date" name="datemax" value="{{$request['datemax']}}" class="form-control uppercase" required>
						</div>
						<div class="col-sm-2 radio">
							<label>
								<input type="radio" name="type" value="message" {{ ($request['type']=='message') ? 'checked' : '' }}>Correo
							</label>&nbsp;&nbsp;&nbsp;
							<label>
								<input type="radio" name="type" value="call" {{ ($request['type']=='call') ? 'checked' : '' }}>Llamada
							</label>
						</div>
						<div class="col-sm-3">
							<button type="submit" class="btn btn-primary"><span class="glyphicon glyphicon-search" aria-hidden="true"></span> BUSCAR</button>
						</div>
					</div>
					{!! Form::close() !!}
				</div>
			</div>
			<div class="panel panel-default">
				<div class="panel-heading panel-heading-custom">RESULTADO</div>
				<div class="panel-body">
					<table class="table table-hover table-condensed">
						<tr>
							<th>Placa</th>
							<th>Modelo</th>
							<th>Cliente</th>
							<th>Contacto</th>
							<th>Telefonos</th>
							<th>Ult. Serv.</th>
							<th>Preventivo</th>
							<th>Fecha</th>
							<th>Estado</th>
							<th>Acciones</th>
						</tr>
						@foreach($vehicles as $vehicle)
						<tr data-id="{{ $vehicle->Placa }}">
							<td>{{ $vehicle->Placa }}</td>
							<td>{{ $vehicle->Modelo }}</td>
							<td>{{ utf8_encode($vehicle->NombreRaz) }} </td>
							<td>{{ utf8_encode($vehicle->Contacto1) }} </td>
							<td>{{ $vehicle->Telefonos."/".$vehicle->Celular }}</td>
							<td align="right">{{ $vehicle->preventive3 }} </td>
							<td align="right">{{ $vehicle->nextkm }} </td>
							<td align="right">{{ $vehicle->nextdate }} </td>
							<td> </td>
							<td>
								<a href="{{ route('autorepair.service_reminder.form_email', $vehicle->Placa) }}" class="btn btn-success btn-xs"><span class="glyphicon glyphicon-envelope" aria-hidden="true"></span> Enviar</a>
							</td>
						</tr>
						@endforeach
					</table>
				</div>
			</div>
		</div>
	</div>
</div>
@endsection
