/**
 * Parallax effect for the hero
 */
export function parallax() {
    let $parallaxElements = $(".parallax");

    $(window).on("scroll", function () {
        window.requestAnimationFrame(function () {

            for (let i = 0; i < $parallaxElements.length; i++) {
                let currentElement = $parallaxElements.eq(i),
                    scrolled = $(window).scrollTop();

                currentElement.css({
                    "transform": "translate3d(0," + scrolled * -0.3 + "px, 0)"
                });
            }
        });
    });
}
