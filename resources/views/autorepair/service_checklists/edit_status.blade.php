@extends('app')

@section('content')
<div class="container">

	<div class="row">
		<div class="col-md-8">
			<div class="panel panel-default">
				<div class="panel-heading panel-heading-custom">Cambiar Estado De La Hoja De Servicio Nro: {{ $model->id}} </div>

				<div class="panel-body">
					<div class="form-horizontal">
						<div class="form-group">
							<label class='col-sm-2 control-label'>Placa:</label>
							<div class="col-sm-6">
								<p class="form-control-static"> {{$model->plate}} </p>
							</div>
						</div>
						<div class="form-group">
							<label class="col-sm-2 control-label">Cliente:</label>
							<div class="col-sm-6">
								<p class="form-control-static"> {{$model->company_name}} </p>
							</div>
						</div>
						<div class="form-group">
							<label class="col-sm-2 control-label">Orden T:</label>
							<div class="col-sm-6">
								<p class="form-control-static"> {{$model->order_id}} </p>
							</div>
						</div>
						<div class="form-group">
							<label class="col-sm-2 control-label">Asesor:</label>
							<div class="col-sm-6">
								<p class="form-control-static"> {{$model->adviser->full_name}} </p>
							</div>
						</div>
					</div>
					<?php $selected['process']=''; $selected['inspect']=''; $selected['rework']=''; $selected['approved']=''; $selected['printed']=''; ?>
					<?php $selected[$model->status]='selected' ?>
					<?php $p1=\Auth::user()->action_allowed('autorepair.service_checklists.edit_as_adviser') ?>
					{!! Form::model($model, ['route'=>[ 'autorepair.service_checklists.save_status', $model ], 'method'=>'GET', 'class'=>'form-horizontal']) !!}
					<div class="form-group">
						{!! Form::label('status','Estado', ['class'=>'col-sm-2 control-label']) !!}
						<div class="col-sm-6">
							<select name="status" class="form-control">
							@if($p1)
								@if($model->status=='inspect')
								<option value="inspect" {{$selected['inspect']}} >INSPECT</option>
								<option value="rework" {{$selected['rework']}} >REWORK</option>
								<option value="approved" {{$selected['approved']}} >APPROVED</option>
								@endif
							@else
								<option value="process" {{$selected['process']}} >PROCESS</option>
								<option value="inspect" {{$selected['inspect']}} >INSPECT</option>
								<option value="rework" {{$selected['rework']}} >REWORK</option>
								<option value="approved" {{$selected['approved']}} >APPROVED</option>
								<option value="printed" {{$selected['printed']}} >PRINTED</option>
							@endif
							</select>
						</div>
					</div>
					<div class="form-group">
						<div class="col-sm-3">
							<button type="submit" class="btn btn-primary"><span class="glyphicon glyphicon-floppy-saved" aria-hidden="true"></span> GRABAR</button>
						</div>
					</div>
					{!! Form::close() !!}
				</div>
			</div>
		</div>
	</div>
</div>
@endsection
