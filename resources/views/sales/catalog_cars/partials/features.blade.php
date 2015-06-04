<br>
@foreach($groups as $group)
	
	<button type="button" class="btn btn-success" aria-label="Left Align"><span class="glyphicon glyphicon-plus" aria-hidden="true"></span></button> <span class="list-group-item-heading">{{ $group->name }}</span>

	<div><input type="text" name="feature[][name]" placeholder="Nombre"> <input type="text" name="feature[][value]" placeholder="Valor"></div>
	<div><input type="text" name="feature[][name]" placeholder="Nombre"> <input type="text" name="feature[][value]" placeholder="Valor"></div>
	<div><input type="text" name="feature[][name]" placeholder="Nombre"> <input type="text" name="feature[][value]" placeholder="Valor"></div>
	<div><input type="text" name="feature[][name]" placeholder="Nombre"> <input type="text" name="feature[][value]" placeholder="Valor"></div>
	<div><input type="text" name="feature[][name]" placeholder="Nombre"> <input type="text" name="feature[][value]" placeholder="Valor"></div>
	<br>

@endforeach