$(document).ready(function(){
	$('.btnAddChecktem').click(function(e){
		var div = $(this).parent();
		addChecktem(div);
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