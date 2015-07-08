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
								@foreach($group->checkitems as $checkitem)
								<?php $i++; ?>
								<div class="form-group  form-group-sm">
									<label for="" class='col-sm-4 control-label'>{!! $checkitem->name !!}</label>
									@if($checkitem->with_status)
									<div class="col-sm-4 radio">
										<label class="checkitem-label-success">
											<input type="radio" name="checkitems[{{ $i }}][status]" value="success" class="checkitem-success">Satisfactorio
										</label>
										<label class="checkitem-label-warning">
											<input type="radio" name="checkitems[{{ $i }}][status]" value="warning" class="checkitem-warning">Proximo
										</label>
										<label class="checkitem-label-danger">
											<input type="radio" name="checkitems[{{ $i }}][status]" value="danger" class="checkitem-danger">Urgente
										</label>
									</div>
									@endif
									@if($checkitem->with_value)
									<div class="col-sm-4">
										<label>
											{{ $checkitem->pre_value }}
											<input type="input" name="checkitems[{{ $i }}][value]">
											{{ $checkitem->post_value }}
										</label>
									</div>
									@endif

								</div>
								@endforeach
							</div>
						</div>
						@endforeach
						@if(isset($model))
						@foreach($model->checkitems as $checkitem)
							<div class="checkitem">
								<input type="hidden" name="checkitems[{{ $i }}][id]" value="{{ $checkitem->id }}">
								<input type="text" name="checkitems[{{ $i }}][name]" placeholder="Nombre" class="checkitem-name" value="{{ $checkitem->name }}">
								<input type="checkbox" value="1" name="checkitems[{{ $i }}][with_status]" class="checkitem-checkbox" <?php echo ($checkitem->with_status==true) ? 'checked=\'checked\'' : '' ; ?> > Con Estado
								<input type="checkbox" value="1" name="checkitems[{{ $i }}][with_value]" class="checkitem-checkbox" <?php echo ($checkitem->with_value==true) ? 'checked=\'checked\'' : '' ; ?> > Con Valor
								<input type="text" name="checkitems[{{ $i }}][pre_value]" placeholder="Pre Valor" class="checkitem-pre_value" value="{{ $checkitem->pre_value }}">
								<input type="text" name="checkitems[{{ $i }}][post_value]" placeholder="Post Valor" class="checkitem-post_value" value="{{ $checkitem->post_value }}">
								<input type="checkbox" value="1" name="checkitems[{{ $i }}][column_two]" class="checkitem-checkbox" <?php echo ($checkitem->column_two==true) ? 'checked=\'checked\'' : '' ; ?> > Columna 2
							</div>
						@endforeach
						@endif
						<input type="hidden" value="{{ $i }}" name="items" id="items">
					</div>
					<br>