$(document).ready(function(){
	$('.btn-delete').click(function(e){
		if (!confirm("Seguro que desea eliminar el Registro?")) {
			e.preventDefault();
			return;
		};
		
		e.preventDefault();
		var row = $(this).parents('tr');
		var id = row.data('id');
		var form = $('#form-delete');
		var url = form.attr('action').replace(':_ID', id);
		var data = form.serialize();

		row.fadeOut();

		$.post(url, data, function(result){
			alert(result.message);
		}).fail(function(){
			alert('El registro no fue eliminado');
			row.show();
		})
	})

	$('#tableStocks').on('click','.btn-delete-stock',function(e){
		confirm("Seguro que desea eliminar el Registro?");
		e.preventDefault();
		var row = $(this).parents('tr');
		row.fadeOut();
		row.remove();
	})

})
