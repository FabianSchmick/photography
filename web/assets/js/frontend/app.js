import {smoothScroll} from "../util/smooth-scroll";

// Functions for the navigation
export function navigation() {
    if ($("header").offset().top - $("main").offset().top === 0) {
        $(".navbar-default").addClass("on");
    } else {
        $(window).bind("scroll", function() {
            var navHeight = $(window).height() - 520;
            if ($(window).scrollTop() > navHeight) {
                $(".navbar-default").addClass("on");
            } else {
                $(".navbar-default").removeClass("on");
            }
        });
    }

    $("body").scrollspy({
        target: ".navbar-default",
        offset: 80
    });

    $("a.page-scroll").on("click", function() {
        if (location.pathname.replace(/^\//, "") === this.pathname.replace(/^\//, "") && location.hostname === this.hostname) {
            var target = $(this.hash);
            target = target.length ? target : $("[name=" + this.hash.slice(1) +"]");
            if (target.length) {
                smoothScroll(target);

                return false;
            }
        }
    });
}
