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
                  <p>Estimado Cliente:</p>
                  <p><h2><strong> {{$vehicle->NombreRaz}} </strong></h2></p>
                  <p>De acuerdo a nuestro historial de servicios y pensando en el cuidado y mantenimiento de su vehículo HONDA <strong>{{$vehicle->Modelo}}</strong> placa <strong>{{$vehicle->Placa}}</strong> MASAKI le recuerda que está próximo a cumplir <strong>{{$vehicle->nextkm}} Km</strong>.</p>
                  <p>Lo invitamos a reservar una cita haciendo click en <a href="http://www.masaki.com.pe/online/"><strong>CITAS</strong></a> o llamando al número 612-7511</p>
                  @if($checks)
                    @if($checks['check_warning'] and count($checks['check_warning']) > 0)
                      <br>De acuerdo a su última visita, consultar con su Asesor sobre las siguientes observaciones:<br>
                      @foreach($checks['check_warning'] as $item)
                      {{$item->checkitem_group->name}} / {{$item->name}} <br>
                      @endforeach
                    @endif
                    @if($checks['check_danger'] and count($checks['check_danger']) > 0)
                      <br>Como un tema de Urgencia, consultar con su Asesor sobre las siguientes observaciones:<br>
                      @foreach($checks['check_danger'] as $item)
                      {{$item->checkitem_group->name}} / {{$item->name}} <br>
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