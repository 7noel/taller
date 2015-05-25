$(document).ready(function(){
	//MAYUSCULAS
	$(".delete").submit(function(e){
	    if (!confirm("Seguro que desea eliminar el Registro?"))
		{
			e.preventDefault();
			return;
		} 
	});
	$('.uppercase').change(function(){
		var cadena=$(this).val();
		cadena = cadena.replace("  "," ");
		cadena = cadena.toUpperCase();
		$(this).val(cadena);
	});

	//carga provincias
	$('#lstDepartamento').change(function(){
		cargaProvincias();
		var depa=$('#lstDepartamento').val();
		if (depa=='') {$('#lstProvincia').html("");};
		$('#lstDistrito').html("");
		//$('#lstProvincia').focus();
	});
	
	//carga distritos
	$('#lstProvincia').change(function(){
		//alert('pp');
		cargaDistritos();
		var prov=$('#lstProvincia').val();
		if (prov=='') {$('#lstDistrito').html("");};
		//$('#lstDistrito').focus();
	});

	$('#txtcompany').autocomplete({
		source: "/finances/companies/autocomplete",
		minLength: 1,
		select: function(event, ui){
			var cod=ui.item.id;
			$('#company_id').val(cod);
		}
	});


	//carga unidades
	$('#lstUnitTypes').change(function(){ loadUnits(); });
	//carga subcategorias
	$('#lstCategories').change(function(){ 
		loadSubCategories(); 
	});

	//pasar elementos entre dos selectores multiples
	$('.pasar').click(function() { return !$('#origen option:selected').remove().appendTo('#destino'); });  
	$('.quitar').click(function() { return !$('#destino option:selected').remove().appendTo('#origen'); });
	$('.pasartodos').click(function() { $('#origen option:visible').each(function() { $(this).remove().appendTo('#destino'); }); });
	$('.quitartodos').click(function() { $('#destino option:visible').each(function() { $(this).remove().appendTo('#origen'); }); });
	$('#submit-role').click(function() { $('#destino option').prop('selected', 'selected'); });
	$('#groups').change(function(){
		var clasegrupo = $('#groups').val();
		$('.groupx').hide();
		$('.group_'+clasegrupo).show();
		if (clasegrupo=="") {$('.groupx').show();};
	})

	//focus al primer input
	$('form:first *:input[type!=hidden]:first').focus();

	//asignar valor por defecto de fecha
	//****if ($('#date').val()=='') { $('#date').val(new Date.today().toString('yyyy-MM-dd')); };
	//if ($('#birth').val()=='') { $('#birth').val(new Date.today().toString('yyyy-MM-dd')); };
	//formatea fechas
	if (!($('.message-errors').length)) {
		$.each($(".date"),function(index,contenido){
			if (contenido.tagName=='INPUT') {
				if(contenido.value!=''){ $(this).val(new Date.parse(contenido.value).toString('dd/MM/yyyy')); };
			} else{
				if(contenido.innerHTML!=''){ $(this).text(new Date.parse(contenido.innerHTML).toString('dd/MM/yyyy')); };
			};
		});
	}

	//Alista datepicker
	$.datepicker.regional['es'] = {
		closeText: 'Cerrar',
		prevText: '<Ant',
		nextText: 'Sig>',
		currentText: 'Hoy',
		monthNames: ['Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio', 'Julio', 'Agosto', 'Septiembre', 'Octubre', 'Noviembre', 'Diciembre'],
		monthNamesShort: ['Ene','Feb','Mar','Abr', 'May','Jun','Jul','Ago','Sep', 'Oct','Nov','Dic'],
		dayNames: ['Domingo', 'Lunes', 'Martes', 'Miércoles', 'Jueves', 'Viernes', 'Sábado'],
		dayNamesShort: ['Dom','Lun','Mar','Mié','Juv','Vie','Sáb'],
		dayNamesMin: ['Do','Lu','Ma','Mi','Ju','Vi','Sá'],
		weekHeader: 'Sm',
		dateFormat: 'dd/mm/yy',
		firstDay: 1,
		isRTL: false,
		showMonthAfterYear: false,
		yearSuffix: ''
	};
	$.datepicker.setDefaults($.datepicker.regional['es']);

	$('#date').datepicker({ format: "dd/mm/yyyy", language: 'es', autoclose: true });
	$('#birth').datepicker({ format: "dd/mm/yyyy", language: 'es', autoclose: true });
});
	
