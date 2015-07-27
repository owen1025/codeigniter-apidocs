const SCROLL_Y_CONTROL = 50

$(document).ready(function(){
	$('.highlight ol li').click(function(event){
		event.preventDefault()

		var _this = $(this);
		$('html, body').stop(true).animate({
			scrollTop: $('.' + _this.text()).offset().top - SCROLL_Y_CONTROL
		}, 500);

		$('.highlight ol .highlight').removeClass('highlight')
		_this.addClass('highlight')
	})

	$('li.collection').click(function(){
		var _this = $(this);

		if (_this.attr('class').indexOf('highlight') <= -1){
			$('li.collection.highlight').removeClass('highlight')
			_this.addClass('highlight')

			$.ajax({
				url : document.URL + '/get_api_detail/' + _this.find('label a').text(),
				type : 'GET',
				success : function(data){
					json_data_convert_tag(_this.find('label a').text(), data)
				}
			})
		}
	})

	$(document).on('keydown', '.api-box .urlparameter input[type="text"]', function(){
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

	$(document).on('click', 'button[type="submit"]', function(){
		var api_parent = $(this).parents('section')
		var call_type = api_parent.find('.method').attr('data-method')

		var ajax_config = {
			type : call_type,
			url : function(){
				var url = api_parent.find('.endpoint').text().replace(/\s/g, '')

				if (call_type == 'GET'){
					$.each(api_parent.children('.parameter').children('.row'), function(index, row_val){
						$.each($(row_val).children('div'), function(div_index, div_val){
							var input_dom = $(div_val).children('input[type="text"]')
							var parameter_val = input_dom.val()
							if (parameter_val != undefined){
								url += (index ? '&' : '?') + input_dom.parent().siblings('div').text() + '=' + parameter_val
							}
						})
					})
				}

				return url
			}(),
			// success: function(data, textStatus, request){


			// 	// var codewrap = $(".response pre.code-wrap")
			// 	// codewrap.html(_.escape(data))
			// 	// codewrap.removeClass('highlighter')
			// 	// parse_code_block()
			// 	// $('body').addClass('console-active')
			// },
			// complete : function(){
			// 	// alert(123)
			// }
		}

		ajax_config['data'] = ajax_data_binding(api_parent, 'parameter')
		ajax_config['headers'] = ajax_data_binding(api_parent, 'header')

		ajax_config['success'] = function(data, a, request){
			var request_data_str = '# ' + call_type + ' ' + ajax_config['url'] + '\n'

			for (parameter_key in ajax_config['data']){
				request_data_str += '? ' + parameter_key + '=' + ajax_config['data'][parameter_key] + '\n'
			}
			request_data_str += '\n'

			for (header_key in ajax_config['headers']){
				request_data_str += '+ ' + header_key + ':' + ajax_config['headers'][header_key] + '\n'
			}
			
			$('.request pre.code-wrap').text(request_data_str)
			$(".request pre.code-wrap").removeClass('highlighter')
			$('.response pre.code-wrap').text(data)
			$('.response pre.code-wrap').removeClass('highlighter')

			parse_code_block()
			$('body').addClass('console-active')
		}
		
		$.ajax(ajax_config)
	})

	$('.footer-header-area').click(function(){
		$('body').removeClass('console-active')
	})
})

function json_data_convert_tag(controller_name, data){
	var parent_tag = $('.main-header-area')
	var api_data = JSON.parse(data)
	var tag_str = ''

	api_data.item.forEach(function(api_val, index){
		if (!index){
			parent_tag.empty()
			tag_str += '<h1>' + controller_name + '</h1>'
		}

		tag_str += '<div class="api-wrap ' + api_val.method_name + '?>">' +
						'<h2 class="method-name">' + api_val.method_name + '</h2>' +
						'<blockquote>Vestibulum rutrum quam vitae fringilla tincidunt. Suspendisse nec tortor urna. Ut laoreet sodales nisi, quis iaculis nulla iaculis vitae. Donec sagittis faucibus lacus eget blandit. Mauris vitae ultricies metus, at condimentum nulla.</blockquote>' +
						'<section class="api-box">' +
							'<div class="method" data-method="' + api_val.call_type.toUpperCase() + '"></div>' +
							'<div class="endpoint">';

		url_parameter_str = ''
		api_val.url_parameter.forEach(function(urlparameter, index){
			url_parameter_str += '/{' + urlparameter + '}'
		})

		tag_str +=				api_data.base_url + controller_name + '/' + api_val.method_name + (url_parameter_str != '/' ? url_parameter_str : '') +
							'</div>'
		
		if (api_val.url_parameter.length){
			tag_str += 		'<div class="urlparameter group">' +
								'<label>URL Parameter</label>' 
			api_val.url_parameter.forEach(function(parameter_val, index){
				tag_str +=		'<div class="row">' +
									'<div class="col-lg-3"><p class="ico-circle-none"></p>{' + parameter_val + '}</div>' +
									'<div class="col-lg-9">' +
										'<input type="text" value="" />' +
									'</div>' +
								'</div>'
			})
			tag_str +=		'</div>'
		}

		if (api_val.header.length){
			tag_str += 		'<div class="header group">' +
								'<label>Header</label>' 
			api_val.header.forEach(function(header_val, index){
				tag_str +=		'<div class="row">' +
									'<div class="col-lg-3"><p class="ico-circle-none"></p>{' + header_val + '}</div>' +
									'<div class="col-lg-9">' +
										'<input type="text" value="" />' +
									'</div>' +
								'</div>'
			})
			tag_str +=		'</div>'
		}

		if (api_val.parameter.length){
			tag_str += 		'<div class="parameter group">' +
								'<label>Parameter</label>' 
			api_val.parameter.forEach(function(parameter_val, index){
				tag_str +=		'<div class="row">' +
									'<div class="col-lg-3"><p class="ico-circle-none"></p>{' + parameter_val + '}</div>' +
									'<div class="col-lg-9">' +
										'<input type="text" value="" />' +
									'</div>' +
								'</div>'
			})
			tag_str +=		'</div>'
		}



		tag_str	+=			'<div class="tryit row">' +
								'<div class="col-lg-12">' +
									'<button type="submit">Try it</button>' +
								'</div>' +
							'</div>' +
						'</section>' +
				   '</div>'
	})
	
	parent_tag.append(tag_str)
}

function ajax_data_binding(api_parent, group_name){
	var data_str = {}
	$.each(api_parent.find('.' + group_name).children('.row'), function(index, row){
		var row_div = $(row).children('div')
		data_str[row_div.eq(0).text()] = row_div.eq(1).children('input[type="text"]').val()
	})
	return data_str
}

function parse_code_block(){
	$("pre.code-wrap").each(function(){
		if ($(this).hasClass("highlighter"))
			return;

		var data = $(this).html();

		data = _.unescape(data).trim();

		// cross-hatch
		var crosshatch = /^(#[\s]+)(GET|POST|DELETE|UPDATE)([\s]{1,})([\S]+)([\s]*HTTP\/[\d.]+){0,1}/gm;
		data = data.replace(crosshatch, "<span class='cross-hatch'>$1$2</span>$3<span class='url'>$4</span><span class='cross-hatch'>$5</span>");

		// spacial-charactor
		var spacialcharactor = /^(\?)([\s]{1,})([\S]+=)([\S]+){0,}$/gm;
		data = data.replace(spacialcharactor, "<span class='spacial-charactor'>$1</span>$2<span class='parameter-key'>$3</span><span class='parameter-value'>$4</span>");

		// header
		var header = /^(\+)([\s]{1,})([\S]+:)[\s]{0,}([\S]+)$/gm;
		data = data.replace(header, "<span class='spacial-charactor'>$1</span>$2<span class='header-key'>$3</span><span class='header-value'>$4</span>");

		// Query Param
		var newlength = 0;
		var queryParam = /(((&){0,1}[\w]+=)([\w]+))/gm;
		while ((m = queryParam.exec(data)) !== null) {
			if (m.index === queryParam.lastIndex)
				queryParam.lastIndex ++;
			newlength += m[1].length;
		}

		if (data.length == newlength) {
			$(".response .description").html("query response");

			data = data.replace(queryParam, "<span class='query-key'>$2</span><span class='query-value'>$4</span>");

		// JSON
		} else if (data.indexOf("<?xml") === -1 && data.indexOf("<? xml") === -1) {
			try {
				odata = data;
				data = JSON.stringify(JSON.parse(data), null, 4);

				$(".response .description").html("json response");

				// brackets
				var brackets = /({|}|\(|\)|\[|\])/gm;
				data = data.replace(brackets, "<span class='brackets'>$1</span>");

				// numbers | strings
				var m;
				var lastindex = 0;
				var newdata = "";
				var numberstrings = /(("[ \t\S]*")|('[ \t\S]*')|([\d]+)){0,1}(:)([\s]{1,})((("[ \t\S]*")|('[ \t\S]*')|([\d]+|null))){0,1}/gm;
				while ((m = numberstrings.exec(data)) !== null) {
					if (m.index === numberstrings.lastIndex) {
				        numberstrings.lastIndex++;
				    }

				    newdata += data.substr(lastindex, m.index - lastindex);

					if (m[4])
						newdata += "<span class='number'>" + m[1] + m[5] + "</span>";
					else
						newdata += "<span class='string'>" + m[1] + m[5] + "</span>";

					newdata += m[6];

					if (m[7]) {
						if (m[11])
							newdata += "<span class='number'>" + m[8] + "</span>";
						else
							newdata += "<span class='string'>" + m[8] + "</span>";

						lastindex = m.index + m[0].length;
					} else {
						lastindex = m.index + m[0].length;
					}
				}
				data = newdata + data.substr(lastindex, data.length - lastindex)
				data = data.replace(/(,)/gm, "<span class='brackets'>$1</span>");
			} catch(e) {
				data = odata;
				$(".response .description").html("response");
			}

		// XML
		} else {
			$(".response .description").html("xml response");

			var m;
			var lastindex = 0;
			var newdata = "";
			var xml = /(<)([\/|?]{0,})([^>\s]+)(([\s]+[^>\s]+)*)(>)/gm;
			while ((m = xml.exec(data)) !== null) {
				if (m.index === xml.lastIndex) {
					xml.lastIndex++;
				}

				newdata += "<span class='xml-content'>" + data.substr(lastindex, m.index - lastindex) + "</span>";

				if (m[2] === '?') {
					newdata += "<span class='xml-block'>" + _.escape(m[0]) + "</span>";
				} else {
					newdata += "<span class='xml-tag-block'>" + _.escape(m[1] + m[2] + m[3]) + "</span>" + m[4].replace(/([\w]+=)(('[\w]')|("[\w]+"))/g, "<span class='xml-field'>$1</span><span class='number'>$2</span>") + "<span class='xml-tag-block'>" + _.escape(m[6]) + "</span>";
				}

				lastindex = m.index + m[0].length;
			}
			data = newdata + "<span class='xml-content'>" + data.substr(lastindex, data.length - lastindex) + "</span>";
		}

		$(this).addClass("highlighter");
		$(this).html(data);
	});
}








