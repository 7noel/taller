@extends('app')
@section('content')
<div class="container">
  <div class="row">
    <div class="col col-md-6 col-md-offset-3"   >
      <div class="panel panel-default">
        <div class="panel-heading"><h3 class="panel-title">Correo para Placa: {{ $vehicle->Placa }}</h3></div>
          <div class="panel-body">
            {!! Form::open(['route' => 'autorepair.service_reminder.send_email', 'method' => 'post']) !!}
            <div class="form-group">
              {!! Form::label('email', 'E-Mail') !!}
              {!! Form::email('email', $vehicle->Email, ['class' => 'form-control' ]) !!}
              <input type="hidden" name="last_page" value="{{ URL::previous() }}">
            </div>
              <div class="form-group">
                {!! Form::hidden('plate', $vehicle->Placa) !!}
                {!! Form::label('subject', 'Asunto') !!}
                {!! Form::text('subject', 'Recordatorio para su proximo servicio', ['class' => 'form-control' ]) !!}
              </div>
              <div class="form-group">
                {!! Form::submit('Enviar', ['class' => 'btn btn-success ' ] ) !!}
              </div>
            {!! Form::close() !!}
        </div>
      </div>
    </div>
  </div>
</div>
@endsection