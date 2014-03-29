$(function() {

	$('header hgroup').hide();
	
	$('header .access').click(function(){
		$('header hgroup').slideToggle();
	});
	
	$(window).scroll(function(){
		$('header hgroup').slideUp();
	});

	$('#badges').bxSlider({
		slideWidth: 250,
	    minSlides: 5,
	    maxSlides: 5,
	    slideMargin: 10,
	    pager: false,
	    nextSelector: '#slider-next',
	    prevSelector: '#slider-prev',
	    nextText: '<i class="fa fa-caret-right"></i>',
	    prevText: '<i class="fa fa-caret-left"></i>',
	    preventDefaultSwipeY: true
	});
	
	$('#slideshow').bxSlider({
		auto: true,
	    pager: true,
	    controls: false,
	    touchEnabled: false,
	    speed: 1000
	});

});