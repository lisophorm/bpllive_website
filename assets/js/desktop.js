$(function() {
	$( "#accordion" ).accordion({
		heightStyle: "content"
	});
	
	$('#slideshow').bxSlider({
		auto: true,
	    pager: true,
	    controls: false,
	    touchEnabled: false,
	    speed: 1000
	});
	
});