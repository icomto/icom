/** jquery.jSuggest.1.0.js **/

/* Copyright (c) 2008 Kean Loong Tan http://www.gimiti.com/kltan
 * Licensed under the MIT (http://www.opensource.org/licenses/mit-license.php)
 * Copyright notice and license must remain intact for legal use
 * jSuggest
 * Version: 1.0 (May 26, 2008)
 * Requires: jQuery 1.2.6+
 */
(function($) {
	$.fn.jSuggest = function(options) {
		// merge users option with default options
		var opts = $.extend({}, $.fn.jSuggest.defaults, options);
		var ob='.main-menu-style-helper';
		var textBox = this;
		var textVal = textBox.value;
		var request = null;
		var requestTimeout = null;
		var cache=Array();
		var jC = $('<div id="jSuggestContainer"></div>').appendTo(ob).hide();
		
		function initSuggestContainer(data) {
			jC.children(' > ul').remove();
			if(!data){
				$('.jSuggestHover').removeClass('jSuggestHover');
				jC.hide();
				return;
			}
			
			$('#jSuggestContainer')
				.html(data)
				.css({
					position: "absolute",
					top: $(ob).offset().top + $(ob).outerHeight() + "px",
					left: $(textBox).offset().left,
					width: (opts.jCwidth ? opts.jCwidth : $(textBox).outerWidth()) + "px",
					opacity: opts.opacity,
					zIndex: opts.zindex
				})
				.show();
			$("#jSuggestContainer ul li")
				.bind("mouseover", function(){
					$('.jSuggestHover').removeClass('jSuggestHover');
					$(this).addClass('jSuggestHover');
					textVal = $(this).text();
				})
				.bind("mouseout", function(){
					$('.jSuggestHover').removeClass('jSuggestHover');
				})
				.click(function(){
					$('.jSuggestHover').removeClass('jSuggestHover');
					$(this).addClass('jSuggestHover');
					$(textBox).val(textVal);
					if(textVal){
						var frm=$(textBox).parent()[0];
						frm.onsubmit(frm);
					}
				});
			//$(".jSuggestLoading").hide();
		}
		function suggestQuery() {
			var pData='q='+$.trim(textBox.value);
			if(cache[pData]){
				try{if(request){request.abort();request=null}}catch(e){}
				initSuggestContainer(cache[pData]);
			}
			else {
				try{if(requestTimeout){clearTimeout(requestTimeout);requestTimeout=null;}}catch(e){}
				requestTimeout=setTimeout(function () {
					try{if(request){request.abort();request=null}}catch(e){}
					request=$.ajax({
						type:opts.type,
						url:opts.url,
						dataType:"html",
						data:pData,
						success:function(data){
							cache[pData] = data;
							initSuggestContainer(data);
						}
					});
				}, opts.delay);
			}
		}
		function suggestHandler(t,e) {
			textBox = t;
			textVal = textBox.value;
			
			if (textVal.length < opts.minchar || textVal=='Suche...' || textVal=='Search...' || opts.filterFunction() == true) {
				$('.jSuggestHover').removeClass('jSuggestHover');
				jC.hide();
				return false;
			}
			
			// if escape key
			if (e.keyCode == 27 ) {
				$(".jSuggestHover").removeClass('jSuggestHover');
				jC.hide();
			}
			
			// if enter key
			else if (e.keyCode == 13 ) {
				if ($('.jSuggestHover').length == 1){
					$(textBox).val($('.jSuggestHover').text());
				}
				jC.hide();
			}
			
			// if down arrow
			else if (e.keyCode == 40) {
				// if any suggestion is highlighted
				if ($('.jSuggestHover').length == 1) {
					if ($('.jSuggestHover').next().length) {
						$('.jSuggestHover').next().addClass('jSuggestHover');
						$(".jSuggestHover:eq(0)").removeClass('jSuggestHover');
					}
					else $(".jSuggestHover").removeClass('jSuggestHover');
				}
				else {
					$("#jSuggestContainer ul li:first-child").addClass('jSuggestHover');
				}
			}
			
			// if up arrow
			else if (e.keyCode == 38) {
				// if any suggestion is highlighted
				if ($('.jSuggestHover').length == 1 ) {
					if ($('.jSuggestHover').prev().length) {
						$('.jSuggestHover').prev().addClass('jSuggestHover');
						$(".jSuggestHover:eq(1)").removeClass('jSuggestHover');
					}
					// if is first child
					else {
						$('.jSuggestHover').removeClass('jSuggestHover');
					}
				}
				else
					$("#jSuggestContainer ul li:last-child").addClass('jSuggestHover');
			}
			
			else if (e.keyCode == 39 || e.keyCode == 37 || e.keyCode == 36 || e.keyCode == 35) 0;
			
			// new query detected
			else{
				//if(!$(".jSuggestLoading").length)
					//$('<div class="jSuggestLoading"><img src="'+opts.loadingImg+'" style="Vertial-Align: middle;"> '+ opts.loadingText+'</div>').prependTo("#jSuggestContainer");
				
				//$(".jSuggestLoading").show();
				//jC.hide().find('ul').remove();
				suggestQuery();
			}
			return false;
		}
		
		$(this).bind("keyup click", function(e){
			return suggestHandler(this,e);
		});
		$(document).bind("click", function(){
			jC.hide();
		});

	};
	
	$.fn.jSuggest.defaults = {
		minchar: 1,
		opacity: 1.0,
		zindex: 20000,
		delay: 2500,
		//loadingImg: 'ajax-loader.gif',
		//loadingText: 'Loading...',
		url: "",
		type: "GET",
		data: "",
		filterFunction: function() { }
	};
})(jQuery);
