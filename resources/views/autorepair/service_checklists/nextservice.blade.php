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
							<th>Preventivo</th>
							<th>Fecha</th>
							<th>Email</th>
							<th>Estado</th>
							<th>Acciones</th>
						</tr>
						@foreach($vehicles as $vehicle)
						<tr data-id="{{ $vehicle->Placa }}">
							<td>{{ $vehicle->Placa }}</td>
							<td>{{ $vehicle->Modelo }}</td>
							<td>{{ utf8_encode($vehicle->NombreRaz) }} </td>
							<td align="right">{{ $vehicle->nextkm }} </td>
							<td align="right">{{ $vehicle->nextdate }} </td>
							<td align="center">
								@if($vehicle->send_email)
								<span class="glyphicon glyphicon-ok"></span>
								@endif
							</td>
							<td align="center">
								@if($vehicle->status == 'SI')
								<span class="glyphicon glyphicon-ok"></span>
								@elseif($vehicle->status == 'NO')
								<span class="glyphicon glyphicon-remove"></span>
								@elseif($vehicle->status == 'CALL_AGAIN')
								<span class="glyphicon glyphicon-phone-alt"></span>
								@endif
							</td>
							<td>
								@if($request['type']=='message')
								<a href="{{ route('autorepair.service_reminder.form_email', $vehicle->Placa) }}" class="btn btn-success btn-xs"><span class="glyphicon glyphicon-envelope" aria-hidden="true"></span> Enviar</a>
								@else
								<a href="{{ route( 'autorepair.service_reminder.change_status' , $vehicle->Placa) }}" class="btn btn-success btn-xs"><span class="glyphicon glyphicon-earphone" aria-hidden="true"></span> Llamar </a>
								@endif
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
