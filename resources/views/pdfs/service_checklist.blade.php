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
			<div class="header-image"></div>
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
		<br><br><br><br><br><br>
		<table class="group">
			@foreach($groups as $group)
			<tr class="block">
				<td class="group-name div-left" width="10">
					<div class="div-rotate">{{ $group->name }}</div>
				</td>
				<td class="group-items div-right">
					@foreach($model->checkitems->where('checkitem_group_id', $group->id) as $checkitem)
					<div>{{ $checkitem->name }}</div>
					@endforeach
				</td>
			</tr>
			@endforeach
		</table>
	</div>
</body>
</html>