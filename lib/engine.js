var currentRequest = false;
var ildd = {
	def:{
		track:false,positionBy:'element',extraClass:'dropdown',top:18,left:0,initDelay:200,exitDelay:600,bodyFn:'ul',
		bodyHandler:function(s){
			var a,d='';
			for(var i=1;;i++)
				if(!(a=this.getAttribute('data-dd-item-'+i)))break;
				else d+='<li>'+a+'</li>';
			return '<ul>'+d+'</ul>';
		}
	},
	menuRadio:{
		track:false,positionBy:'element',extraClass:'dropdown',top:24,left:-12,initDelay:200,exitDelay:600,bodyFn:'ul',
		bodyHandler:function(s){
			return '<ul><li>'+this.getAttribute('data-radio')+'</li></ul>';
		}
	},
	menuForum:{
		track:false,
		positionBy:'element',
		extraClass:'dropdown',
		top:0,
		left:160,
		initDelay:400,
		jumpDelay:50,
		exitDelay:800
	},
	user:{
		track:false,
		positionBy:'element',
		extraClass:'dropdown',
		top:15,
		left:10,
		initDelay:800,
		exitDelay:300,
		bodyHandler:function(s){
			return user_tooltips[this.getAttribute('data-dd-user-id')];
		}
	},
	mmchl:{
		track:false,
		positionBy:'element',
		extraClass:'dropdown',
		top:35,
		initDelay:300,
		exitDelay:800
	},
	dragDrop:{
		track:false,
		positionBy:'absolute',
		extraClass:'dropdown',
		top:20,
		left:20,
		initDelay:400,
		jumpDelay:50,
		exitDelay:-1,
		dragDropElement:'.dragDrop',
		hideOnInnerClick:false,
		hideOnOuterClick:false
	}
};

var serializeArrayPost = function(a) {
	var r='';
	for(k in a){
		if(typeof a[k]=='object')r+=serializeArrayPostSub(String(k), a[k]);
		else r+='&'+escape(String(k))+'='+escape(String(a[k]));
	}
	return r;
}
var serializeArrayPostSub = function(k, a) {
	var r = '', k3 = '';
	for(k2 in a){
		//k3=k+'['+String(k2)+']';
		k3 = k + '/' + String(k2);
		if(typeof a[k2] == 'object') r += '&' + serializeArrayPostSub(k3, a[k2]);
		else r += '&' + escape(k3) + '=' + escape(String(a[k2]));
	}
	return r;
}



var handleJson = function(d) {
	for(var i = 0; i < d.s1.length; i++) {
		try {
			eval(d.s1[i]);
		}
		catch(e) {
			reportAjaxError('s^1: ' + e + '\n' + d.s1[i]);
		}
	}
	if(d.title) document.title = d['title'];
	if(d.e) {
		for(var e in d.e){
			if(d.e[e]==null) continue;
			var ec = e.substr(0,1);
			if(ec == '#' || ec == '.' || ec == '[') ec = e;
			else ec = '#' + e;
			if(typeof d.e[e] == 'object'){
				switch(d.e[e].fn) {
				case 1:
					for(var i = 0; i < d.e[e].d.length; i++) $(ec).append(d.e[e].d[i]);
					break;
				case 2:
					for(var i = 0; i < d.e[e].d.length; i++) $(ec).prepend(d.e[e].d[i]);
					break;
				case 3:
					for(var i = 0; i < d.e[e].d.length; i++) $(ec).before(d.e[e].d[i]);
					break;
				case 4:
					for(var i = 0; i < d.e[e].d.length; i++) $(ec).after(d.e[e].d[i]);
					break;
				}
			}
			else if(e == '__obj__') {
				$(arguments[1]).replaceWith(d.e[e]);
			}
			else {
				var ec = e.substr(0,1);
				if(ec == '#' || ec == '.' || ec == '[') $(e).html(d.e[e]);
				else $('#'+e).html(d.e[e]);
			}
		}
		//reportAjaxError(x.toString());
	}
	if(d.r) {
		for(var e in d.r){
			if(d.r[e]==null) continue;
			if(e == '__obj__') {
				$(arguments[1]).replaceWith(d.r[e]);
			}
			else {
				var ec = e.substr(0,1);
				if(ec == '#' || ec == '.' || ec == '[') $(e).replaceWith(d.r[e]);
				else $('#'+e).replaceWith(d.r[e]);
			}
		}
		//reportAjaxError(x.toString());
	}
	for(var i = 0; i < d.s.length; i++) {
		try {
			eval(d.s[i]);
		}
		catch(e) {
			reportAjaxError('s: ' + e + '\n' + d.s[i]);
		}
	}
}

