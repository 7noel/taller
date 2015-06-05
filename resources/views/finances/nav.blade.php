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
							<li><a href="{{ route('admin.document_types.index') }}">Documentos</a></li>
							<li><a href="{{ route('finances.payment_conditions.index') }}">Condiciones de Pago</a></li>
						</ul>
					</li>
