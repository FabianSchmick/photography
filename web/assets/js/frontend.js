// Global vars
var currentPage = 1,
    entriesGallery = $("[data-fancybox='entries']"),
    entriesGalleryLength = entriesGallery.length,
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
    var parallaxElements = $('.parallax');

    $(window).on('scroll', function () {
        window.requestAnimationFrame(function () {

            for (var i = 0; i < parallaxElements.length; i++) {
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
    var paginateUrlPage = paginateUrl + '/' + currentPage,
        spinner = $('#spinner');

    $(spinner).show();

    return $.get(paginateUrlPage, function(data) {
        if (data.length == 0) {
            $(spinner).remove();
            checkAjax = false;
            return false;
        }

        $(data).insertBefore($(spinner));
        $(spinner).hide();
        $('[data-justified="true"]').justifiedGallery('norewind');

        callback(data)
    });
}

// Callback because of asynchronous call
function callback(data) {
    $(data).each(function(index, element) {
        if (typeof $(element).data('src') !== 'undefined') {
            $.fancybox.getInstance().addContent({
                type : 'ajax',
                src  : $(element).data('src')
            });
            entriesGalleryLength++;
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

    $('[data-justified="true"]').justifiedGallery({
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
    $.fancybox.defaults.lang = $('html').attr('lang');
    $.fancybox.defaults.hash = false;
    $.fancybox.defaults.autoFocus = false;
    $.fancybox.defaults.smallBtn = false;
    $.fancybox.defaults.buttons = ["close"];

    $.fancybox.defaults.beforeShow = function(instance){
        var entries = $("[data-fancybox='entries']");

        history.pushState(null, '', $(entries[this.index]).attr('href'));

        if (checkAjax && this.index  >= entriesGalleryLength - 3){
            loadEntries();
        }
    };
    $.fancybox.defaults.afterClose = function(instance){
        history.pushState(null, '', pageUrl);
    };

    $(entriesGallery).fancybox();
}

// Loads next or prev entry for entry detail page links
function loadNextPrevEntry() {
    var spinner = '#spinner';
    $(spinner).hide();

    $('section#entry').on('click', 'a.prev, a.next', function (e) {
        $(spinner).show();

        loadEntry($(this).attr('href'));

        e.preventDefault();
    }).on('swipeleft', function() {
        $(spinner).show();

        loadEntry($(this).find('a.prev').attr('href'));
    }).on('swiperight', function() {
        $(spinner).show();

        loadEntry($(this).find('a.next').attr('href'));
    });

    function loadEntry(url) {
        $.get(url, function(data) {
            var html = $.parseHTML(data);

            $('section#entry article').replaceWith($(html).find('section#entry article'));

            $('section#entry img').on('load', function() {
                $(spinner).hide();
            });

            history.pushState(null, '', url);
        });
    }
}
