$(document).ready(function() {
    parallax();
    lazyLoad();
});

// Parallax effect for the cover
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

// Lazy loads the entries
function lazyLoad() {
    var currentPage = 1;
    $('#content').on('click', '#loadMore', function () {
        currentPage++;
        var paginateUrlPage = paginateUrl + '/' + currentPage;
        $("#spinner").show();
        $.get(paginateUrlPage, function(data) {
            $("#entries").append($(data));
            $("#spinner").hide();
            $("#loadMore").hide();
        });
    });
}
