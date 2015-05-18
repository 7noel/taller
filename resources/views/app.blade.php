<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<title>Masaki</title>
	{!! Html::style('css/app.css') !!}

	<!-- Fonts -->
	<link href='//fonts.googleapis.com/css?family=Roboto:400,300' rel='stylesheet' type='text/css'>
	<link href='//cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.4.0/css/bootstrap-datepicker.min.css' rel='stylesheet' type='text/css'>

	<!-- HTML5 shim and Respond.js for IE8 support of HTML5 elements and media queries -->
	<!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
	<!--[if lt IE 9]>
		<script src="https://oss.maxcdn.com/html5shiv/3.7.2/html5shiv.min.js"></script>
		<script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
	<![endif]-->
</head>
<body>
	<nav class="navbar navbar-inverse">
		<div class="container-fluid">
			<div class="navbar-header">
				<button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#bs-example-navbar-collapse-1">
					<span class="sr-only">Toggle Navigation</span>
					<span class="icon-bar"></span>
					<span class="icon-bar"></span>
					<span class="icon-bar"></span>
				</button>
				<a class="navbar-brand" href="{{ url('/') }}">Masaki</a>
			</div>

			<div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1">
				@if( !Auth::guest() )
				<ul class="nav navbar-nav">
					<li class="dropdown">
						<a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-expanded="false">Seguridad<span class="caret"></span></a>
						<ul class="dropdown-menu" role="menu">
							<li><a href="{{ route('guard.users.index') }}">Unuarios</a></li>
							<li class="divider"></li>
							<li><a href="{{ route('guard.roles.index') }}">Roles</a></li>
							<li><a href="{{ route('guard.permission_groups.index') }}">Grupos</a></li>
							<li><a href="{{ route('guard.permissions.index') }}">Permisos</a></li>
						</ul>
					</li>
					<li class="dropdown">
						<a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-expanded="false">Almacén<span class="caret"></span></a>
						<ul class="dropdown-menu" role="menu">
							<li><a href="{{ route('storage.warehouses.index') }}">Almacenes</a></li>
							<li><a href="{{ route('storage.products.index') }}">Productos</a></li>
							<li><a href="{{ route('storage.products.index') }}">Movimientos</a></li>
							<li class="divider"></li>
							<li><a href="{{ route('storage.categories.index') }}">Categoías</a></li>
							<li><a href="{{ route('storage.sub_categories.index') }}">Sub Categorías</a></li>
							<li><a href="{{ route('admin.unit_types.index') }}">Tipos de Unidad</a></li>
							<li><a href="{{ route('storage.units.index') }}">Unidades</a></li>
						</ul>
					</li>
					<li class="dropdown">
						<a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-expanded="false">Ventas<span class="caret"></span></a>
						<ul class="dropdown-menu" role="menu">
							<li><a href="#">Notas de Pedido de Vehículo</a></li>
							<li><a href="#">Notas de Pedido de Accesorios</a></li>
							<li><a href="#">Facturación</a></li>
							<li><a href="#">Ordenes de Compra</a></li>
							<li class="divider"></li>
							<li><a href="#">Marcas</a></li>
							<li><a href="#">Modelos</a></li>
							<li><a href="#">Versiones</a></li>
							<li><a href="#">Lista de Vehículos</a></li>
							<li><a href="#">Colores Interiores</a></li>
							<li><a href="#">Colores Exteriores</a></li>
						</ul>
					</li>
					<li class="dropdown">
						<a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-expanded="false">PostVenta<span class="caret"></span></a>
						<ul class="dropdown-menu" role="menu">
							<li><a href="#">Ordenes de Trabajo</a></li>
							<li><a href="#">Facturación</a></li>
							<li><a href="#">Ordenes de Servicio</a></li>
							<li class="divider"></li>
							<li><a href="#">Tipos de Ordenes</a></li>
						</ul>
					</li>
					<li class="dropdown">
						<a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-expanded="false">Finanzas<span class="caret"></span></a>
						<ul class="dropdown-menu" role="menu">
							<li><a href="#">Cuentas por Cobrar</a></li>
							<li><a href="#">Cuentas por Pagar</a></li>
							<li><a href="#">Facturación Total</a></li>
							<li class="divider"></li>
							<li><a href="{{ route('admin.currencies.index') }}">Monedas</a></li>
							<li><a href="{{ route('finances.exchanges.index') }}">Tipo de Cambio</a></li>
							<li><a href="{{ route('finances.companies.index') }}">Empresas</a></li>
						</ul>
					</li>
					<li class="dropdown">
						<a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-expanded="false">Recursos Humanos<span class="caret"></span></a>
						<ul class="dropdown-menu" role="menu">
							<li class="divider"></li>
							<li><a href="{{ route('humanresources.employees.index') }}">Empleados</a></li>
							<li><a href="#">Planilla</a></li>
							<li><a href="{{ route('admin.id_types.index') }}">Documentos</a></li>
						</ul>
					</li>
					<li class="dropdown">
						<a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-expanded="false">Logistica<span class="caret"></span></a>
						<ul class="dropdown-menu" role="menu">
							<li><a href="#">Ordenes de Compra</a></li>
							<li><a href="#">Compras</a></li>
							<li><a href="#">algo</a></li>
							<li class="divider"></li>
							<li><a href="#">Separated link</a></li>
							<li class="divider"></li>
							<li><a href="#">One more separated link</a></li>
						</ul>
					</li>
				</ul>
				@endif

				<ul class="nav navbar-nav navbar-right">
					@if (Auth::guest())
						<li><a href="{{ url('/auth/login') }}">Login</a></li>
						<li><a href="{{ url('/auth/register') }}">Register</a></li>
					@else
						<li class="dropdown">
							<a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-expanded="false">{{ Auth::user()->name }} <span class="caret"></span></a>
							<ul class="dropdown-menu" role="menu">
								<li><a href="{{ url('/guard/change_password') }}">Cambiar Contraseña</a></li>
								<li class="divider"></li>
								<li><a href="{{ url('/auth/logout') }}">Salir</a></li>
							</ul>
						</li>
					@endif
				</ul>
			</div>
		</div>
	</nav>
	@yield('content')
	<!-- Scripts -->
	<script src="//cdnjs.cloudflare.com/ajax/libs/jquery/2.1.3/jquery.min.js"></script>
	<script src="//cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/3.3.1/js/bootstrap.min.js"></script>
	<script src="//cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.4.0/js/bootstrap-datepicker.min.js"></script>
	{!! Html::script('js/admin.js') !!}
	{!! Html::script('js/delete.js') !!}
	{!! Html::script('js/date.js') !!}
	{!! Html::script('js/modal.js') !!}
	@yield('scripts')
</body>
</html>
