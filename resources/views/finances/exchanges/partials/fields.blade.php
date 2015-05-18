					<div class="form-group  form-group-sm">
						{!! Form::label('date','Fecha', ['class'=>'col-sm-2 control-label']) !!}
						<div class="col-sm-10">
						{!! Form::text('date', null, ['class'=>'form-control date']) !!}
						</div>
					</div>
					<div class="form-group  form-group-sm">
						{!! Form::label('currency_id','Moneda', ['class'=>'col-sm-2 control-label']) !!}
						<div class="col-sm-10">
						{!! Form::select('currency_id', $currencies, null,['class'=>'form-control']); !!}
						</div>
					</div>
					<div class="form-group  form-group-sm">
						{!! Form::label('sales','Venta', ['class'=>'col-sm-2 control-label']) !!}
						<div class="col-sm-10">
						{!! Form::text('sales', null, ['class'=>'form-control']) !!}
						</div>
					</div>
					<div class="form-group  form-group-sm">
						{!! Form::label('purchase','Compra', ['class'=>'col-sm-2 control-label']) !!}
						<div class="col-sm-10">
						{!! Form::text('purchase', null, ['class'=>'form-control']) !!}
						</div>
					</div>