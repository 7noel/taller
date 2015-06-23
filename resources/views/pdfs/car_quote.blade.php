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
				<img src="{{ './img/honda_masaki.jpg' }}" alt="" width="200px">
				<br><br>
				<p><strong>Masaki S.A.C.</strong></p>
				<p>Av. Javier Prado Este 5446</p>
				<p>Urb. Camacho - La Molina</p>
				<p>Telf.: 612-7500</p>
			</div>
			<div class="date">
				<?php 
				setlocale(LC_TIME, 'Spanish');
				$dt=\Carbon::now();
				 ?>
				Lima, {{ $dt->formatLocalized('%A %d de %B de %Y') }}
			</div>
		</div>
		<br>
		<div class="greeting">
			Señor (es): <br>
			<strong>SERGIO LUIS DA COSTA BURGA</strong> <br>
			<strong>Att.-</strong> <br>
			Estimado (s) señor (es) : <br>
											
			<p>Nos es muy grato saludarle y a la vez presentarnos como <strong>MASAKI SAC</strong>, concesionario autorizado <span>HONDA</span>. Asimismo, deseamos hacerle extensiva las características técnicas y mejor oferta de nuestro vehiculo: </p>
		</div>
		<div class="body">
			<div class="features-primary">
				<br><br>
				<strong>MARCA</strong>{{ $quote->catalog_car->version->modelo->brand->name }}<br>
				<strong>MODELO</strong>{{ $quote->catalog_car->version->modelo->name.' '.$quote->catalog_car->version->name }}<br>
				<strong>CILINDRADA</strong>{{ $quote->catalog_car->cylinder }}<br>
				<strong>TRANSMISIÓN</strong>{{ $quote->catalog_car->transmission }}<br>
				<strong>ASIENTOS</strong>{{ $quote->catalog_car->seats }}<br>
				<strong>COMBUSTIBLE</strong>{{ $quote->catalog_car->fuel }}<br>
				<strong>AÑO DE FAB.</strong>{{ $quote->catalog_car->manufacture_year }}<br>
				<strong>AÑO DE MOD.</strong>{{ $quote->catalog_car->model_year }}<br>

			</div>
			<div class="image-car">
				<img src="{{ './storage/img/'.$quote->catalog_car->image }}" alt="">
			</div>
		</div>
		<br><br><br><br><br>
		<div class="features-1">
			<div class="features-1-left">
				@foreach($groups->where('template', 'primaryLeft') as $group)
				<p class="red-underline feature-subtitle">{{ $group->name }}</p>
				<table>
					
				</table>
					@foreach($quote->catalog_car->features->where('feature_group_id', $group->id) as $feature)
					<span class="name">{{ $feature->name }}</span> <span class="value">{{ $feature->value }}</span><br>
					@endforeach
				@endforeach
			</div>
			<div class="features-1-right">
				@foreach($groups->where('template', 'primaryRight') as $group)
				<p class="red-underline feature-subtitle">{{ $group->name }}</p>
					@foreach($quote->catalog_car->features->where('feature_group_id', $group->id) as $feature)
					<span class="name">{{ $feature->name }}</span> <span class="value">{{ $feature->value }}</span><br>
					@endforeach
				@endforeach
			</div>
		</div>
		<div class="options">
			<div class="cortesias">
				<p><span class="red-underline">CORTESÍAS:</span></p>
				<ul>
					<li>Trámite de Placas físicas, tarjeta de Propiedad e Inscripción Municipal SAT</li>
					<li>Kit. de Seguridad ( Seguro de Emblemas y seguro de plumillas )</li>
					<li>Servicio de Mantenimiento gratuito en 1,000 Kms.</li>
				</ul>
			</div>
			<div class="incluye">
				<p><span class="red-underline">INCLUYE:</span> Alarma de fabrica y pisos alfombrados originales.</p>
			</div>
		</div>
		<div class="price">
			<div class="price">
				<p><span class="red-underline">PRECIO DE LISTA:</span> {{ $quote->currency->symbol.' '.$quote->price }}</p>
			</div>
			<div class="price-set">
				<p><span>PRECIO ESPECIAL:</span> {{ $quote->currency->symbol.' '.$quote->set_price }} <span>> Precio incluye I.G.V.</span></p>
			</div>
			<div>
				<p>Tipo de cambio preferencial <span>3.15</span></p>
				<p>PRECIO EN SOLES <span></span></p>
			</div>
		</div>
		<div class="colors">
			<p>COLOR DISPONIBLE: <span> Plata Alabastro, Gris Smoky, Gris Steel, Rojo Cherry.</span></p>
		</div>
		<div style='page-break-after: always;'></div>
		<div class="header-pdf-2">
			<img src="{{ './img/header_quote.png' }}" alt="">
		</div>
		<div class="features-2">
			<div class="features-2-left">
				<p class="blue-underline">EQUIPAMIENTO INTERIOR:</p>
				@foreach($groups->where('template', 'in') as $group)
				<p class="red-underline feature-subtitle">{{ $group->name }}</p>
					@foreach($quote->catalog_car->features->where('feature_group_id', $group->id) as $feature)
					{{ $feature->name }} <br>
					@endforeach
				@endforeach
			</div>
			<div class="features-2-right">
				<p class="blue-underline">EQUIPAMIENTO EXTERIOR:</p>
				@foreach($groups->where('template', 'out') as $group)
				<p class="red-underline feature-subtitle">{{ $group->name }}</p>
					@foreach($quote->catalog_car->features->where('feature_group_id', $group->id) as $feature)
					{{ $feature->name }} <br>
					@endforeach
				@endforeach
			</div>
		</div>
		<div class="cuentas">
			<p>CUENTAS BANCARIAS MASAKI SAC</p>
			<table class="border">
				<tr class="center">
					<td colspan="2" class="red-italic">CTA. CORRIENTE DÓLARES</td>
				</tr>
				<tr>
					<td class="td-bank">BANCO CONTINENTAL</td>
					<td class="td-cuenta">0011 910 010011603879</td>
				</tr>
				<tr>
					<td class="td-bank">BANCO DE CRÉDITO</td>
					<td class="td-cuenta">193-1624871-1-47</td>
				</tr>
				<tr>
					<td class="td-bank">BANCO SCOTIABANK</td>
					<td class="td-cuenta">000-3142887</td>
				</tr>
				<tr>
					<td class="td-bank">BANCO INTERBANK</td>
					<td class="td-cuenta">045-3000-488783</td>
				</tr>
				<tr>
					<td class="td-bank">BANCO HSBC</td>
					<td class="td-cuenta">104532001</td>
				</tr>
			</table>
		</div>
		<br>
		<div class="conditions">
			<table>
				<tr>
					<td class="conditions-name">VALIDEZ DE LA OFERTA :</td>
					<td>Según disponibilidad de stock</td>
				</tr>
				<tr>
					<td class="conditions-name">PLAZO DE ENTREGA :</td>
					<td>Con tarjeta de Propiedad, 15 días útiles de la fecha de cancelación. <br><strong>sujeto a stock</strong></td>
				</tr>
				<tr>
					<td class="conditions-name">GARANTÍA:</td>
					<td>3 años ó 100.000 kilómetros (lo primero que ocurra)</td>
				</tr>
			</table>
		</div>
		<br>
		<div>
			<p>A la espera de sus prontas y gratas noticias quedamos de Uds.</p>
			<p>Atentamente,</p>
		</div>
	</div>

</body>
</html>