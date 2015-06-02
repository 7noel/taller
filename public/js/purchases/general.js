$(document).ready(function(){
	$('#btnUpdateProduct').hide();
	$('#btnCancelProduct').hide();
	//para el autocomplete de los productos
	$('#txtproduct').autocomplete({ });
	$('#listWarehouses').change(function(){
		/*if ($('#listWarehouses').val()>0) {
			resetAutocomplete();
		} else{
			$('#txtproduct').autocomplete("destroy");
		};*/
		resetAutocomplete();
	});
	$( "#txtproduct" ).focus(function() {
		if ($('#listWarehouses').val()==='') {
			$('#listWarehouses').focus();
		}
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
});

function resetAutocomplete () {
	var warehouse_id = $('#listWarehouses').val();
	var page = "/storage/products/autocomplete/"+warehouse_id;
	if (warehouse_id > 0) {
		$('#txtproduct').autocomplete("destroy");
		$('#txtproduct').autocomplete({
			source: page,
			minLength: 4,
			select: function(event, ui){
				var data = ui.item.id;
				$('#code').text(data.intern_code);
				$('#stock').text(parseFloat(data.stock).toFixed(2));
				$('#unit').text(data.unit_symbol);
				$('#price').text(parseFloat(data.price).toFixed(2));
				var detail = {stock_id:data.stock_id, name:data.name, product_id:data.product_id, intern_code: data.intern_code, provider_code: data.provider_code,manufacturer_code:data.manufacturer_code, warehouse_id:data.warehouse_id, unit_id:data.unit_id, unit_symbol:data.unit_symbol, stock:parseFloat(data.stock).toFixed(2), price:parseFloat(data.price).toFixed(2) };
				$('#row_data').data('detail',detail);
				$('#txtquantity').focus();
			}
		});
	}
}
function calcTotal () {
	var q = parseFloat($('#txtquantity').val());
	var d = parseFloat($('#txtdiscount').val());
	var p = parseFloat($('#price').text());
	if (!(q>0)) {q=1; $('#txtquantity').val('1.00');}
	if (!(d>=0)) {d=0; $('#txtdiscount').val('0.00');}
	if (!(p>=0)) {p=0; $('#price').text('0.00');}

	var t = p*q*(100-d)/100;
	$('#total').text(t.toFixed(2));

}
function addDetail () {
	var data = $('#row_data').data('detail');
	data.quantity = parseFloat($('#txtquantity').val()).toFixed(2);
	data.discount = parseFloat($('#txtdiscount').val()).toFixed(2);
	data.items = parseFloat($('#items').val());
	data.vbruto = (data.price*data.quantity).toFixed(2);
	data.vventa = (data.vbruto*(100-data.discount)/100).toFixed(2);
	data.items = 1 + data.items;
	var table = [
		{ class:'text-center', value:data.warehouse_id },
		{ class:'text-center', value:data.intern_code },
		{ class:'text-left', value:data.name },
		{ class:'text-right', value:data.quantity + ' ' + data.unit_symbol },
		{ class:'text-right', value:data.price },
		{ class:'text-right', value:data.vbruto },
		{ class:'text-right', value:data.discount },
		{ class:'text-right', value:data.vventa }
	];
	var name_detail = "purchase_details";
	var inputs = [
		{ name:name_detail+'['+data.items+'][product_id]', value:data.product_id },
		{ name:name_detail+'['+data.items+'][warehouse_id]', value:data.warehouse_id },
		{ name:name_detail+'['+data.items+'][stock_id]', value:data.stock_id },
		{ name:name_detail+'['+data.items+'][unit_id]', value:data.unit_id },
		{ name:name_detail+'['+data.items+'][quantity]', value:data.quantity },
		{ name:name_detail+'['+data.items+'][discount]', value:data.discount }
	];
	if (data.warehouse_id>0 && data.stock_id>0 && data.quantity>0) {
		tds = generateTds(table);
		btns = generateBtns(inputs);
		$('#tblDetails').append("<tr data-id=\""+data.product_id+"\">"+tds+btns+"</tr>");
		$('#items').val(data.items);
		$('#txtproduct').val('');
		$('#stock_id').val('');
		$('#txtquantity').val('');
		$('#txtdiscount').val('');
		$('#code').text('');
		$('#stock').text('');
		$('#price').text('');
		$('#total').text('');
		$('#unit').text('');
		$('#txtproduct').focus();
	}
	
	console.log(table);
}
function deleteDetail (e, row) {
	if (confirm("Seguro que desea eliminar el Registro?")) {
		e.preventDefault();
		row.fadeOut();
		row.remove();
	}
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
		$('#txtquantity').focus();
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