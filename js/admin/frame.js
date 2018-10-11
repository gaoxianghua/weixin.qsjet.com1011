/**
 * 
 */

$(document).ready(function() {
	$('.white_container_outer').css({'min-height':$(window).height()-202});
//	$('.white_container_outer').css({'width':$96%});
	$('.white_container').css({'min-height':$(window).height()-202});
//	$('.white_container').css({'width':96%});
//	$('.white_container').css({'width':$(window).width()-52});

});

$(window).resize(function() {
	$('.white_container_outer').css({'min-height':$(window).height()-202});
//	$('.white_container_outer').css({'width':96%});
	$('.white_container').css({'min-height':$(window).height()-202});
//	$('.white_container').css({'width':96%});
//	$('.white_container').css({'width':$(window).width()-52});
	});