var lastAjaxUpdate = new Date().getTime();
var ajaxUpdate = function() {
	var ts = new Date().getTime();
	if((!currentRequest && lastAjaxUpdate + ajaxUpdateInterval < ts) || lastAjaxUpdate + ajaxUpdateInterval + 60 < ts) iC();
}



//obj, data|target_obj|~parent_html, data
var iC_confirm = function(msg, obj, data, args, useHistory, successCb) {
	return confirm(msg) ? iC(obj, data, args, useHistory, successCb) : false;
}
var iC = function(obj, data, args, useHistory, successCb) {
	currentRequest = true;

	lastAjaxUpdate = new Date().getTime();

	var opts = { obj: null, data: '', href: '' };

	if(obj) {
		opts.obj = $(obj);
		if(!opts.obj.length) opts.obj = null;
	}

	switch(typeof obj) {
	case 'object':
		if(obj.action) opts.href = obj.action;
		else if(obj.href) opts.href = obj.href;
		opts.data += '&' + $(obj).serialize();
		if($(obj).attr('name')) opts.data += '&' + escape($(obj).attr('name')) + '=' + escape($(obj).attr('value'));
		break;
	case 'string':
		if(m = String(obj).match(/^~(.*)$/)) {
			opts.obj = $(obj).parents(m[1]);
			if(!opts.obj.length) opts.obj = null;
			else if(opts.obj.attr('name')) opts.data += '&' + escape(opts.obj.attr('name')) + '=' + escape(opts.obj.attr('value'));
		}
		else if(m = String(obj).match(/^([#\.].*)$/)) {
			opts.obj = $(m[1]);
			if(!opts.obj.length) opts.obj = null;
			else if(opts.obj.attr('name')) opts.data += '&' + escape(opts.obj.attr('name')) + '=' + escape(opts.obj.attr('value'));
		}
		else opts.data += '&' + obj;
		break;
	}

	switch(typeof data) {
	case 'string':
		if(m = String(data).match(/^~(.*)$/)) {
			opts.obj = $(obj).parents(m[1]);
			is_obj = true;
		}
		else if(m = String(data).match(/^([#\.].*)$/)) {
			opts.obj = $(m[1]);
			is_obj = true;
		}
		else {
			opts.data += '&' + data;
			is_obj = false;
		}
		if(is_obj) {
			if(!opts.obj.length) opts.obj = null;
			else if(opts.obj.attr('name')) opts.data += '&' + escape(opts.obj.attr('name')) + '=' + escape(opts.obj.attr('value'));
		}
		break;
	case 'object':
		opts.obj = $(data);
		if(!opts.obj.length) opts.obj = null;
		else if(opts.obj.attr('name')) opts.data += '&' + escape(opts.obj.attr('name')) + '=' + escape(opts.obj.attr('value'));
		break;
	case 'null':
		this.obj = null;
		break;
	}

	var myrtd = $.extend({}, ilrtd);
	opts.mybackup = {};

	for(mod in myrtd.imodules) {
		if(imodule_conditions[mod]) {
			backup = imodule_conditions[mod](myrtd.imodules[mod]);
			if(backup) opts.mybackup[mod] = backup;
		}
	}

	myrtd.current_location = String(document.location);

	if(args) opts.data += '&' + args;

	opts.successCb = successCb;
	opts.useHistory = useHistory;

	if(useHistory && history && history.pushState) {
		myrtd.html5_history = 1;
		opts.href_history = opts.href;
	}

	opts.data += serializeArrayPost(myrtd);
	opts.data += '&_ajax=1';

	opts.id = (i = String(opts.href).indexOf('#')) != -1 ? String(opts.href).substr(i + 1) : null;
	opts.href = '/'+ilrtd.current_language + '/ajax/' + String(opts.href).replace(/^https?:\/\/[^\/]+/i, '').replace(/^\/(de|en)\//, '/').replace(/^\/+/, '');

	if(typeof currentRequest === 'object') {
		currentRequest.abort();
		$('.ajax-loader-ic').remove();
	}
	currentRequest = $.ajax({
		type: 'POST',
		url: opts.href,
		data: opts.data,
		dataType: 'html',
		async: true,
		beforeSend: function() {
			if(opts.obj) {
				var p = opts.obj.position();
				opts.loader = $('<div class="ajax-loader-ic"/>').
					css({
						top: p.top - 2,
						left: p.left - 2,
						width: opts.obj.outerWidth() + 4,
						height: opts.obj.outerHeight() + 4,
						display: 'block'
					}).appendTo('body');
				opts.loader.hide().fadeTo(300, opts.loader.css('opacity'))
				//$(opts.obj).css({opacity: 0.3});
			}
		},
		success: function(d) {
			try {
				if(typeof d == 'undefined' || !d) {
					if(opts.loader) opts.loader.remove();
					return;
				}

				var e = null;
				if(e = String(d).match(/^([^\{]+)(\{.+)?$/)) {
					reportAjaxError('runtime-error-1:<br>' + e[1]);
					d = e[2];
				}
				if(e = String(d).match(/^(.+\})?([^\}]+)$/)) {
					reportAjaxError('runtime-error-2:<br>' + e[1]);
					d = e[2];
				}

				eval('d = ' + d);

				handleJson(d, opts.obj);
				if(opts.useHistory && opts.href_history && history && history.pushState)
					history.pushState(opts.href_history, d.title || null, opts.href_history);

				if(opts.mybackup) ilrtd.imodules = $.extend(true, opts.mybackup, ilrtd.imodules);

				initAjax();
			}
			catch(e) {
				if(opts.loader) opts.loader.remove();
				reportAjaxError('ajax.successHandler: '+e+'\n\n'+d);
			}

			if(opts.loader && (typeof d.keep_loading == 'undefined' || d.keep_loading === false || d.keep_loading == 'false'))
				opts.loader.remove();
			if(opts.id) {
				try {
					if(opts.id == '0') window.scrollTo(0, 0);
					else {
						var e = $('#' + opts.id);
						//if(!e.is(':visible'))
							window.scrollTo(0, e.position().top);
					}
				}catch(e) {
				}
			}

			currentRequest = false;
			if(opts.successCb) opts.successCb(opts);
		},
		error: function(x, o, e){
			if(opts.loader) opts.loader.remove();
			switch(x.status){
			case '0': txt = 'Connection timed out'; break;
			case '404': txt = '404 Not Found'; break;
			case '500': txt = '500 Internal Server Error'; break;
			case '501': txt = '501 Not Implemented'; break;
			case '502': txt = '502 Bad Gateway'; break;
			case '503': txt = '503 Service Unavailable'; break;
			case '504': txt = '504 Gateway Timeout'; break;
			case '505': txt = '505 HTTP Version Not Supported'; break;
			default: txt = 'Unknown: ' + x.status; break;
			}
			reportAjaxError(x.status + ' ' + x.statusText + ' ' + txt);
			currentRequest = false;
		}
	});
	return false;
}



function initAjax() {
	$('.image:not(.lb-i)').addClass('lb-i').lightBox();
	$('.bbcodeedit:not(.bbe-i)').addClass('bbe-i').markItUp(myBbcodeSettings);
	$('.wikiedit:not(.we-i)').addClass('we-i').markItUp(myWikiSettings);

	$('.im_chat-pages:not(.ic-p)').addClass('ic-p').each(function() {
		var form = $(this).find('form.im-chat-page-form'),
			page = form.find('input.im-chat-page');
		$(this).find('a').addClass('lb-anc').click(function(){
			if(m = String(this.href).match(/\/([0-9]+)\/$/)) {
				page.val(m[1]);
				form.submit();
				return false;
			}
		});
	});

	try {
		$('[data-tooltip]:not(.tt-i)').addClass('tt-i').tooltip({
			extraClass:'default-tooltip',
			jumpDelay:0,
			bodyHandler:function(){
				return this.getAttribute('data-tooltip');
			}
		});
	}
	catch(e) {
	}
	try {
		$('[data-dd]:not(.dd-i)').addClass('dd-i').each(function(){
			var s=this.getAttribute('data-dd');
			s=(s?String(s):false);
			if(s)try{eval('var s='+s+';');}catch(e){reportAjaxError(''+e)}
			else var s={};
			$(this).tooltip($.extend({
				bodyHandler:function(s){return this.getAttribute('data-dd-body');},
				readyHandler:function(){initAjax();}
			},s))
		});
	}
	catch(e) {
	}

	$('a:not(.lb-anc):not([target]):not([name]):not([onclick]):not([rel]):not([href^="javascript:"])[href!=""]')
		.addClass('lb-anc')
		.click(function(){
			if(typeof event !== 'undefined' && (event.ctrlKey || event.shiftKey || event.altKey)) return true;
			return iC(this, '#Module', 'imodules/ajax/action=select_module', true, function(opts) {
				if(!opts.id) window.scrollTo(0, 0);
			});
		});

	updateTimestamps();
}


var mslide = function(self, name) {
	var i = $(self).next();
	$(self).find('input[name$=display]').attr('value', i[0].style.display == 'none' ? 'yes' : 'no');
	iC(self, null);
	i.slideToggle(150);
	return false;
}



var updateTimestamps = function() {
	$('[data-timestamp]').each(function(){
		var ts=this.getAttribute('data-timestamp-j');
		if(!ts){
			ts=Math.round(new Date().getTime()/1000);
			this.setAttribute('data-timestamp-j',ts);
		}
		ts=(ts-Math.round(new Date().getTime()/1000))-this.getAttribute('data-timestamp');
		var r=ts<0;
		if(r)ts=-ts;
		len=[60,60,24,7,4.35,12];
		for(var i=0;len.length>i&&ts>=len[i];i++)ts/=len[i];
		var mode=(r?'MODE_1':'MODE_2');
		if(ts==0)ts=LANG_TIME[mode][0];
		else{
			ts=Math.round(ts);
			if(ts==1)ts=String(LANG_TIME.ONE[i])+' '+LANG_TIME.SINGULAR[i];
			else ts=String(ts)+' '+LANG_TIME.PLURAL[i];
			ts=LANG_TIME[mode][r?1:2].replace(/%s/,ts);
		}
		if(this.innerHTML!=ts)this.innerHTML=ts;
	});
}


function chat_base_submit(t, txt_default, txt_no_msg, txt_shout){
	var th = $(t);
	var txt = th.find('textarea');
	if(!txt.length) txt = th.find('input[type=text]');
	txt = txt[0];
	if(txt.value.length <= 0 || txt.value == txt_default) alert(txt_no_msg);
	else {
		iC(t);
		txt.value = '';
		th.find('[type=submit]').html(txt_shout);
	}
	return false;
}
im_chat_submit = chat_base_submit;


function im_tabs_switch(self, mod) {
	if(typeof event !== 'undefined' && (event.ctrlKey || event.shiftKey || event.altKey)) return true;
	s = $(self).parent().parent();
	return iC(self, '#' + mod + '__content', undefined, true, function(opts) {
		s.parent().find('div').removeClass('active').addClass('normal');
		s.addClass('active');
	});
}


var imodule_conditions = {
	poll: function(args) {
		if(!args.IDLE) return;
		var num_backups = 0, backup = {IDLE: {}};
		for(var poll_id in args.IDLE) {
			var e = $('.poll-item-' + poll_id);
			if(!e.length) args.IDLE[poll_id] = null;
			else if(!e.is(':visible')) {
				num_backups++;
				backup.IDLE[poll_id] = args.IDLE[poll_id];
				args.IDLE[poll_id] = null;
			}
		}
		return num_backups ? backup : false;
	},

	forum: function(args) {
		if(!args.IDLE) return;
		var num_backups = 0, backup = {IDLE: {}};
		for(var namespace in args.IDLE) {
			for(var limit in args.IDLE[namespace]) {
				var e = $('#IM_MENU_forum_' + namespace + '_' + limit);
				if(!e.length) {
					args.IDLE[namespace][limit] = null;
				}
				else if(!e.is(':visible')) {
					num_backups++;
					if(!backup.IDLE[namespace]) backup.IDLE[namespace] = {};
					backup.IDLE[namespace][limit] = args.IDLE[namespace][limit];
					args.IDLE[namespace][limit] = null;
				}
			}
		}
		return num_backups ? backup : false;
	},

	online_users: function(args) {
		if(!args.IDLE) return;
		var num_backups = 0, backup = {IDLE: {}};
		if(args.IDLE.menu) {
			var e = $('#IM_MENU_online_users');
			if(!e.length) args.IDLE.menu = null;
			else if(!e.is(':visible')) {
				num_backups++;
				backup.IDLE.menu = args.IDLE.menu;
				args.IDLE.menu = null;
			}
		}
		return num_backups ? backup : false;
	},

	radio: function(args) {
		if(!args.IDLE) return;
		var num_backups = 0, backup = {IDLE: {}};
		if(args.IDLE.menu) {
			var e = $('#IM_MENU_radio');
			if(!e.length) args.IDLE.menu = null;
			else if(!e.is(':visible')) {
				num_backups++;
				backup.IDLE.menu = args.IDLE.menu;
				args.IDLE.menu = null;
			}
		}
		return num_backups ? backup : false;
	},

	/*chat: function(args) {
		if(!args.IDLE) return;
		var num_backups = 0, backup = {IDLE: {}};
		for(var chat_id in args.IDLE) {
			if(args.IDLE[chat_id].menu) {
				var e = $('#IM_MENU_chat_' + chat_id);
				if(!e.length) {
					if(args.IDLE[chat_id].module) args.IDLE[chat_id].menu = null;
					else args.IDLE[chat_id] = null;
				}
				else if(!e.is(':visible')) {
					num_backups++;
					if(args.IDLE[chat_id].module) {
						if(!backup.IDLE[chat_id]) backup.IDLE[chat_id] = {};
						backup.IDLE[chat_id].menu = args.IDLE[chat_id].menu;
						args.IDLE[chat_id].menu = null;
					}
					else {
						$.extend(backup.IDLE[chat_id], args.IDLE[chat_id]);
						args.IDLE[chat_id] = null;
					}
				}
			}
		}
		return num_backups ? backup : false;
	},

	shoutbox: function(args) {
		if(!args.IDLE) return;
		var num_backups = 0, backup = {IDLE: {}};
		for(var chat_id in args.IDLE) {
			if(args.IDLE[chat_id].menu) {
				var e = $('#IM_MENU_shoutbox');
				if(!e.length) {
					if(args.IDLE[chat_id].module) args.IDLE[chat_id].menu = null;
					else args.IDLE[chat_id] = null;
				}
				else if(!e.is(':visible')) {
					num_backups++;
					if(args.IDLE[chat_id].module) {
						if(!backup.IDLE[chat_id]) backup.IDLE[chat_id] = {};
						backup.IDLE[chat_id].menu = args.IDLE[chat_id].menu;
						args.IDLE[chat_id].menu = null;
					}
					else {
						$.extend(backup.IDLE[chat_id], args.IDLE[chat_id]);
						args.IDLE[chat_id] = null;
					}
				}
			}
		}
		return num_backups ? backup : false;
	},*/

	__chat2_prototype: function(args, imodule_name) {
		if(!args.IDLE) return;
		var num_backups = 0, backup = {IDLE: {chat: {}}}, numNamespacesAlive = 0;;
		for(var namespace in args.IDLE) {
			if(!String(namespace).match(/^im_chat/)) continue;
			var numChatsAlive = 0;
			for(var chat_id in args.IDLE[namespace]) {
				var numPlacesAlive = 0;
				for(var place in args.IDLE[namespace][chat_id]) {
					if(place == 'last_id') continue;
					var e = $('#'+imodule_name+'_'+namespace+chat_id+place);
					if(!e.length) {
						args.IDLE[namespace][chat_id][place] = null;
					}
					/*else if(!e.is(':visible')) {
						num_backups++;
						if(args.IDLE[namespace][chat_id][place]) {
							if(!backup.IDLE[namespace][chat_id]) backup.IDLE[namespace][chat_id] = {};
							backup.IDLE[namespace][chat_id][place] = args.IDLE[namespace][chat_id][place];
							args.IDLE[namespace][chat_id][place] = null;
						}
						else {
							$.extend(backup.IDLE[namespace][chat_id], args.IDLE[namespace][chat_id]);
							args.IDLE[namespace][chat_id] = null;
						}
					}*/
					else {
						numPlacesAlive++;
					}
				}
				if(!numPlacesAlive) {
					args.IDLE[namespace][chat_id] = null;
				}
				else {
					numChatsAlive++;
				}
			}
			if(!numChatsAlive) {
				args.IDLE[namespace] = null;
			}
			else {
				numNamespacesAlive++;
			}
		}
		if(!numNamespacesAlive) {
			args.IDLE = null;
		}
		return num_backups ? backup : false;
	}
};
imodule_conditions.shoutbox = function(args) { return imodule_conditions.__chat2_prototype(args, 'shoutbox'); }
imodule_conditions.chat = function(args) { return imodule_conditions.__chat2_prototype(args, 'chat'); }
imodule_conditions.i = function(args) { return imodule_conditions.__chat2_prototype(args, 'i'); }







function dump(arr,level) {
	var dumped_text = "";
	if(!level) level = 0;

	var level_padding = "";
	for(var j=0;j<level+1;j++) level_padding += "    ";

	if(typeof(arr) == 'object') {
		for(var item in arr) {
			var value = arr[item];

			if(typeof(value) == 'object') {
				dumped_text += level_padding + "'" + item + "' ...\n";
				dumped_text += dump(value,level+1);
			} else {
				dumped_text += level_padding + "'" + item + "' => \"" + value + "\"\n";
			}
		}
	} else {
		dumped_text = "===>"+arr+"<===("+typeof(arr)+")";
	}
	return dumped_text;
}





$(function(){
	if(history && history.pushState)
		$(window).bind('popstate', function(e) {
			if(e.originalEvent.state)
				//iC({href: e.originalEvent.state}, '#Module', 'imodules/ajax/action=select_module', false);
				document.location = e.originalEvent.state;
		});

	$('.sf-menu').superfish({delay:400,delayOver:350,speed:200,dropShadows:false,disableHI:true});
	initAjax();
	updateTimestamps();
	setInterval(updateTimestamps,5000);
});
