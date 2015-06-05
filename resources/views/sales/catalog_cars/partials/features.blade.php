<br>
<?php $i=0; ?>
@foreach($groups as $group)
<div data-group="{{$group->id}}">
	<button type="button" class="btn btn-success btn-xs btnAddFeature" aria-label="Left Align"><span class="glyphicon glyphicon-plus" aria-hidden="true"></span></button> <span class="list-group-item-heading">{{ $group->name }}</span>
	@if(isset($model))
	@foreach($model->features->where('feature_group_id', $group->id) as $feature)

		<?php $i++; ?>

		<div class="feature">
			<input type="hidden" name="features[{{ $i }}][id]" value="{{ $feature->id }}">
			<input type="hidden" name="features[{{ $i }}][feature_group_id]" value="{{ $group->id }}" class="feature_group_id">
			<input type="text" name="features[{{ $i }}][name]" placeholder="Nombre" class="name" value="{{ $feature->name }}">
			<input type="text" name="features[{{ $i }}][value]" placeholder="Valor" class="value" value="{{ $feature->value }}">
		</div>

	@endforeach
	@endif
</div>
<br>
@endforeach
<input type="hidden" value="{{ $i }}" name="items" id="items">
