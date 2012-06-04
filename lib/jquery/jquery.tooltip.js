;$(function(){
	var h,cur=null,next=null,st=null,ct=null,showE=null,trackside='left';
	$.fn.extend({
		tooltip:function(s){
			s=$.extend({
				id:'tooltip',
				extraClass:'',
				initDelay:300,
				jumpDelay:-1,//-1=initDelay
				exitDelay:200,
				track:true,//works only with positionBy=mouse
				positionBy:'mouse',//mouse|element|absolute|fixed
				top:15,
				left:15,
				margin:15,
				bodyHandler:null,
				readyHandler:null,
				hideOnMouseOut:true,
				hideOnInnerClick:true,
				hideOnOuterClick:true,
				dragDropElement:''
			},s);
			if(s.jumpDelay==-1)s.jumpDelay=s.initDelay;
			if(!h)h=$('<div id="'+s.id+'"></div>')
				.appendTo(document.body)
				.hide()
				.mouseover(function(){if(ct)clearTimeout(ct);ct=null;});
			return this
				.each(function(){
					$.data(this,'tooltip',s);
					if(s.hideOnMouseOut)$(this).bind('mouseout',hide);
					if(s.hideOnInnerClick)$(this).bind('click',_hide);
				})
				.mouseover(show);
		}
	});
	function show(e){
		try{
		if(ct&&cur==this){
			clearTimeout(ct);
			ct=null;
		}
		if(cur==this||next==this)return;
		if(st){
			clearTimeout(st);
			st=null;
		}
		var s=$.data(this,'tooltip');
		if(!s.bodyHandler)return;
		st=null;
		next=this;
		if(!s.initDelay||(s.jumpDelay==0&&h.is(':visible')))_show(e);
		else {
			showE=e;
			st=setTimeout(_show,h.is(':visible')?s.jumpDelay:s.initDelay);
		}
		}catch(e){reportAjaxError(e)}
	}
	function _show(e){
		try{
		if(!next)return;
		cur=next;
		next=null;
		trackside='left';
		if(ct){
			clearTimeout(ct);
			ct=null;
		}
		st=null;
		var s=$.data(cur,'tooltip');
		if(s.hideOnMouseOut)h.bind('mouseout',hide);else h.unbind('mouseout',hide);
		if(s.hideOnInnerClick)h.bind('click',_hide);else h.unbind('click',_hide);
		if(s.hideOnOuterClick)$(document.body).bind('click',_hide);else $(document.body).unbind('click',_hide);
		h
			.html(s.bodyHandler.call(cur,s))
			.addClass(s.extraClass)
			.show();
		if(s.dragDropElement)$(s.dragDropElement,h).css({cursor:'move'}).bind('mousedown',initDrop);
		switch(s.positionBy){
		default:
			move(showE?showE:e);
			showE=null;
			if(s.track){
				h.bind('mousemove',move);
				$(document.body).bind('mousemove',move);
				s.moveTop=s.top;
				s.moveLeft=s.left;
			}
			else{
				h.unbind('mousemove',move);
				$(document.body).unbind('mousemove',move);
			}
			h.css({position:'absolute'});
			break;
		case 'element':
			var p=$(cur).position();
			h.css({position:'absolute',top:p.top+s.top,left:p.left+s.left});
			break;
		case 'absolute':
			h.css({position:'absolute',top:s.top,left:s.left});
			break;
		case 'fixed':
			h.css({position:'fixed',top:s.top,left:s.left});
			break;
		}
		if(s.readyHandler)s.readyHandler.call(cur);
		}catch(e){reportAjaxError('s:'+e)}
	}
	function initDrop(e){
		if(!e||!cur)return;
		var s=$.data(cur,'tooltip');
		if(!s)return;
		var p=h.position();
		s.moveTop=p.top-e.pageY;
		s.moveLeft=p.left-e.pageX;
		$(document.body).bind('mousemove',move)
						.bind('mouseup',exitDrop);
	}
	function exitDrop(e){
		$(document.body).unbind('mousemove',move)
						.unbind('mouseup',exitDrop);
	}
	function move(e){
		try{
		if(!cur)return;
		var s=$.data(cur,'tooltip');
		if(!s)return;
		var top=e.pageY+s.moveTop,
			left=e.pageX+s.moveLeft,
			height=h.height(),
			width=h.width(),
			bottom=top+height,
			right=left+width,
			min_top=$(window).scrollTop(),
			min_left=$(window).scrollLeft(),
			max_bottom=(min_top+$(window).height())-s.margin,
			max_right=(min_left+$(window).width())-s.margin;
		min_top+=s.margin;
		min_left+=s.margin;
		if(bottom>max_bottom){
			var x=height+Math.round(s.margin*2.25);
			if(top-x>min_top)top-=x;
		}
		if(right>max_right){
			if(trackside=='right'||(right-max_right>width*0.40)){
				left-=width+Math.round(s.margin*2.25);
				trackside='right';
			}
			else left-=right-max_right;
		}
		else trackside='left';
		if(left<min_left)left+=min_left-left;
		h.css({top:top,left:left})
		}catch(e){reportAjaxError('m:'+e)}
	}
	function hide(){
		try{
		if(this==next)next=null;
		if(ct)clearTimeout(ct);
		if(!cur)_hide();
		else{
			var s=$.data(cur,'tooltip');
			if(s&&s.exitDelay>0)ct=setTimeout(_hide,s.exitDelay);
			else if(!s||s.exitDelay==0)_hide();
		}
		}catch(e){reportAjaxError('h:'+e)}
	}
	function _hide(e){
		try{
		if(cur){
			var s=$.data(cur,'tooltip');
			if(s&&e&&s.hideOnOuterClick&&!s.hideOnInnerClick&&this==document.body){
				var p=h.position();
				if(e.pageY>p.top&&e.pageX>p.left&&e.pageY<p.top+h.height()&&e.pageX<p.left+h.width())return;
			}
		}
		else var s=null;
		h.hide();
		h.unbind('click',_hide)
		 .unbind('mouseout',hide)
		 .unbind('mousemove',move);
		$(document.body).unbind('click',_hide)
						.unbind('mousemove',move);
		if(ct){
			clearTimeout(ct);
			ct=null;
		}
		if(s)h.removeClass(s.extraClass);
		cur=null;
		}catch(e){reportAjaxError('_h:'+e)}
	}
});
