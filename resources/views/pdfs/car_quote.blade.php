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
			</div>
			<div class="date">
				<?php 
				setlocale(LC_TIME, 'Spanish');
				$dt=\Carbon::now();
				 ?>
				Lima, {{ $dt->formatLocalized('%A %d de %B de %Y') }}
			</div>
		</div>
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
				<p>{{ $group->name }}</p>
					@foreach($quote->catalog_car->features->where('feature_group_id', $group->id) as $feature)
					{{ $feature->name }} <br>
					@endforeach
				@endforeach
			</div>
			<div class="features-1-right">
				@foreach($groups->where('template', 'primaryRight') as $group)
				<p>{{ $group->name }}</p>
					@foreach($quote->catalog_car->features->where('feature_group_id', $group->id) as $feature)
					{{ $feature->name }} <br>
					@endforeach
				@endforeach
			</div>
		</div>
		<div class="options">
			<div class="cortesias">
				<p><span>CORTESÍAS:</span></p>
				<ul>
					<li>Trámite de Placas físicas, tarjeta de Propiedad e Inscripción Municipal SAT</li>
					<li>Kit. de Seguridad ( Seguro de Emblemas y seguro de plumillas )</li>
					<li>Servicio de Mantenimiento gratuito en 1,000 Kms.</li>
				</ul>
			</div>
			<div class="incluye">
				<p><span>INCLUYE:</span> Alarma de fabrica y pisos alfombrados originales.</p>
			</div>
		</div>
		<div class="price">
			<div class="price">
				<p><span>PRECIO DE LISTA:</span> {{ $quote->currency->symbol.' '.$quote->price }}</p>
			</div>
			<div class="price-set">
				<p><span>PRECIO ESPECIAL:</span> {{ $quote->currency->symbol.' '.$quote->set_price }} <span>> Precio incluye I.G.V.</span></p>
			</div>
		</div>
		<div class="colors"></div>
	</div>

</body>
</html>