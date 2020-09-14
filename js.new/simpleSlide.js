/*
Simple jQuery Slideshow
by Jon Raasch (http://jonraasch.com) (http://twitter.com/jonraasch)
*/
function slideSwitch() {
    var $active = $('#slideshow li.active');

    if ($active.length == 0 ) $active = $('#slideshow li:last');

    // use this to pull the divs in the order they appear in the markup
    var $next =  $active.next().length ? $active.next() : $('#slideshow li:first');

    // uncomment below to pull the divs randomly
    // var $sibs  = $active.siblings();
    // var rndNum = Math.floor(Math.random() * $sibs.length );
    // var $next  = $( $sibs[ rndNum ] );


    //$active.addClass('last-active');
	
	$active.hide();
    
	$next.css({'opacity': '0.0'})
        .addClass('active')
        .show()
		.animate({opacity: 1.0}, 1000, function() {
            $active.removeClass('active last-active');
        });
}

jQuery(document).ready(function($) {
	setInterval("slideSwitch()", 6000);    
});