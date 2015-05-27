					<div class="form-group  form-group-sm">
						{!! Form::label('date','Fecha', ['class'=>'col-sm-2 control-label']) !!}
						<div class="col-sm-2">
						{!! Form::text('date', null, ['class'=>'form-control date']) !!}
						</div>
					</div>
					<div class="form-group  form-group-sm">
						{!! Form::label('document_type_id','Documento', ['class'=>'col-sm-2 control-label']) !!}
						<div class="col-sm-10">
							<div class="form-inline">
							{!! Form::select('document_type_id',$document_types , null, ['class'=>'form-control col-sm-1']) !!}
							{!! Form::text('series', null, ['class'=>'form-control', 'placeholder'=>'Serie']) !!}
							{!! Form::text('number', null, ['class'=>'form-control', 'placeholder'=>'Número']) !!}
							</div>
						</div>
					</div>
					<div class="form-group  form-group-sm">
						{!! Form::label('currency_id','Moneda', ['class'=>'col-sm-2 control-label']) !!}
						<div class="col-sm-10">
							<div class="form-inline">
							{!! Form::select('currency_id',$currencies , null, ['class'=>'form-control col-sm-1']) !!}
							</div>
						</div>
					</div>
					<div class="form-group  form-group-sm">
						{!! Form::label('payment_condition_id','Condición de Pago', ['class'=>'col-sm-2 control-label']) !!}
						<div class="col-sm-10">
							<div class="form-inline">
							{!! Form::select('payment_condition_id',$payment_conditions , null, ['class'=>'form-control col-sm-1']) !!}
							</div>
						</div>
					</div>
					<div class="form-group  form-group-sm">
						{!! Form::label('company','Empresa', ['class'=>'col-sm-2 control-label']) !!}
						<div class="col-sm-10">
						{!! Form::hidden('company_id', null, ['id'=>'company_id']) !!}
						{!! Form::text('company', null, ['class'=>'form-control', 'id'=>'txtcompany']) !!}
						</div>
					</div>