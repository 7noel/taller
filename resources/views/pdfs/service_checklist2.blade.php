<!DOCTYPE html>
<html lang="es">
<head>
	<meta charset="UTF-8">
	<title>CHECKLIST</title>
	{!! Html::style('css/service_checklist2.css') !!}
</head>
<body>
	<div class="checklist">
		<div class="header">
			<div class="header-image">
				<img src="/img/banner-express-service.png" alt="" height="40px">
			</div>
			<div class="header-title">CHECKLIST MULTI-PUNTO DE INSPECCIÓN VEHICULAR DE HONDA</div>
			<div class="header-data">
				<div>
					<span>Nombre:</span>
					<input type="radio" value="p">
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
		<table class="group">
			@foreach($groups as $key => $group)
			<tr class="blockd block-{{ $key }}">
				<td class="group-name group-name-{{ $key }}">
					<div class="div-rotate">{{ $group->name }}</div>
				</td>
				<td class="group-items red">
					@foreach($group->checkitems as $checkitem)
					<?php 
						if (isset($model)) { $cc = $checkitem->service_checklists()->where('service_checklist_id',$model->id)->first(); }
						if (isset($cc)) { $status = $cc->pivot->status; $value = $cc->pivot->value;
						} else { $status = ''; $value = ''; }
					 ?>
					<div class="div-checkitem">
						<div class="inline-block checkitem-name strike"><span>{!! $checkitem->name !!}</span></div>
						<div class="inline-block div-radios">
							@if($checkitem->with_status)
								@if($status == 'success')

								<input type="radio" name="checkitems[{{ $checkitem->id }}][status]" value="success" class="checkitem-success radio-success" checked>
								<input type="radio" name="checkitems[{{ $checkitem->id }}][status]" value="warning" class="checkitem-warning radio-warning">
								<input type="radio" name="checkitems[{{ $checkitem->id }}][status]" value="danger" class="checkitem-danger radio-danger">

								@elseif($status == 'warning')

								<input type="radio" name="checkitems[{{ $checkitem->id }}][status]" value="success" class="checkitem-success radio-success">
								<input type="radio" name="checkitems[{{ $checkitem->id }}][status]" value="warning" class="checkitem-warning radio-warning" checked>
								<input type="radio" name="checkitems[{{ $checkitem->id }}][status]" value="danger" class="checkitem-danger radio-danger">

								@elseif($status == 'danger')

								<input type="radio" name="checkitems[{{ $checkitem->id }}][status]" value="success" class="checkitem-success radio-success">
								<input type="radio" name="checkitems[{{ $checkitem->id }}][status]" value="warning" class="checkitem-warning radio-warning">
								<input type="radio" name="checkitems[{{ $checkitem->id }}][status]" value="danger" class="checkitem-danger radio-danger" checked>

								@endif
							@endif
						</div>
					</div>
					@endforeach
				</td>
			</tr>
			@endforeach
			<tr>
				<td class="group-name"> </td>
				<td class="group-items">
						<div class="inline-block div-options">
							<div>Observaciones</div>
							<div>
								Este es un comentario de prueba para el siste de Hoja Semaforo
							</div>
							<br>
							<label for="">Asesor: {{ $model->adviser }}</label><br>
							<label for="">Técnico: {{ $model->technician }}</label>
							<div></div>
						</div>
						<div class="inline-block div-images">
							<label>Daños o golpes debajo del auto</label><br><br>
							<img src="/img/banner-auto.png" alt="" height="80px"><br><br>
							<img src="/img/honda_masaki.jpg" alt="" height="20px">
						</div>
				</td>
			</tr>
		</table>
	</div>
</body>
</html>