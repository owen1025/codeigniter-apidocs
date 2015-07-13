$(document).ready(function(){
	$('.api-box .urlparameter input[type="text"]').focusout(function(){
		var _this = $(this)
		var input_index = _this.parents('.urlparameter').find('input[type="text"]').index(_this)
		var url_val = _this.parents('.group').siblings('.endpoint')
		var method_name = _this.parents('.api-box').siblings('.method-name').text()

		var split_url = url_val.text().split(method_name + '/')
		split_url[0] += method_name
		split_url[1].split('/').forEach(function(element, index){
			split_url[0] += '/' + (index == input_index ? (_this.val() != '' ? _this.val() : _this.parent().siblings('div').text()) : element)
		})

		url_val.text(split_url[0])
	})

	$('button[type="submit"]').click(function(){
		var api_parent = $(this).parents('section')

		var ajax_config = {
			'url' : function(){
				var url = 'test'

				$.each(api_parent.children('.parameter').children('.row'), function(index, row_val){
					$.each($(row_val).children('div'), function(div_index, div_val){
						var input_dom = $(div_val).children('input[type="text"]')
						var parameter_val = input_dom.val()
						if (parameter_val != undefined){
							url += (index ? '&' : '?') + input_dom.parent().siblings('div').text() + '=' + parameter_val
						}
					})
				})
				console.log(url)
			}()
		}
		
		$.ajax(ajax_config)		
	})
})