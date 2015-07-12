<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<title>Document</title>
	{!! Html::style('css/car_quote.css') !!}
</head>
<body>
	<div class="quote">
		<div class="header-pdf">
			<div class="logo">
				<img src="{{ './img/honda_masaki.jpg' }}" alt="">
				<br><br>
				<p><strong>Masaki S.A.C.</strong></p>
				<p>Av. Javier Prado Este 5446</p>
				<p>Urb. Camacho - La Molina</p>
				<p>Telf.: 612-7500</p>
				<p>www.masaki.com.pe</p>
			</div>
			<div class="date">
				<?php 
				setlocale(LC_TIME, 'spanish');
				$dt=\Carbon::now();
				 ?>
				Lima, {{ $dt->formatLocalized('%A %d de %B de %Y') }}
			</div>
		</div>
		<br>
		<div class="greeting">
			Señor (es): <br>
			<span>{{ $quote->company->company_name }}</span> <br>
			<span>Atención</span> <br>
			<span>{{ $quote->attention }}</span> <br>
			<p>De nuestra consideración:</p>
											
			<p>Nos es grato saludarle en atención a su interés por la marca y presentarnos a nombre de Masaki S.A.C. concesionario autorizado de HONDA DEL PERÚ, para lo cual hacemos presente las características técnicas  del modelo solicitado:</p>
		</div>
		<div class="body">
			<div class="image-car">
				<div class="modelo"><img src="{{ './storage/img/'.$quote->catalog_car->version->modelo->image }}" alt="" height="40px"></div>
				<div class="inline label_version">VERSIÓN : </div>
				<div class="inline version">{{ $quote->catalog_car->version->name }}</div>
				<br>
				@if($quote->catalog_car->image2 == '')
				<div class="div-center div-img">
					<img src="{{ './storage/img/'.$quote->catalog_car->image1 }}" alt="">
				</div>

				@else
				<div class="div-left div-img">
					<img src="{{ './storage/img/'.$quote->catalog_car->image1 }}" alt="">
				</div>
				<div class="div-right div-img">
					<img src="{{ './storage/img/'.$quote->catalog_car->image2 }}" alt="">
				</div>
				@endif
				<div class="image-car-obs center">Fotos Referenciales</div>
			</div>
			<br>
			<div class="features-primary">
				<div class="div-left">
					<strong>AÑO DE FAB.</strong>{{ $quote->catalog_car->manufacture_year }}<br>
					<strong>AÑO DE MOD.</strong>{{ $quote->catalog_car->model_year }}<br>
					<strong>COMBUSTIBLE</strong>{{ $quote->catalog_car->fuel }}<br>
				</div>
				<div class="div-right">
					<strong>CILINDRADA</strong>{{ $quote->catalog_car->cylinder }}<br>
					<strong>TRANSMISIÓN</strong>{{ $quote->catalog_car->transmission }}<br>
					<strong>ASIENTOS</strong>{{ $quote->catalog_car->seats }}<br>
				</div>
			</div>
		</div>
		<br>
		<div class="features-1">
			<div class="div-left">
				@foreach($groups->where('template', 'primaryLeft') as $group)
				<p class="feature-subtitle">{{ $group->name }}</p>
				<table>
					@foreach($quote->catalog_car->features->where('feature_group_id', $group->id) as $feature)
					<tr>
						<td  class="name">{{ $feature->name }}</td>
						<td class="value">{{ (($feature->name=="") ? '' : ':').$feature->value }}</td>
					</tr>
					@endforeach
				</table>
				@endforeach
			</div>
			<div class="div-right">
				@foreach($groups->where('template', 'primaryRight') as $group)
				<p class="feature-subtitle">{{ $group->name }}</p>
				<table>
					@foreach($quote->catalog_car->features->where('feature_group_id', $group->id) as $feature)
					<tr>
						<td  class="name">{{ $feature->name }}</td>
						<td class="value">{{ (($feature->name=="") ? '' : ':').$feature->value }}</td>
					</tr>
					@endforeach
				</table>
				@endforeach
			</div>
		</div>
		<div class="price">
			<br>
			<div class="price-set">
				<span clas="price-label">VALOR DE LA PROPUESTA ECONÓMICA:</span><div class='price-value center'>{{ $quote->currency->symbol.' '.number_format($quote->set_price, 2, '.', ',') }} </div>
			</div>
			<div class="price-conditions">
				<ul>
					<li>Monto Expresado en Dólares Americanos. El precio Incluye I.G.V.</li>
					<li>No Incluye costo de flete de Lima a Provincias</li>
					<li>Sujeto a variación de modificación tributaria o arancelaria</li>
				</ul>
			</div>
		</div>

		<div style='page-break-after: always;'></div>
		
		<div class="features-2">
			<div class="div-left">
				@foreach($groups->where('template', 'in') as $group)
				<p class="feature-subtitle">{{ $group->name }}</p>
					@foreach($quote->catalog_car->features->where('feature_group_id', $group->id) as $feature)
					{!! $feature->name !!} <br>
					@endforeach
				@endforeach
			</div>
			<div class="div-right">
				@foreach($groups->where('template', 'out') as $group)
				<p class="feature-subtitle">{{ $group->name }}</p>
					@foreach($quote->catalog_car->features->where('feature_group_id', $group->id) as $feature)
					{!! $feature->name !!} <br>
					@endforeach
				@endforeach
			</div>
		</div>
		<div class="image-features">
			<div class="div-left div-img">
				<img src="{{ './storage/img/'.$quote->catalog_car->image3 }}" alt="">
				<p>{{ $quote->catalog_car->description_image3 }}</p>
			</div>
			<div class="div-right div-img">
				<img src="{{ './storage/img/'.$quote->catalog_car->image4 }}" alt="">
				<p>{{ $quote->catalog_car->description_image4 }}</p>
			</div>
		</div>
		<div class="conditions-others">
			<table>
				<tr>
					<td class="td-1">Garantía de Fábrica</td>
					<td class="td-2">: 3 años ó 100,000 kms. (Lo que ocurra primero)</td>
				</tr>
				<tr>
					<td class="td-1">Condiciones de Venta</td>
					<td class="td-2">: Al contado a través del sistema financiero.</td>
				</tr>
				<tr>
					<td class="td-1">Tiempo de Entrega</td>
					<td class="td-2">: 15 días útiles, contra cancelación de la unidad.</td>
				</tr>
				<tr>
					<td class="td-1">Trámites</td>
					<td class="td-2">: Tarjeta de propiedad, Placas de rodaje, Declaración Municipal SAT.</td>
				</tr>
				<tr>
					<td class="td-1">Kit de Seguridad</td>
					<td class="td-2">: Alarma Original, Seguro de aros, Seguro de plumillas, Seguro de emblemas, Jgo. Pisos Originales Alfombrados.</td>
				</tr>
			</table>
		</div>
		</div>
		<div class="cuentas div-left">
			<p><strong>CUENTAS BANCARIAS</strong></p>
			<table class="tbl-cuentas">
				<tr>
					<td class="td-1">BANCO CONTINENTAL</td>
					<td class="td-2">0011 910 010011603879</td>
				</tr>
				<tr>
					<td class="td-1">BANCO DE CRÉDITO</td>
					<td class="td-2">193-1624871-1-47</td>
				</tr>
				<tr>
					<td class="td-1">BANCO SCOTIABANK</td>
					<td class="td-2">000-3142887</td>
				</tr>
				<tr>
					<td class="td-1">BANCO INTERBANK</td>
					<td class="td-2">045-3000-488783</td>
				</tr>
			</table>
		</div>
		<div class="div-right">
			<br>
			<p><strong>Seguro Vehicular:</strong></p>
			<p>Cotice sin compromiso o solicite a su Asesor Comercial</p>
			<p><strong>RIMAC - PACIFICO - MAPFRE - MAGALLANES</strong></p>
		</div>
		<br>
		<div>
			<p>Sin otro particular quedamos a la espera de su órden.</p>
			<div>Atentamente,</div>
		</div>
		<div class="div-firma center">
			<div class="firma">
				<img src="{{ './storage/img/'.$quote->employee->signature }}" alt="">
			</div>
			<p class="firma-name">{{ $quote->employee->full_name }}</p>
			<p>Asesor Comercial HONDA</p>
			<p>{{ 'CELULAR: '.$quote->employee->mobile_company }}</p>
			<p>{{ 'OFICINA: '.$quote->employee->phone_company }}</p>
			<p>{{ 'EMAIL: '.$quote->employee->email_company }}</p>
		</div>
	</div>

</body>
</html>