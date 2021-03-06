import { smoothScroll } from './smooth-scroll';

/**
 * Ajax replace for pagination
 */
export function pagination() {
    const selector = 'ul[data-paginate]';

    $('main').on('click', selector+' a', function (e) {
        let $this = $(this),
            url = $this.attr('href'),
            $pagination = $this.closest(selector),
            replace = $pagination.data('paginate');

        $.get(url, data => {
            let $html = $($.parseHTML(data));

            $(replace).replaceWith($html.find(replace));

            smoothScroll(replace);

            history.pushState(null, '', url);
        });

        e.preventDefault();
    });
}
