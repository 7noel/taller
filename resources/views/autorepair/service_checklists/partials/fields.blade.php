					<div class="form-group form-group-sm">
						{!! Form::label('order_id','Nro de Orden', ['class'=>'col-sm-2 control-label']) !!}
						<div class="col-sm-3">
						{!! Form::text('order_id', null, ['class'=>'form-control uppercase', 'required'=>'required']) !!}
						</div>
						{!! Form::label('plate','Placa', ['class'=>'col-sm-2 control-label']) !!}
						<div class="col-sm-3">
						{!! Form::text('plate', null, ['class'=>'form-control', 'required', 'readonly']) !!}
						</div>
					</div>
					<div class="form-group form-group-sm">
						{!! Form::label('company_name','Cliente', ['class'=>'col-sm-2 control-label']) !!}
						<div class="col-sm-3">
						{!! Form::text('company_name', null, ['class'=>'form-control', 'required', 'readonly']) !!}
						</div>
						{!! Form::label('adviser','Asesor', ['class'=>'col-sm-2 control-label']) !!}
						<div class="col-sm-3">
						{!! Form::text('adviser', null, ['class'=>'form-control uppercase', 'readonly']) !!}
						</div>
					</div>
					<div class="form-group form-group-sm">
						{!! Form::label('technician','Técnico', ['class'=>'col-sm-2 control-label']) !!}
						<div class="col-sm-3">
						{!! Form::text('technician', null, ['class'=>'form-control uppercase']) !!}
						</div>
						{!! Form::label('observation','Observación', ['class'=>'col-sm-2 control-label']) !!}
						<div class="col-sm-3">
						{!! Form::text('observation', null, ['class'=>'form-control uppercase']) !!}
						</div>
					</div>
					<div class="divCheckitems">
						<?php $i=0; ?>
						@foreach($groups as $group)
						<div>
							<p><strong>{{ $group->name }}</strong></p>
							<div>
								@foreach($group->checkitems as $key => $checkitem)
								<?php $i++; ?>
								<div class="form-group  form-group-sm">
									<label for="" class='col-sm-4 control-label'>{!! $checkitem->name !!}</label>
									<?php 
										if (isset($model)) {
											$cc = $checkitem->service_checklists()->where('service_checklist_id',$model->id)->first();
										}
										if (isset($cc)) {
											$status = $cc->pivot->status;
											$value = $cc->pivot->value;
										} else {
											$status = '';
											$value = '';
										}
									 ?>
									@if($checkitem->with_status)
										<div class="col-sm-4 radio">
											<label class="checkitem-label-success">
												@if($status == 'success')
												<input type="radio" name="checkitems[{{ $checkitem->id }}][status]" value="success" class="checkitem-success radio-success" checked>
												@else
												<input type="radio" name="checkitems[{{ $checkitem->id }}][status]" value="success" class="checkitem-success radio-success" >
												@endif
												Satisfactorio
											</label>
											<label class="checkitem-label-warning">
												@if($status == 'warning')
												<input type="radio" name="checkitems[{{ $checkitem->id }}][status]" value="warning" class="checkitem-warning radio-warning" checked>
												@else
												<input type="radio" name="checkitems[{{ $checkitem->id }}][status]" value="warning" class="checkitem-warning radio-warning">
												@endif
												Proximo
											</label>
											<label class="checkitem-label-danger">
												@if($status == 'danger')
												<input type="radio" name="checkitems[{{ $checkitem->id }}][status]" value="danger" class="checkitem-danger radio-danger" checked>
												@else
												<input type="radio" name="checkitems[{{ $checkitem->id }}][status]" value="danger" class="checkitem-danger radio-danger">
												@endif
												Urgente
											</label>
										</div>
									@endif
									@if($checkitem->with_check)
										<div class="col-sm-4 radio">
											<label>
												@if($status == 'info')
												<input type="checkbox" name="checkitems[{{ $checkitem->id }}][status]" value="info" class="checkitem-info checkbox-info" checked>
												@else
												<input type="checkbox" name="checkitems[{{ $checkitem->id }}][status]" value="info" class="checkitem-info checkbox-info">
												@endif
											</label>
										</div>
									@endif
									@if($checkitem->with_value)
										<div class="col-sm-4">
											<label>
												{{ $checkitem->pre_value }}
												<input type="input" name="checkitems[{{ $checkitem->id }}][value]" value="{{ $value }}">
												{{ $checkitem->post_value }}
											</label>
										</div>
									@endif
								</div>
								@endforeach
							</div>
						</div>
						@endforeach
						<input type="hidden" value="{{ $i }}" name="items" id="items">
					</div>
					<br>