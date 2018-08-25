// Global vars
var currentPage = 1,
    group = $("[data-fancybox='entries']"),
    groupLength = group.length,
    checkAjax = true;

$(document).ready(function() {
    navigation();
    parallax();
});

// Functions for the navigation
function navigation() {
    if ($('header').offset().top - $('main').offset().top === 0) {
        $('.navbar-default').addClass('on');
    } else {
        $(window).bind('scroll', function() {
            var navHeight = $(window).height() - 520;
            if ($(window).scrollTop() > navHeight) {
                $('.navbar-default').addClass('on');
            } else {
                $('.navbar-default').removeClass('on');
            }
        });
    }

    $('body').scrollspy({
        target: '.navbar-default',
        offset: 80
    });

    $('a.page-scroll').click(function() {
        if (location.pathname.replace(/^\//,'') == this.pathname.replace(/^\//,'') && location.hostname == this.hostname) {
            var target = $(this.hash);
            target = target.length ? target : $('[name=' + this.hash.slice(1) +']');
            if (target.length) {
                $('html,body').animate({
                    scrollTop: target.offset().top - 40
                }, 900);
                return false;
            }
        }
    });
}

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
    $(window).scroll(function() {
        if (checkAjax && $(window).scrollTop() + $(window).height() > $(document).height() - 600) {
            checkAjax = false;
            $.when(loadEntries()).done(function(){
                checkAjax = true;
            });
        }
    });
}

// Ajax function to get the next entries
function loadEntries() {
    currentPage++;
    var paginateUrlPage = paginateUrl + '/' + currentPage;
    $("#spinner").show();

    return $.ajax({
        url: paginateUrlPage,
        type: 'GET',
        dataType: 'html',
        success: function(data) {
            if (data.length == 0) {
                $("#spinner").remove();
                checkAjax = false;
                return false;
            }

            $(data).insertBefore($("#spinner"));
            $("#spinner").hide();
            $('#entries').justifiedGallery('norewind');

            callback(data)
        }
    });
}

// Callback because of asynchronous call
function callback(data) {
    $(data).each(function(index, element) {
        if (typeof $(element).data('src') !== 'undefined') {
            $.fancybox.getInstance('createGroup', {
                type : 'ajax',
                src  : $(element).data('src'),
                opts : {
                    hash : false,
                }
            });
            groupLength++;
        }
    });

    $.fancybox.getInstance('updateControls', 'force');
}

// Justify entries
function justify() {
    var rowHeight = 400;

    if ($(window).width() < 1025) {
        rowHeight = 300;
    } else if ($(window).width() < 768) {
        rowHeight = 200;
    } else if ($(window).width() < 500) {
        rowHeight = 125;
    }

    $(window).on('resize', function() {
        if ($(window).width() < 1025) {
            rowHeight = 300;
        } else if ($(window).width() < 768) {
            rowHeight = 200;
        } else if ($(window).width() < 500) {
            rowHeight = 125;
        }
    });

    $('#entries').justifiedGallery({
        rowHeight : rowHeight,
        maxRowHeight : '175%',
        lastRow : 'nojustify',
        margins : 5,
        imagesAnimationDuration: 1000,
        captions: false
    });
}

// Lightbox (fancybox) for the image entries -> load dynamic content https://github.com/fancyapps/fancyBox/issues/257
function lightbox() {
    var lang = 'en';

    if ($('html').attr('lang') === 'de') {
        lang = $('html').attr('lang');
    }

    $(group).fancybox({
        hash : false,
        autoFocus : false,
        lang : lang,

        beforeShow:  function(instance){
            var entries = $("[data-fancybox='entries']");

            history.pushState(null, '', $(entries[this.index]).attr('href'));

            if (checkAjax && this.index  >= groupLength - 3){
                loadEntries();
            }
        },

        afterClose: function(instance){
            history.pushState(null, '', homeUrl);
        }
    });
}
