@extends('app')

@section('content')
<div class="container">

	<div class="row">
		<div class="col-md-8">
			<div class="panel panel-default">
				<div class="panel-heading panel-heading-custom">Cambiar Estado Del Service Reminder: {{ $vehicle->Placa}} </div>

				<div class="panel-body">
					<div class="form-horizontal">
						<div class="form-group">
							<label class='col-sm-2 control-label'>Placa:</label>
							<div class="col-sm-6">
								<p class="form-control-static"> {{$vehicle->Placa}} </p>
							</div>
						</div>
						<div class="form-group">
							<label class="col-sm-2 control-label">Cliente:</label>
							<div class="col-sm-6">
								<p class="form-control-static"> {{$vehicle->NombreRaz}} </p>
							</div>
						</div>
					</div>
					{!! Form::model($vehicle, ['route'=>[ 'autorepair.service_reminder.save_status', $vehicle->Placa ], 'method'=>'GET', 'class'=>'form-horizontal']) !!}
					<div class="form-group">
						<input type="hidden" name="last_page" value="{{ URL::previous() }}">
						{!! Form::label('status','Estado', ['class'=>'col-sm-2 control-label']) !!}
						<div class="col-sm-6">
							{!! Form::select('status', $status_list, $vehicle->status, ['class'=>'form-control', 'required']) !!}
						</div>
					</div>
					<div class="form-group">
						<div class="col-sm-3">
							<button type="submit" class="btn btn-primary"><span class="glyphicon glyphicon-floppy-saved" aria-hidden="true"></span> GRABAR</button>
						</div>
					</div>
					{!! Form::close() !!}
				</div>
			</div>
		</div>
	</div>
</div>
@endsection
