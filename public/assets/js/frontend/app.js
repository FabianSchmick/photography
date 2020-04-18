import { smoothScroll } from '../util/smooth-scroll';

/**
 * Functions for the navigation
 */
export function navigation() {
    let $navbar = $('.navbar-default'),
        $window = $(window);

    if ($('#menu').offset().top - $('main').offset().top === 0) {
        $navbar.addClass('on');
    } else {
        addClassOn();
        $window.bind('scroll', function() {
            addClassOn();
        });
    }

    function addClassOn() {
        const navHeight = $window.height() - 520;
        if ($window.scrollTop() > navHeight) {
            $navbar.addClass('on');
        } else {
            $navbar.removeClass('on');
        }
    }

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
