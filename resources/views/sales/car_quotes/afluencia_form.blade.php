@extends('app')

@section('content')
<div class="container">
	<div class="row">
		<div class="col-md-10 col-md-offset-1">
			<div class="panel panel-default">
				<div class="panel-heading panel-heading-custom">AFLUENCIA: NOEL HUILLCA HUAMANI</div>
				@if(Session::has('message'))
					<p class="alert alert-success">{{ Session::get('message') }}</p>
				@endif
				<div class="panel-body">
					<form action="#" class="form-horizontal">
						<div class="form-group  form-group-sm">
							{!! Form::label('company','Tipo', ['class'=>'col-sm-2 control-label']) !!}
							<div class="col-sm-10">
								<label class="col-sm-2">
									<input type="radio" name="optionsRadios" id="optionsRadios1" value="option1" checked> Presencial
								</label>
								<label class="col-sm-2">
									<input type="radio" name="optionsRadios" id="optionsRadios1" value="option1" checked> Telefónica
								</label>
								<label class="col-sm-2">
									<input type="radio" name="optionsRadios" id="optionsRadios1" value="option1" checked> Virtual
								</label>
							</div>
						</div>
						<div class="form-group form-group-sm">
							{!! Form::label('canal_id','Canal', ['class'=>'col-sm-2 control-label']) !!}
							<div class="col-sm-3">
								{!! Form::select('canal_id',$canals, null,['class'=>'form-control', 'required']) !!}
							</div>
							
						</div>
						<div class="form-group  form-group-sm">
							{!! Form::label('attention','Canal', ['class'=>'col-sm-2 control-label']) !!}
							<div class="col-sm-10">
								<input type="text" class="form-control" value="NOEL HUILLCA HUAMANI">
							</div>
						</div>
						<div class="form-group  form-group-sm">
							{!! Form::label('version_id','Modelo/Version', ['class'=>'col-sm-2 control-label']) !!}
							<div class="col-sm-3">
								{!! Form::select('version_id',$versions, null,['class'=>'form-control', 'id'=>'lstVersions', 'required']) !!}
							</div>
							{!! Form::label('catalog_car_id','Fabricación/Modelo', ['class'=>'col-sm-2 control-label']) !!}
							<div class="col-sm-3">
								{!! Form::select('catalog_car_id', [''=>'Seleccionar'], null,['class'=>'form-control', 'id'=>'lstYears', 'required']); !!}
							</div>
						</div>
						<div class="form-group">
							<div class="col-sm-offset-2 col-sm-10">
								<button type="submit" class="btn btn-primary"><span class="glyphicon glyphicon-floppy-disk" aria-hidden="true"></span> GRABAR AFLUENCIA </button>
							</div>
						</div>
					</form>
				</div>
			</div>
		</div>
	</div>
</div>


@endsection

@section('scripts')

@endsection