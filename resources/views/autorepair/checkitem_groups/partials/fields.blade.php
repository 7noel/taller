					<div class="form-group  form-group-sm">
						{!! Form::label('name','Nombre', ['class'=>'col-sm-2 control-label']) !!}
						<div class="col-sm-10">
						{!! Form::text('name', null, ['class'=>'form-control uppercase']) !!}
						</div>
					</div>
					<div class="form-group  form-group-sm">
						{!! Form::label('order','Orden', ['class'=>'col-sm-2 control-label']) !!}
						<div class="col-sm-10">
						{!! Form::text('order', null, ['class'=>'form-control uppercase']) !!}
						</div>
					</div>
					<div class="form-group  form-group-sm">
						{!! Form::label('','Opciones:', ['class'=>'col-sm-2 control-label']) !!}
						<div class="col-sm-10">
							<label class="checkbox-inline">
								{!! Form::checkbox('is_double_column', '1') !!} Es doble columna
							</label>
						</div>
					</div>
					<div class="divCheckitems">
						<button type="button" class="btn btn-success btn-xs btnAddChecktem" aria-label="Left Align"><span class="glyphicon glyphicon-plus" aria-hidden="true"></span></button> <span class="list-group-item-heading">Nuevo Item</span>
						<?php $i=0; ?>
						@if(isset($model))
						@foreach($model->checkitems as $checkitem)
							<?php $i++; ?>
							<div class="checkitem">
								<input type="hidden" name="checkitems[{{ $i }}][id]" value="{{ $checkitem->id }}">
								<input type="text" name="checkitems[{{ $i }}][name]" placeholder="Nombre" class="checkitem-name" value="{{ $checkitem->name }}">
								<input type="checkbox" value="1" name="checkitems[{{ $i }}][with_status]" class="checkitem-checkbox" <?php echo ($checkitem->with_status==true) ? 'checked=\'checked\'' : '' ; ?> > Con Estado
								<input type="checkbox" value="1" name="checkitems[{{ $i }}][with_value]" class="checkitem-checkbox" <?php echo ($checkitem->with_value==true) ? 'checked=\'checked\'' : '' ; ?> > Con Valor
								<input type="checkbox" value="1" name="checkitems[{{ $i }}][with_check]" class="checkitem-checkbox" <?php echo ($checkitem->with_check==true) ? 'checked=\'checked\'' : '' ; ?> > Con Check
								<input type="text" name="checkitems[{{ $i }}][pre_value]" placeholder="Pre Valor" class="checkitem-pre_value" value="{{ $checkitem->pre_value }}">
								<input type="text" name="checkitems[{{ $i }}][post_value]" placeholder="Post Valor" class="checkitem-post_value" value="{{ $checkitem->post_value }}">
								<input type="checkbox" value="1" name="checkitems[{{ $i }}][column_two]" class="checkitem-checkbox" <?php echo ($checkitem->column_two==true) ? 'checked=\'checked\'' : '' ; ?> > Columna 2
							</div>
						@endforeach
						@endif
						<input type="hidden" value="{{ $i }}" name="items" id="items">
					</div>
					<br>