/*cargar provincias*/
function cargaProvincias(){
	var idDepartamento = $('#lstDepartamento option:selected').val();
	var page ="/listarProvincias/" + idDepartamento;
	if(idDepartamento != ''){
		$.get(page, function(data){
			$('#lstProvincia').empty();
			$('#lstProvincia').append("<option value=''>SELECCIONAR</option>");
			$.each(data, function (index, ProvinciaObj) {
				$('#lstProvincia').append("<option value='"+ProvinciaObj.provincia+"'>"+ProvinciaObj.provincia+"</option>");
			});
			
		});
	}
}

/*cargar distritos*/
function cargaDistritos(){
	var idDepartamento = $('#lstDepartamento option:selected').val();
	var rel=$('#lstProvincia option:selected').val();
	var page = "/listarDistritos/" + idDepartamento + "/" +rel;
	if(rel !=''){
		$.get(page, function(data){
			console.log(data);
			$('#lstDistrito').empty();
			$('#lstDistrito').append("<option value=''>SELECCIONAR</option>");
			$.each(data, function (index, DistritoObj) {
				$('#lstDistrito').append("<option value='"+DistritoObj.id+"'>"+DistritoObj.distrito+"</option>");
			});
			
		});
	}
}
/*cargar unidades*/
function loadUnits(){
	var unit_type_id = $('#lstUnitTypes option:selected').val();
	var page = "/listUnits/" + unit_type_id;
	if(unit_type_id !=''){
		$.get(page, function(data){
			$('#lstUnit').empty();
			$('#lstUnit').append("<option value=''>SELECCIONAR</option>");
			$.each(data, function (index, Obj) {
				$('#lstUnit').append("<option value='"+Obj.id+"'>"+Obj.name+"</option>");
			});
			
		});
	} else {
		$('#lstUnit').html("");
	};
}
/*cargar subcategorias*/
function loadSubCategories(){
	var category_id = $('#lstCategories option:selected').val();
	var page = "/listSubCategories/" + category_id;
	if(category_id !=''){
		$.get(page, function(data){
			$('#lstSubCategories').empty();
			$('#lstSubCategories').append("<option value=''>SELECCIONAR</option>");
			$.each(data, function (index, Obj) {
				$('#lstSubCategories').append("<option value='"+Obj.id+"'>"+Obj.name+"</option>");
			});
			
		});
	} else {
		$('#lstSubCategories').html("");
	};
}
/*cargar modelos*/
function cargaModelos(){
	var id = $('#brand_id option:selected').val();
	var page = "/listarModelos/" + id;
	if(id !=''){
		$.get(page, function(data){
			console.log(data);
			$('#model_id').empty();
			$('#model_id').append("<option value=''>SELECCIONAR</option>");
			$.each(data, function (index, ModelObj) {
				$('#model_id').append("<option value='"+ModelObj.id+"'>"+ModelObj.name+"</option>");
			});
			
		});
	}
}
function toDate1 (fecha) {
	var reggie = /(\d{4})-(\d{2})-(\d{2})/;
	var dateArray = reggie.exec(fecha);
	var dateObject = new Date(
		(+dateArray[1]),
		(+dateArray[2])-1, // Careful, month starts at 0!
		(+dateArray[3])
	); 
	return dateObject;
}
function toDate2 (fecha) {
	var reggie = /(\d{4})-(\d{2})-(\d{2}) (\d{2}):(\d{2}):(\d{2})/;
	var dateArray = reggie.exec(fecha);
	var dateObject = new Date(
		(+dateArray[1]),
		(+dateArray[2])-1, // Careful, month starts at 0!
		(+dateArray[3])
		(+dateArray[4]),
		(+dateArray[5]),
		(+dateArray[6])
	); 
	return dateObject;
}
function myRound(value, places) {
	var multiplier = Math.pow(10, places);
	return (Math.round(value * multiplier) / multiplier);
}
function arrayToHidden (dato, name, item) {
	var data = "";
	$.each(dato, function(index, value){
		//data = data + "<input type=\"hidden\" name=\""+name+"["+item+"]["+index+"]\" value=\""+value+"\" class=\"data-"+index"\">";
	});
	return data;
}
function generateTds (table) {
	var data = "";
	$.each(table, function(index,Obj){
		data = data+"<td class=\""+Obj.class+"\">"+Obj.value+"</td>";
	});
	return data;
}
function generateBtns () {
	return "<td><a href=\"#\" class=\"btn-edit-detail btn btn-primary btn-xs\"><span class=\"glyphicon glyphicon-pencil\" aria-hidden=\"true\"></span> Editar</a> <a href=\"#\" class=\"btn-delete-detail btn btn-danger btn-xs\"><span class=\"glyphicon glyphicon-trash\" aria-hidden=\"true\"></span> Eliminar</a></td>";
}