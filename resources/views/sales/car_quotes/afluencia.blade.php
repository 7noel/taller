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
					<p><a class="btn btn-info" href="#" role="button"><span class="glyphicon glyphicon-plus" aria-hidden="true"></span> crear</a></p>
					<table class="table table-hover table-condensed">
						<tr>
							<th>#</th>
							<td>Fecha</td>
							<th>Vehicule</th>
							<th>Precio</th>
							<th>Acciones</th>
						</tr>
						<tr data-id="">
							<td>204</td>
							<td>01/12/2015</td>
							<td>CIVIC LX AT</td>
							<td>27000.00</td>
							<td>
								<a href="{{ route( 'sales.car_quotes.edit', 43 ) }}" class="btn btn-success btn-xs"><span class="glyphicon glyphicon-shopping-cart" aria-hidden="true"></span> Cotizar</a>
								<a href="{{ route('sales.afluencia.create') }}" class="btn btn-primary btn-xs"><span class="glyphicon glyphicon-pencil" aria-hidden="true"></span> Editar</a>
								<a href="#" class="btn-delete btn btn-danger btn-xs"><span class="glyphicon glyphicon-trash" aria-hidden="true"></span> Eliminar</a>
							</td>
						</tr>
						<tr data-id="">
							<td>142</td>
							<td>18/12/2014</td>
							<td>CIVIC SEDAN</td>
							<td>27000.00</td>
							<td>
								<a href="{{ route( 'sales.car_quotes.edit', 43 ) }}" class="btn btn-success btn-xs"><span class="glyphicon glyphicon-shopping-cart" aria-hidden="true"></span> Cotizar</a>
								<a href="{{ route('sales.afluencia.create') }}" class="btn btn-primary btn-xs"><span class="glyphicon glyphicon-pencil" aria-hidden="true"></span> Editar</a>
								<a href="#" class="btn-delete btn btn-danger btn-xs"><span class="glyphicon glyphicon-trash" aria-hidden="true"></span> Eliminar</a>
							</td>
						</tr>
						<tr data-id="">
							<td>102</td>
							<td>20/05/2014</td>
							<td>CIVIC SEDAN</td>
							<td>27000.00</td>
							<td>
								<a href="{{ route( 'sales.car_quotes.edit', 43 ) }}" class="btn btn-success btn-xs"><span class="glyphicon glyphicon-shopping-cart" aria-hidden="true"></span> Cotizar</a>
								<a href="{{ route('sales.afluencia.create') }}" class="btn btn-primary btn-xs"><span class="glyphicon glyphicon-pencil" aria-hidden="true"></span> Editar</a>
								<a href="#" class="btn-delete btn btn-danger btn-xs"><span class="glyphicon glyphicon-trash" aria-hidden="true"></span> Eliminar</a>
							</td>
						</tr>
						<tr data-id="">
							<td>12</td>
							<td>11/10/2013</td>
							<td>CIVIC SEDAN</td>
							<td>27000.00</td>
							<td>
								<a href="{{ route( 'sales.car_quotes.edit', 43 ) }}" class="btn btn-success btn-xs"><span class="glyphicon glyphicon-shopping-cart" aria-hidden="true"></span> Cotizar</a>
								<a href="{{ route('sales.afluencia.create') }}" class="btn btn-primary btn-xs"><span class="glyphicon glyphicon-pencil" aria-hidden="true"></span> Editar</a>
								<a href="#" class="btn-delete btn btn-danger btn-xs"><span class="glyphicon glyphicon-trash" aria-hidden="true"></span> Eliminar</a>
							</td>
						</tr>
					</table>
				</div>
			</div>
		</div>
	</div>
</div>


@endsection

@section('scripts')

@endsection