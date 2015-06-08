@extends('app')

@section('content')
<div class="container">

	<div class="row">
		<div class="col-md-10 col-md-offset-1">
			<div class="panel panel-default">
				<div class="panel-heading panel-heading-custom">{{ config('options.' . Request::route()->getAction()['as'] .'.panel') . $model->name }}</div>

				<div class="panel-body">
					@include('partials.messages')
					{!! Form::model($model, ['route' => str_replace('show', 'index', Request::route()->getAction()['as']), 'class'=>'form-horizontal']) !!}
					
					@include( str_replace('show', 'partials.fields', Request::route()->getAction()['as']) )
					<div class="form-group">
						<div class="col-sm-offset-2 col-sm-10">
							<a href="{{ URL::previous() }}" class="btn btn-primary">Regresar</a>
						</div>
					</div>
					{!! Form::close() !!}
				</div>
			</div>
		</div>
	</div>
</div>
@endsection

@section('scripts')



@endsection