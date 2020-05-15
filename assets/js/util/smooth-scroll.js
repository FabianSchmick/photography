/**
 * Smooth scroll to an element
 */
export function smoothScroll(target) {
    $('html,body').animate({
        scrollTop: $(target).offset().top - $('#menu').height()
    }, 900);
}
