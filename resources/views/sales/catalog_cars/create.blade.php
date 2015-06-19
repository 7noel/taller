@extends('app')

@section('content')
<div class="container">

	<div class="row">
		<div class="col-md-10 col-md-offset-1">
			<div class="panel panel-default">
				<div class="panel-heading panel-heading-custom">{{ config('options.' . Request::route()->getAction()['as'] .'.panel') }}</div>
					<div class="panel-body">
						<ul id="myTab" class="nav nav-tabs" role="tablist">
							<li role="presentation" class="active"><a href="#home" id="home-tab" role="tab" data-toggle="tab" aria-controls="home" aria-expanded="true">Vehículo</a></li>
							<li role="presentation"><a href="#features" role="tab" id="features-tab" data-toggle="tab" aria-controls="features">Características</a></li>
						</ul>
						{!! Form::open(['route'=> str_replace('create', 'store', Request::route()->getAction()['as']) , 'method'=>'POST', 'class'=>'form-horizontal', 'enctype'=>"multipart/form-data"]) !!}
						<div id="myTabContent" class="tab-content">
							<div role="tabpanel" class="tab-pane fade in active" id="home" aria-labelledBy="home-tab">
								@include('partials.messages')
								@include( str_replace('create', 'partials.fields', Request::route()->getAction()['as']) )
							</div>
							<div role="tabpanel" class="tab-pane fade" id="features" aria-labelledBy="features-tab">
								@include('sales.catalog_cars.partials.features')
							</div>
						</div>
						<div class="form-group">
							<div class="col-sm-offset-2 col-sm-10">
								<button type="submit" class="btn btn-primary">{{ config('options.' . Request::route()->getAction()['as'] .'.create') }}</button>
							</div>
						</div>
						{!! Form::close() !!}
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
@endsection

@section('scripts')

@include( str_replace('create', 'scripts', Request::route()->getAction()['as']) )

@endsection