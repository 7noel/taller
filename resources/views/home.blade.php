@extends('app')

@section('content')
<div class="container">
	<div class="row">
		<div class="col-md-10 col-md-offset-1">
			<div class="panel panel-default">
				<div class="panel-heading panel-heading-custom">Home</div>

				<div class="panel-body">
					<div class="row">
						<div class="col-xs-6 col-md-3">
							<a href="{{ route('finances.companies.index') }}" class="thumbnail">EMPRESAS
								<img src="/img/companies.png" alt="...">
							</a>
						</div>
						<div class="col-xs-6 col-md-3">
							<a href="#" class="thumbnail">VENTAS
								<img src="/img/sale.jpg" alt="...">
							</a>
						</div>
						<div class="col-xs-6 col-md-3">
							<a href="{{ route('logistics.purchases.index') }}" class="thumbnail">COMPRAS
								<img src="/img/buy.jpg" alt="...">
							</a>
						</div>
						<div class="col-xs-6 col-md-3">
							<a href="#" class="thumbnail">POSTVENTA
								<img src="/img/service.jpg" alt="...">
							</a>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
@endsection
