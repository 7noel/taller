@if($status == 'success')
<input type="radio" name="checkitems[{{ $checkitem->id }}][status]" value="success" class="checkitem-success radio-success" checked disabled>
@else
<input type="radio" name="checkitems[{{ $checkitem->id }}][status]" value="success" class="checkitem-success radio-success" disabled>
@endif

@if($status == 'warning')
<input type="radio" name="checkitems[{{ $checkitem->id }}][status]" value="warning" class="checkitem-warning radio-warning" checked disabled>
@else
<input type="radio" name="checkitems[{{ $checkitem->id }}][status]" value="warning" class="checkitem-warning radio-warning" disabled>
@endif

@if($status == 'danger')
<input type="radio" name="checkitems[{{ $checkitem->id }}][status]" value="danger" class="checkitem-danger radio-danger" checked disabled>
@else
<input type="radio" name="checkitems[{{ $checkitem->id }}][status]" value="danger" class="checkitem-danger radio-danger" disabled>
@endif
