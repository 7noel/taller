$(document).ready(function(){
	$('#btnUpdateProduct').hide();
	$('#btnCancelProduct').hide();
	//para el autocomplete de los productos
	$('#txtproduct').autocomplete({ });
	$('#listWarehouses').change(function(){
		resetAutocomplete();
	});

	$('#txtquantity, #txtdiscount').change(function(){
		calcTotal();
	});
	$('#btnAddProduct').click(function(e){
		addDetail();
	});
	$('#btnUpdateProduct').click(function(e){
		updateDetail();
	});
	$('#tblDetails').on('click','.btn-delete-detail',function(e){
		var row = $(this).parents('tr');
		deleteDetail(e,row);
	});
	$('#tblDetails').on('click','.btn-edit-detail',function(e){
		var row = $(this).parents('tr');
		editDetail(row);
	});
	$('#btnCancelProduct').click(function(e){
		cancelDetail();
	});
})

function resetAutocomplete () {
	var warehouse_id = $('#listWarehouses').val();
	var page = "/storage/products/autocomplete/"+warehouse_id;
	$('#txtproduct').autocomplete("destroy");
	$('#txtproduct').autocomplete({
		source: page,
		minLength: 1,
		select: function(event, ui){
			var price = parseFloat(ui.item.id.product.price);
			$('#warehouse_id').val(ui.item.id.warehouse_id);
			$('#product_id').val(ui.item.id.id);
			$('#code').text(ui.item.id.id);
			$('#stock').text(ui.item.id.stock.toFixed(2));
			$('#unit').text(ui.item.id.product.unit.symbol);
			$('#price').text(price.toFixed(2));
			$('#txtquantity').focus();
		}
	});
}
function calcTotal () {
	var q = parseFloat($('#txtquantity').val());
	var d = parseFloat($('#txtdiscount').val());
	var p = parseFloat($('#price').text());
	if (!(q>0)) {q=1; $('#txtquantity').val('1.00');};
	if (!(d>=0)) {d=0; $('#txtdiscount').val('0.00');};
	if (!(p>=0)) {p=0; $('#price').text('0.00');};

	var t = p*q*(100-d)/100;
	$('#total').text(t.toFixed(2));

}
function addDetail () {
	var w = parseFloat($('#warehouse_id').val());
	var c = parseFloat($('#product_id').val());
	var n = $('#txtproduct').val();
	var q = parseFloat($('#txtquantity').val());
	var u = $('#unit').text();
	var d = parseFloat($('#txtdiscount').val());
	var p = parseFloat($('#price').text());
	var items = parseFloat($('#items').val());
	var vbruto = p*q;
	var vventa = vbruto*(100+d)/100;
	console.log('almacen: '+w);
	console.log('codigo: '+c);
	console.log('cantidad: '+q);
	if (w>0 && c>0 && q>0) {
		$('#tblDetails').append("<tr><td class=\"text-center\">"+w+"</td><td class=\"text-center\">"+c+"</td><td>"+n+"</td><td class=\"text-right\"><label>"+q.toFixed(2)+" <label> <span>"+u+"</span></td><td class=\"text-right\">"+p.toFixed(2)+"</td><td class=\"text-right\">"+vbruto.toFixed(2)+"</td><td class=\"text-right\">"+d.toFixed(2)+"</td><td class=\"text-right\">"+vventa.toFixed(2)+"</td> <td><a href=\"#\" class=\"btn-edit-detail btn btn-primary btn-xs\"><span class=\"glyphicon glyphicon-pencil\" aria-hidden=\"true\"></span> Editar</a> <a href=\"#\" class=\"btn-delete-detail btn btn-danger btn-xs\"><span class=\"glyphicon glyphicon-trash\" aria-hidden=\"true\"></span> Eliminar</a><input type='hidden' name='stocks["+items+"][warehouse_id]' value='"+w+"'><input type='hidden' name='stocks["+items+"][stock]' value='"+q+"'></td><tr>");
		
		items = 1+items;
		$('#items').val(items);
		$('#txtproduct').val('');
		$('#product_id').val('');
		$('#txtquantity').val('');
		$('#txtdiscount').val('');
		$('#code').text('');
		$('#stock').text('');
		$('#price').text('');
		$('#txtproduct').focus();
	};
}
function deleteDetail (e, row) {
	if (confirm("Seguro que desea eliminar el Registro?")) {
		e.preventDefault();
		row.fadeOut();
		row.remove();
	};
}
function editDetail (row) {
	$('#btnAddProduct').hide();
	$('#btnUpdateProduct').show();
	$('#btnCancelProduct').show();
	var warehouse_id = row.find('td:eq(0)').text();
	var product_id = row.find('td:eq(1)').text();
	var page = "/storage/products/ajaxGetData/"+warehouse_id+"/"+product_id;
	$.get(page, function(data){
		console.log(data);
		var w = row.find('td:eq(0)').text();
		var c = row.find('td:eq(1)').text();
		var n = row.find('td:eq(2)').text();
		var q = row.find('td:eq(3) label').text();
		var u = row.find('td:eq(3) span').text();
		var p = row.find('td:eq(4)').text();
		var d = row.find('td:eq(6)').text();


		$('#warehouse_id').val(w);
		$('#product_id').val(c);
		$('#code').text(c);
		$('#txtproduct').val(n);
		$('#txtquantity').val(q);
		$('#unit').text(u);
		$('#txtdiscount').val(d);
		$('#stock').text(data.stock.toFixed(2));
		$('#price').text(p);
	});

}
function updateDetail () {
	alert('Update Detalle');
}
function cancelDetail () {
	$('#btnAddProduct').show();
	$('#btnUpdateProduct').hide();
	$('#btnCancelProduct').hide();
	//$('#items').val(items);
	$('#txtproduct').val('');
	$('#product_id').val('');
	$('#txtquantity').val('');
	$('#txtdiscount').val('');
	$('#code').text('');
	$('#stock').text('');
	$('#price').text('');
	$('#txtproduct').focus();
}