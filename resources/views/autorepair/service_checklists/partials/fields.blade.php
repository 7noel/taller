					<div class="form-group  form-group-sm">
						{!! Form::label('order_id','Nro de Orden', ['class'=>'col-sm-2 control-label']) !!}
						<div class="col-sm-10">
						{!! Form::text('order_id', null, ['class'=>'form-control uppercase']) !!}
						</div>
					</div>
					<div class="form-group  form-group-sm">
						{!! Form::label('adviser','Asesor', ['class'=>'col-sm-2 control-label']) !!}
						<div class="col-sm-10">
						{!! Form::text('adviser', null, ['class'=>'form-control uppercase']) !!}
						</div>
					</div>
					<div class="form-group  form-group-sm">
						{!! Form::label('technician','Técnico', ['class'=>'col-sm-2 control-label']) !!}
						<div class="col-sm-10">
						{!! Form::text('technician', null, ['class'=>'form-control uppercase']) !!}
						</div>
					</div>
					<div class="form-group  form-group-sm">
						{!! Form::label('observation','Observación', ['class'=>'col-sm-2 control-label']) !!}
						<div class="col-sm-10">
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
									@if($checkitem->with_status)
										@if(isset($model))
										<?php 
											$cc = $checkitem->service_checklists()->where('service_checklist_id',$model->id)->first();
											if (isset($cc)) {
												$status = $cc->pivot->status;
												$value = $cc->pivot->value;
											} else {
												$status = '';
												$value = '';
											}
										 ?>
										<div class="col-sm-4 radio">
											<label class="checkitem-label-success">
												@if($status == 'success')
												<input type="radio" name="checkitems[{{ $checkitem->id }}][status]" value="success" class="checkitem-success checkbox-success" checked>
												@else
												<input type="radio" name="checkitems[{{ $checkitem->id }}][status]" value="success" class="checkitem-success checkbox-success" >
												@endif
												Satisfactorio
											</label>
											<label class="checkitem-label-warning">
												@if($status == 'warning')
												<input type="radio" name="checkitems[{{ $checkitem->id }}][status]" value="warning" class="checkitem-warning checkbox-warning" checked>
												@else
												<input type="radio" name="checkitems[{{ $checkitem->id }}][status]" value="warning" class="checkitem-warning checkbox-warning">
												@endif
												Proximo
											</label>
											<label class="checkitem-label-danger">
												@if($status == 'danger')
												<input type="radio" name="checkitems[{{ $checkitem->id }}][status]" value="danger" class="checkitem-danger checkbox-danger" checked>
												@else
												<input type="radio" name="checkitems[{{ $checkitem->id }}][status]" value="danger" class="checkitem-danger checkbox-danger">
												@endif
												Urgente
											</label>
										</div>
										@else
										<div class="col-sm-4 radio">
											<label class="checkitem-label-success">
												<input type="radio" name="checkitems[{{ $checkitem->id }}][status]" value="success" class="checkitem-success checkbox-success">Satisfactorio
											</label>
											<label class="checkitem-label-warning">
												<input type="radio" name="checkitems[{{ $checkitem->id }}][status]" value="warning" class="checkitem-warning checkbox-warning">Proximo
											</label>
											<label class="checkitem-label-danger">
												<input type="radio" name="checkitems[{{ $checkitem->id }}][status]" value="danger" class="checkitem-danger checkbox-danger">Urgente
											</label>
										</div>
										@endif
									@endif
									@if($checkitem->with_value)
										@if(isset($model))
										<?php 
											$cc = $checkitem->service_checklists()->where('service_checklist_id',$model->id)->first();
											if (isset($cc)) {
												$status = $cc->pivot->status;
												$value = $cc->pivot->value;
											} else {
												$status = '';
												$value = '';
											}
										 ?>
										<div class="col-sm-4">
											<label>
												{{ $checkitem->pre_value }}
												<input type="input" name="checkitems[{{ $checkitem->id }}][value]" value="{{ $value }}">
												{{ $checkitem->post_value }}
											</label>
										</div>
										@else
										<div class="col-sm-4">
											<label>
												{{ $checkitem->pre_value }}
												<input type="input" name="checkitems[{{ $checkitem->id }}][value]">
												{{ $checkitem->post_value }}
											</label>
										</div>
										@endif
									@endif

								</div>
								@endforeach
							</div>
						</div>
						@endforeach
						
						<input type="hidden" value="{{ $i }}" name="items" id="items">
					</div>
					<br>