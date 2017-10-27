$(document).ready(function() {
    parallax();
});

function parallax() {
    var parallaxElements = $('.parallax'),
        parallaxQuantity = parallaxElements.length;

    $(window).on('scroll', function () {
        window.requestAnimationFrame(function () {

            for (var i = 0; i < parallaxQuantity; i++) {
                var currentElement = parallaxElements.eq(i);
                var scrolled = $(window).scrollTop();

                currentElement.css({
                    'transform': 'translate3d(0,' + scrolled * -0.3 + 'px, 0)'
                });
            }
        });
    });
}
