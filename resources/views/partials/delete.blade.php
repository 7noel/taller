{!! Form::open(['route'=>[ str_replace('edit', 'destroy', Request::route()->getAction()['as']) , $model], 'method'=>'DELETE']) !!}
	<button type="submit" class="btn btn-danger delete"><span class="glyphicon glyphicon-trash" aria-hidden="true"></span> {{ config('options.' . Request::route()->getAction()['as'] .'.delete') }}</button>
{!! Form::close() !!}
