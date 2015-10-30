/*!
 * Start Bootstrap - Grayscale Bootstrap Theme (http://startbootstrap.com)
 * Code licensed under the Apache License v2.0.
 * For details, see http://www.apache.org/licenses/LICENSE-2.0.
 */

// jQuery to collapse the navbar on scroll
$(window).scroll(function() {
    if ($(".navbar").offset().top > 50) {
        $(".navbar-fixed-top").addClass("top-nav-collapse");
        $(".nav-icon").attr("src","./imgs/logo_light_small.png");
        $(".nav-brand").removeClass("secondary");
        $(".navbar-right").removeClass("secondary");
    } else {
        $(".navbar-fixed-top").removeClass("top-nav-collapse");
        $(".nav-icon").attr("src","./imgs/logo_dark_small.png");
        $(".nav-brand").addClass("secondary");
        $(".navbar-right").addClass("secondary");
    }
});

// jQuery for page scrolling feature - requires jQuery Easing plugin
$(function() {
    $('a.page-scroll').bind('click', function(event) {
        var $anchor = $(this);
        $('html, body').stop().animate({
            scrollTop: $($anchor.attr('href')).offset().top
        }, 1500, 'easeInOutExpo');
        event.preventDefault();
    });
});
// Closes the Responsive Menu on Menu Item Click
$('.navbar-collapse ul li a').click(function() {
    $('.navbar-toggle:visible').click();
});

