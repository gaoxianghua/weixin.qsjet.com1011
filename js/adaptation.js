var clientPer =$(window).width() / 320;
var oldWidth = $(window).width();
		ZSY(document, window);
		function ZSY(doc, win){
	    	var docEl = doc.documentElement,
	        	resizeEvt = 'orientationchange' in window ? 'orientationchange' : 'resize',
	        	recalc = function () {
	          	var clientWidth = docEl.clientWidth;
	          	if (!clientWidth) return;
	          	docEl.style.fontSize = 20 * (clientWidth / 320) + 'px';
	        };
	
	      	if (!doc.addEventListener) return;
	      	win.addEventListener(resizeEvt, recalc, false);
	      	doc.addEventListener('DOMContentLoaded', recalc, false);
	  	};
	  	window.onresize = function(){
	  		if ($(window).width() != oldWidth) {
	  			location.reload();
	  		}
		};