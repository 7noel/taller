<!DOCTYPE html>
<html lang="es">
<head>
	<meta charset="UTF-8">
	<title>CHECKLIST</title>
	{!! Html::style('css/service_checklist.css') !!}
</head>
<body>
	<div class="checklist">
		<div class="header">
			<div class="header-image">
				<img src="./img/banner-express-service.png" alt="" height="40px">
			</div>
			<div class="header-title">CHECKLIST MULTI-PUNTO DE INSPECCIÓN VEHICULAR DE HONDA</div>
			<div class="header-data">
				<div>
					<span>Nombre:</span>
					<span>{{ $model->company_name }}</span>
				</div>
				<div>
					<span>Placa:</span>
					<span>{{ $model->plate }}</span>
					<span>Fecha:</span>
					<span>{{ $model->created_at }}</span>
				</div>
			</div>
		</div>
		<div class="group">
			@foreach($groups as $key => $group)
			@if($key==0)
			<div class="block block-{{ $key }}">
				<div class="group-name group-name-{{ $key }} inline-block">
					<div class="div-rotate">{{ $group->name }}</div>
				</div>
				<div class="group-items inline-block">
					@foreach($group->checkitems as $checkitem)
					<div style="background: gray;">{{ $checkitem->name }}</div>
					@endforeach
				</div>
			</div>
			@endif
			@endforeach
			@if(1==0)
			<div>
				<div class="div-left"></div>
				<div class="group-items div-right">
					<div>
						<div class="inline-block" width="300px" style="background: green;">
							<div>Observaciones</div>
							<span>Este es un comentario</span>
							<br>
							<label for="">Asesor: {{ $model->adviser }}</label><br>
							<label for="">Técnico: {{ $model->technician }}</label>
						</div>
						<div class="inline-block div-images" width="300px">
							<label>Daños o golpes debajo del auto</label><br><br>
							<img src="./img/banner-auto.png" alt="" height="80px"><br><br>
							<img src="./img/honda_masaki.jpg" alt="" height="20px">
						</div>
					</div>
				</div>
			</div>
			@endif
		</div>
	</div>
</body>
</html>