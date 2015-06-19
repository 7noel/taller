@extends('app')

@section('content')
<div class="container">

	<div class="row">
		<div class="col-md-10 col-md-offset-1">
			<div class="panel panel-default">
				<div class="panel-heading panel-heading-custom">{{ config('options.' . Request::route()->getAction()['as'] .'.panel') . $model->name }}</div>
					<div class="panel-body">
						<ul id="myTab" class="nav nav-tabs" role="tablist">
							<li role="presentation" class="active"><a href="#home" id="home-tab" role="tab" data-toggle="tab" aria-controls="home" aria-expanded="true">Compra</a></li>
							<li role="presentation"><a href="#details" role="tab" id="details-tab" data-toggle="tab" aria-controls="details">Detalle</a></li>
						</ul>
						{!! Form::model($model, ['route'=>[ str_replace('edit', 'update', Request::route()->getAction()['as']) , $model], 'method'=>'PUT', 'class'=>'form-horizontal']) !!}
						<div id="myTabContent" class="tab-content">
							<div role="tabpanel" class="tab-pane fade in active" id="home" aria-labelledBy="home-tab">
								@include('partials.messages')
								@include( str_replace('edit', 'partials.fields', Request::route()->getAction()['as']) )
							</div>
							<div role="tabpanel" class="tab-pane fade" id="details" aria-labelledBy="details-tab">
								@include('logistics.purchases.partials.details')
							</div>
						</div>
						<div class="form-group">
							<div class="col-sm-offset-2 col-sm-10">
								<button type="submit" class="btn btn-primary">{{ config('options.' . Request::route()->getAction()['as'] .'.update') }}</button>
							</div>
						</div>
						{!! Form::close() !!}
					</div>
				</div>
			</div>
			@include('partials.delete')
		</div>
	</div>
</div>
@endsection

@section('scripts')

@include( str_replace('edit', 'scripts', Request::route()->getAction()['as']) )

@endsection