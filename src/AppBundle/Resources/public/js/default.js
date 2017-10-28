$(document).ready(function() {
    parallax();
    lazyLoad();
    justify();
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

    $(window).scroll(function() {
        if ($(window).scrollTop() + $(window).height() == $(document).height()) {
            currentPage++;
            var paginateUrlPage = paginateUrl + '/' + currentPage;
            $("#spinner").show();
            $.get(paginateUrlPage, function(data) {
                if (data.length == 0) {
                    $("#spinner").remove();
                    return false;
                }
                $(data).insertBefore($("#spinner"));
                $("#spinner").hide();
                $('#entries').justifiedGallery('norewind');
            });
        }
    });
}

// Justify entries
function justify() {
    $('#entries').justifiedGallery({
        rowHeight : 400,
        maxRowHeight : '175%',
        lastRow : 'nojustify',
        margins : 5,
        imagesAnimationDuration: 1000,
        captions: false
    });
}
