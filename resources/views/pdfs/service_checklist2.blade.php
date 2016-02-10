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
				<img src="/img/honda-logo.png" alt="" height="40px" class="img-padding-left">
				<img src="/img/LOGO_MASAKI.png" alt="" height="40px">
			</div>
			<div class="header-title">CHECKLIST MULTI-PUNTO DE INSPECCIÓN VEHICULAR DE HONDA</div>
			<div class="header-data">
				<div>
					<span class="header-data-label">Nombre:</span>
					<span>{{ $model->company_name }}</span>
				</div>
				<div>
					<span class="header-data-label">Placa:</span>
					<span>{{ $model->plate }}</span>
					<span class="header-data-label padding-left">Fecha:</span>
					<?php $fecha = \Carbon::createFromFormat("Y-m-d H:i:s", $model->created_at, 'America/Lima'); ?>
					<span>{{ $fecha->format('d/m/Y') }}</span>
				</div>
			</div>
		</div>
		<table class="group">
			<?php $with_two_values = array(1,2,3,4,16,48,49); ?>
			<?php $with_one_value = array(45,50,51); ?>
			<?php $with_value = array(38,41); ?>
			@foreach($groups as $key => $group)
			<tr class="blockd block-{{ $key }}">
				<td class="group-name group-name-{{ $key }}">
					<div class="div-rotate">{{ $group->name }}</div>
				</td>
				<td class="group-items red">
				@if($group->is_double_column)
					<?php $column=1; ?>
					<div class="div_column_one">
					@foreach($group->checkitems as $checkitem)
					<?php 
						if (isset($model)) { $cc = $checkitem->service_checklists()->where('service_checklist_id',$model->id)->first(); }
						if (isset($cc)) { $status = $cc->pivot->status; $value = $cc->pivot->value;
						} else { $status = ''; $value = ''; $column_two = ''; }
					 ?>
					@if($column==1 and $checkitem->column_two == '1')
					<?php $column=2; ?>
					</div>
					<div class="div_column_two">
					@endif
						<div class="div-checkitem">
							<div class="inline-block checkitem-name strike"><span>{!! $checkitem->name !!}</span></div>
							<div class="inline-block div-radios">
								@if($checkitem->with_status)
									@if( in_array($checkitem->id, $with_two_values) )
									@include( 'pdfs/checklist_partials/value02')
									@elseif( in_array($checkitem->id, $with_one_value) )
									@include( 'pdfs/checklist_partials/value03')
									@else
									@include( 'pdfs/checklist_partials/value01')
									@endif
								@endif
								@if($checkitem->with_value and in_array($checkitem->id, $with_value))
									<span>{{ $checkitem->pre_value." ".$value." ".$checkitem->post_value}}</span>
								@endif
							</div>
						</div>
					@endforeach
					</div>
				@else
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
								@if( in_array($checkitem->id, $with_two_values) )
								@include( 'pdfs/checklist_partials/value02')
								@elseif( in_array($checkitem->id, $with_one_value) )
								@include( 'pdfs/checklist_partials/value03')
								@else
								@include( 'pdfs/checklist_partials/value01')
								@endif
							@else
							@include( 'pdfs/checklist_partials/value04')
							@endif
						</div>
					</div>
					@endforeach
				@endif
				</td>
			</tr>
			@endforeach
			<tr>
				<td class="group-name"> </td>
				<td class="group-items">
						<div class="inline-block div-options">
							<div>Observaciones</div>
							<div>
								{{ $model->observation }}
							</div>
							<br>
							<label for="">Asesor: {{ $model->adviser->full_name }}</label><br>
							<div></div>
						</div>
				</td>
			</tr>
		</table>
	</div>
</body>
</html>