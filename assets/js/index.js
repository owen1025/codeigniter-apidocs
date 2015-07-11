$(document).ready(function(){
	$('button[type="submit"]').click(function(){
		ajax_get($(this), 'localhost://method_name');
	})
})

function ajax_get(button, url){
	var api_parent = button.parents('section')
	
	$.ajax({
		'url': function(){
			$.each(api_parent.children('.parameter').children('.row'), function(index, row_val){
				$.each($(row_val).children('div'), function(div_index, div_val){
					var input_dom = $(div_val).children('input[type="text"]')
					var parameter_val = input_dom.val()
					if (parameter_val != undefined){
						url += (index ? '&' : '?') + input_dom.parent().siblings('div').text() + '=' + parameter_val
					}
				})
			})
			return url
		}(),
	})
}