$(document).ready(function(){
	$('.btnAddFeature').click(function(e){
		var div = $(this).parent();
		addFeature(div);
	});
	$('.edit').click(function(e){
		e.preventDefault();
		company_id = $('#company_id').val();
		if (company_id > 0) {
			var url = $(this).attr('href').replace(':_ID', company_id);
			window.location = url;
		} else{
			$('#txtcompany').focus();
		}
	});
});
function addFeature (div) {
	var group = div.data('group');
	console.log(group);
	var html = htmlFeature(group);
	console.log(html);
	div.append(html);
}
function htmlFeature (group) {
	var html = '';
	var items = parseFloat($('#items').val()) + 1;
	$('#items').val(items);
	html = html + "<div class=\"feature\">";
	html = html + "<input type=\"hidden\" name=\"features["+items+"][feature_group_id]\" value=\"" + group + "\" class=\"feature_group_id\">";
	html = html + "<input type=\"text\" name=\"features["+items+"][name]\" placeholder=\"Nombre\" class=\"name\"> ";
	html = html + "<input type=\"text\" name=\"features["+items+"][value]\" placeholder=\"Valor\" class=\"value\">";
	html = html + "</div>";
	return html;
}