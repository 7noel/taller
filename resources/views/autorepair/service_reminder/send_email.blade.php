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
                {!! Form::label('body', 'Mensaje') !!}
                <textarea name="body" class="form-control ckeditor">
                  Señores: {{$vehicle->NombreRaz}} <br>
                  Atención: {{$vehicle->Contacto1}} <br>
                  Recordatorio para su proximo servicio de  {{$vehicle->nextkm}} kilometros el día {{$vehicle->nextdate}} <br>
                  @if($checks)
                    @if($checks['check_warning'])
                      <br>Para la Proxima visita <br>
                      @foreach($checks['check_warning'] as $item)
                      {{$item->name}} <br>
                      @endforeach
                    @endif
                    @if($checks['check_danger'])
                      <br>Urgentes: <br>
                      @foreach($checks['check_danger'] as $item)
                      {{$item->name}} <br>
                      @endforeach
                    @endif
                  @endif
                </textarea>
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