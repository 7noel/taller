$(document).ready(function(){
	//carga almacenes
	$('#btnNewStock').click(function() {
		loadWarehouses();
	});
	$('#btnSaveStock').click(function() {
		loadStock();
	});

});


/*cargar almacenes*/
function loadWarehouses(){
	$('#stockini').val('');
	var page = "/listWarehouses";
	var destroy = false;
	$.get(page, function(data){
		$('#listWarehouses').empty();
		$('#listWarehouses').append("<option value=''> </option>");
		$.each(data, function (index, Obj) {
			$('#listWarehouses').append("<option value='"+Obj.id+"'>"+Obj.id+"</option>");
			$("#tableStocks tr").find('td:eq(0)').each(function () {
				destroy = false;
				codigo = $(this).html();
				if(codigo==Obj.id){
					destroy = true;
				}
				if (destroy === true) { $("#listWarehouses option[value='"+Obj.id+"']").remove(); }
			});
		});
		
	});
}

function loadStock () {
	var warehouse = $('#listWarehouses').val();
	var stock = parseFloat($('#stockini').val());
	var items = parseFloat($('#items').val());
	if (warehouse !== "") {
		$('#tableStocks').append("<tr><td>"+warehouse+"</td><td>"+stock+"</td><td><a href='#' class='btn-delete-stock btn btn-danger btn-xs'>Eliminar</a><input type='hidden' name='stocks["+items+"][warehouse_id]' value='"+warehouse+"'><input type='hidden' name='stocks["+items+"][stock]' value='"+stock+"'></td><tr>");
		items = 1+items;
		$('#items').val(items);
	}
}