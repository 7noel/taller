					<div class="form-group form-group-sm">
						{!! Form::hidden('status', null) !!}
						{!! Form::label('order_id','Nro de Orden', ['class'=>'col-sm-2 control-label']) !!}
						<div class="col-sm-3">
						@if(isset($model))
						{!! Form::text('order_id', null, ['class'=>'form-control uppercase', 'required'=>'required', 'readonly']) !!}
						@else
						{!! Form::text('order_id', null, ['class'=>'form-control uppercase', 'required'=>'required']) !!}
						@endif
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
						{!! Form::hidden('adviser_id', null, ['id'=>'adviser_id', 'required']) !!}
						{!! Form::text('adviser', null, ['class'=>'form-control uppercase', 'readonly', 'required']) !!}
						</div>
					</div>
					<div class="form-group form-group-sm">
						{!! Form::label('technician_id','Técnico', ['class'=>'col-sm-2 control-label']) !!}
						<div class="col-sm-3">
						@if(\Auth::user()->employee->job_id == 4)
						{!! Form::select('technician_id', [\Auth::user()->employee->id => \Auth::user()->employee->full_name], \Auth::user()->employee->id, ['class'=>'form-control', 'required']) !!}
						@else
						{!! Form::select('technician_id', $technicians, null, ['class'=>'form-control', 'required']) !!}
						@endif
						</div>
					</div>
					<div class="form-group">
						{!! Form::label('observation','Observación', ['class'=>'col-sm-2 control-label']) !!}
						<div class="col-sm-8">
						{!! Form::textarea('observation', null, ['class'=>'form-control uppercase','size' => '10x3']) !!}
						</div>
					</div>			
<?php $p1=\Auth::user()->action_allowed('autorepair.service_checklists.part01') ?>
<?php $p2=\Auth::user()->action_allowed('autorepair.service_checklists.part02') ?>
					<div class="divCheckitems">
						<?php $i=0; ?>
						<?php $with_two_values = array(1,2,3,4,16,48,49); ?>
						<?php $with_one_value = array(45,50,51); ?>
						@foreach($groups as $group)
						@if(($group->id != 7 and $p1) or ($group->id == 7 and $p2))
						<div>
							<p><strong>{{ $group->name }}</strong></p>
							<div>
								@foreach($group->checkitems as $key => $checkitem)
								<?php $i++; ?>
								<div class="form-group form-group-sm">
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
										<div class="col-sm-2">
											@if( in_array($checkitem->id, $with_two_values) )
											<select name="checkitems[{{ $checkitem->id }}][status]" class="form-control checkitem-select {{ $status }}">
												@if($status == '')
												<option value="" class="option" selected>Seleccionar</option>
												@else
												<option value="" class="option">Seleccionar</option>
												@endif

												@if($status == 'success')
												<option value="success" class="option" selected>Satisfactorio</option>
												@else
												<option value="success" class="option">Satisfactorio</option>
												@endif

												@if($status == 'danger')
												<option value="danger" class="option" selected>Urgente</option>
												@else
												<option value="danger" class="option">Urgente</option>
												@endif
											</select>
											@elseif( in_array($checkitem->id, $with_one_value) )
											<label>
												@if($status == 'info')
												<input type="checkbox" name="checkitems[{{ $checkitem->id }}][status]" value="info" class="checkitem-info checkbox-info" checked>
												@else
												<input type="checkbox" name="checkitems[{{ $checkitem->id }}][status]" value="info" class="checkitem-info checkbox-info">
												@endif
											</label>
											@else
											<select name="checkitems[{{ $checkitem->id }}][status]" class="form-control checkitem-select {{ $status }}">
												@if($status == '')
												<option value="" class="option" selected>Seleccionar</option>
												@else
												<option value="" class="option">Seleccionar</option>
												@endif

												@if($status == 'success')
												<option value="success" class="option" selected>Satisfactorio</option>
												@else
												<option value="success" class="option">Satisfactorio</option>
												@endif

												@if($status == 'warning')
												<option value="warning" class="option" selected>Proximo</option>
												@else
												<option value="warning" class="option">Proximo</option>
												@endif

												@if($status == 'danger')
												<option value="danger" class="option" selected>Urgente</option>
												@else
												<option value="danger" class="option">Urgente</option>
												@endif
											</select>
											@endif
											
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
						@endif
						@endforeach
						<input type="hidden" value="{{ $i }}" name="items" id="items">
					</div>
					<br>