					<div class="form-group  form-group-sm">
						{!! Form::label('name','Nombres', ['class'=>'col-sm-2 control-label']) !!}
						<div class="col-sm-10">
						{!! Form::text('name', null, ['class'=>'form-control']) !!}
						</div>
					</div>
					<div class="form-group  form-group-sm">
						{!! Form::label('internal_code','Codigo Interno', ['class'=>'col-sm-2 control-label']) !!}
						<div class="col-sm-10">
						{!! Form::text('internal_code', null, ['class'=>'form-control']) !!}
						</div>
					</div>
					<div class="form-group  form-group-sm">
						{!! Form::label('provider_code','Codigo de Proveedor', ['class'=>'col-sm-2 control-label']) !!}
						<div class="col-sm-10">
						{!! Form::text('provider_code', null, ['class'=>'form-control']) !!}
						</div>
					</div>
					<div class="form-group  form-group-sm">
						{!! Form::label('manufacturer_code','Codigo de Fabricante', ['class'=>'col-sm-2 control-label']) !!}
						<div class="col-sm-10">
						{!! Form::text('manufacturer_code', null, ['class'=>'form-control']) !!}
						</div>
					</div>
					<div class="form-group  form-group-sm">
						{!! Form::label('description','Descripción', ['class'=>'col-sm-2 control-label']) !!}
						<div class="col-sm-10">
						{!! Form::text('description', null, ['class'=>'form-control']) !!}
						</div>
					</div>
					<div class="form-group  form-group-sm">
						{!! Form::label('sub_category_id','SubCategoría', ['class'=>'col-sm-2 control-label']) !!}
						<div class="col-sm-10">
							<div class="form-inline">
							{!! Form::select('category_id', $categories, ((isset($model->sub_category_id)) ? $model->sub_category->category_id : null),['class'=>'form-control', 'id'=>'lstCategories']); !!}
							{!! Form::select('sub_category_id', ((isset($sub_categories)) ? $sub_categories : [''=>'SELECCIONAR']), null,['class'=>'form-control', 'id'=>'lstSubCategories']); !!}
							</div>
						</div>
					</div>
					<div class="form-group  form-group-sm">
						{!! Form::label('unit_id','Unidad', ['class'=>'col-sm-2 control-label']) !!}
						<div class="col-sm-10">
							<div class="form-inline">
							{!! Form::select('unit_type_id', $unit_types, ((isset($model->unit_id)) ? $model->unit->unit_type_id : null),['class'=>'form-control', 'id'=>'lstUnitTypes']); !!}
							{!! Form::select('unit_id', ((isset($units)) ? $units : [''=>'SELECCIONAR']), null,['class'=>'form-control', 'id'=>'lstUnit']); !!}
							</div>
						</div>
					</div>
					@if(isset($model))
						@include('storage.products.partials.stocks')
					@endif
