$(document).ready(function(){
	$('.btnAddChecktem').click(function(e){
		var div = $(this).parent();
		addChecktem(div);
	});
	$('#order_id').change(function(){
		getDataOt();
	});
	$('.checkitem-select').change(function(){
		var select = $(this);
		console.log(select.val());
		select.removeClass('success');
		select.removeClass('warning');
		select.removeClass('danger');
		if (select.val()=='success') {
			select.addClass('success');
		} else if(select.val()=='warning'){
			select.addClass('warning');
		} else if(select.val()=='danger'){
			select.addClass('danger');
		} else{
			//select.addClass('select-none');
		};

	});
	$('#date-cita').change(function(){
		var date = $(this).val();
		loadTimes(date);
	});
	$('#time-cita').change(function(){
		var date = $('#date-cita').val();
		var time = $('#time-cita').val();
		loadAsesores(date, time);
	});
	$('#placa').change(function(){
		var placa = $(this).val();
		loadData(placa);
	});
	$('#orderasesor').change(function(){
		var order = $(this).val();
		var date = $('#date-cita').val();
		getAsesor(order, date);
	});
});
function addChecktem (div) {
	var html = htmlCheckitem();
	div.append(html);
}
function htmlCheckitem () {
	var html = '';
	var items = parseFloat($('#items').val()) + 1;
	$('#items').val(items);
	html = html + "<div class=\"checkitem\">";
	html = html + "<input type=\"text\" name=\"checkitems["+items+"][name]\" placeholder=\"Nombre\" class=\"checkitem-name\"> ";
	html = html + "<input type=\"checkbox\" name=\"checkitems["+items+"][with_status]\" value=\"1\" class=\"checkitem-checkbox\" checked=\"checked\"> Con Estado ";
	html = html + "<input type=\"checkbox\" name=\"checkitems["+items+"][with_value]\" value=\"1\" class=\"checkitem-checkbox\"> Con Valor ";
	html = html + "<input type=\"checkbox\" name=\"checkitems["+items+"][with_check]\" value=\"1\" class=\"checkitem-checkbox\"> Con Check ";
	html = html + "<input type=\"text\" name=\"checkitems["+items+"][pre_value]\" placeholder=\"Pre Valor\" class=\"checkitem-pre_value\"> ";
	html = html + "<input type=\"text\" name=\"checkitems["+items+"][post_value]\" placeholder=\"Post Valor\" class=\"checkitem-post_value\"> ";
	html = html + "<input type=\"checkbox\" name=\"checkitems["+items+"][column_two]\" value=\"1\" class=\"checkitem-checkbox\"> Columna 2";
	html = html + "</div>";
	return html;
}
function getDataOt () {
	var ot = $('#order_id').val();
	var page = "/autorepair/service_checklists/ajaxGetOt/" + ot;
	$.get(page, function(data){
		console.log(data);
		if ($.isEmptyObject(data)) {
			$('#order_id').val('');
		} else{
			$('#company_name').val(data.NomCliente);
			$('#plate').val(data.Placa);
			$('#adviser_id').val(data.adviser_id);
			$('#adviser').val(data.adviser);
		};
	});
}
function loadTimes (date) {
	if (date == "") {
		$('#time-cita option').remove();
		$('#time-cita').append("<option>Seleccionar</option>");
		$('#orderasesor option').remove();
		$('#orderasesor').append("<option>Seleccionar</option>");
	} else{
		var page = "/autorepair/schedulings/ajaxGetTime/" + date;
		$.get(page, function(data){
			console.log(data);
			$('#time-cita option').remove();
			$('#time-cita').append(data);
		});
	};
}
function loadAsesores (date, time) {
	if (time == "") {
		$('#orderasesor option').remove();
		$('#orderasesor').append("<option>Seleccionar</option>");
	} else{
		var page = "/autorepair/schedulings/ajaxGetAsesores/" + date + "/" + time;
		$.get(page, function(data){
			console.log(data);
			$('#orderasesor option').remove();
			$('#orderasesor').append(data);
		});
	};
}
function loadData (placa) {
	if (placa == "") {
		$('#CodCliente').val("");
		$('#ruc_clie').val("");
		$('#nom_clie').val("");
		$('#RUC').val("");
		$('#DniExt').val("");
		$('#DNI').val("");
		$('#Direccion').val("");
		$('#Urbanizacion').val("");
		$('#distrito').val("");
		$('#Provincia').val("");
		$('#Telefonos').val("");
		$('#Celular').val("");
		$('#Contacto1').val("");
		$('#Email').val("");
		$('#Marca').val("");
		$('#Modelo').val("");
		$('#Version').val("");
		$('#tipo').val("");
		$('#Color').val("");
		$('#Serie').val("");
		$('#NoMotor').val("");
		$('#Anofab').val("");
		$('#NroChasis').val("");
		$('#AnoModelo').val("");
	} else{
		var page = "/autorepair/vehiculos/ajaxGetData/" + placa;
		$.get(page, function(data){
			console.log(data);
			$('#CodCliente').val(data.CodCliente);
			$('#ruc_clie').val(data.ruc_clie);
			$('#nom_clie').val(data.nom_clie);
			$('#RUC').val(data.RUC);
			$('#DniExt').val(data.DniExt);
			$('#DNI').val(data.DNI);
			$('#Direccion').val(data.Direccion);
			$('#Urbanizacion').val(data.Urbanizacion);
			$('#distrito').val(data.distrito);
			$('#Provincia').val(data.Provincia);
			$('#Telefonos').val(data.Telefonos);
			$('#Celular').val(data.Celular);
			$('#Contacto1').val(data.Contacto1);
			$('#Email').val(data.Email);
			$('#Marca').val(data.Marca);
			$('#Modelo').val(data.Modelo);
			$('#Version').val(data.Version);
			$('#tipo').val(data.tipo);
			$('#Color').val(data.Color);
			$('#Serie').val(data.Serie);
			$('#NoMotor').val(data.NoMotor);
			$('#Anofab').val(data.Anofab);
			$('#NroChasis').val(data.NroChasis);
			$('#AnoModelo').val(data.AnoModelo);
		});
	};
}
function getAsesor (order, date) {
	var page = "/autorepair/ajaxGetAsesorByOrder/" + order + "/" + date;
	$.get(page, function(data){
		console.log(data);
		$('#asesor').val(data.Codigo);
		$('#nomasesor').val(data.Nombre);
	});
}