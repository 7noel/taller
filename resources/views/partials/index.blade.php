@extends('app')

@section('content')
<div class="container">
	<div class="row">
		<div class="col-md-10 col-md-offset-1">
			<div class="panel panel-default">
				<div class="panel-heading panel-heading-custom">{{ config('options.' . Request::route()->getAction()['as'] .'.panel') }}</div>
				@if(Session::has('message'))
					<p class="alert alert-success">{{ Session::get('message') }}</p>
				@endif
				<div class="panel-body">
					@include('partials.search')
					<p><a class="btn btn-info" href="{{ route( str_replace('index', 'create', Request::route()->getAction()['as']) ) }}" role="button"><span class="glyphicon glyphicon-plus" aria-hidden="true"></span> {{ config('options.' . Request::route()->getAction()['as'] .'.create') }}</a></p>
					@include( str_replace('index', 'partials.table', Request::route()->getAction()['as']) )
					{!! $models->appends(\Request::only(['name']))->render() !!}
				</div>
			</div>
		</div>
	</div>
</div>

{!! Form::open(['route'=>[str_replace('index', 'destroy', Request::route()->getAction()['as']), ':_ID'], 'method'=>'DELETE', 'id'=>'form-delete']) !!}
{!! Form::close() !!}

@endsection

@section('scripts')

{!! Html::script('js/admin.js') !!}

@endsection