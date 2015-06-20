//var $ = jQuery.noConflict();

jQuery(document).ready(function() {
		
var ww = document.body.clientWidth;

jQuery(document).ready(function() {

	jQuery(".menu-main li a").each(function() {
		if (jQuery(this).next().length > 0) {
			jQuery(this).addClass("s_parent");
		};
	})
	
	jQuery(".menu-toggle").click(function(e) {
		e.preventDefault();
		jQuery(this).toggleClass("active");
		jQuery(".menu-main").toggle();
	});
	adjustMenu();
})

jQuery(window).bind('resize orientationchange', function() {
	ww = document.body.clientWidth;
	adjustMenu();
});


var adjustMenu = function() {
	
	 if (ww < 959) {
		jQuery(".menu-toggle").css("display", "none");
		jQuery(".menu-main").show();
		jQuery(".menu-main li").removeClass("hover");
		jQuery(".menu-main li a").unbind('click');
		jQuery(".menu-main li").unbind('mouseenter mouseleave').bind('mouseenter mouseleave', function() {
		 	// must be attached to li so that mouseleave is not triggered when hover over submenu
		 	jQuery(this).toggleClass('hover');
			 $('.navigation-scroll').jScrollPane();
		});
	}
	
	else if (ww >= 959) {
		//jQuery(".menu-toggle").css("display", "inline-block");
		jQuery(".menu-main li").unbind('mouseenter mouseleave');
		jQuery(".menu-main li a.s_parent").unbind('click').bind('click', function(e) {
			// must be attached to anchor element to prevent bubbling
			e.preventDefault();
			jQuery(this).parent("li").toggleClass("hover");
			 $('.navigation-scroll').jScrollPane();
		});
	} 
}

});