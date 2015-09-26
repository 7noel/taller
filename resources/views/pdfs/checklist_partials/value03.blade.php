&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp
@if($status == 'info')
<input type="radio" name="checkitems[{{ $checkitem->id }}][status]" value="info" class="checkitem-info radio-info" checked disabled>
@else
<input type="radio" name="checkitems[{{ $checkitem->id }}][status]" value="info" class="checkitem-info radio-info" disabled>
@endif