import { smoothScroll } from '../util/smooth-scroll';

/**
 * Functions for the navigation
 */
export function navigation() {
    navigationAddClassOn();
    $(window).bind('scroll', function() {
        navigationAddClassOn();
    });

    $('body').scrollspy({
        target: '.navbar-default',
        offset: 80
    });

    $('a.page-scroll').on('click', function() {
        if (location.pathname.replace(/^\//, '') === this.pathname.replace(/^\//, '') && location.hostname === this.hostname) {
            let target = $(this.hash);
            target = target.length ? target : $('[name=' + this.hash.slice(1) +']');
            if (target.length) {
                smoothScroll(target);

                return false;
            }
        }
    });
}

export function navigationAddClassOn() {
    let mainOffsetTop = $('main').offset().top,
        $navbar = $('.navbar-default'),
        $window = $(window);

    const navHeight = $window.height() - 520;

    if (mainOffsetTop === 0 || ($window.scrollTop() - mainOffsetTop === 0) || $window.scrollTop() > navHeight) {
        $navbar.addClass('on');
    } else {
        $navbar.removeClass('on');
    }
}
