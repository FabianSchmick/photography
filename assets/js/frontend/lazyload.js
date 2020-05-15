import LazyLoad from 'vanilla-lazyload';

/**
 * Lazyload for images
 *
 * @return ILazyLoad
 */
export function lazyload() {
    return new LazyLoad({
        elements_selector: '.lazy'
    });
}
