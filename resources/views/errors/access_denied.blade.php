@extends('app')

@section('content')
<div class="container">
	<div class="jumbotron">
		<h1>ACCESO DENEGADO</h1>
		<p>Si necesita tener acceso al enlace seleccionado debe comunicarse con el administrador del sistema</p>
		<p>
			<a class="btn btn-lg btn-primary" href="{{ URL::previous() }}" role="button">Volver &raquo;</a>
		</p>
	</div>
</div>


@endsection