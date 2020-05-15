import { smoothScroll } from './smooth-scroll';

/**
 * Ajax replace for pagination
 */
export function pagination() {
    $('main').on('click', 'ul.pagination a', function (e) {
        let $this = $(this),
            url = $this.attr('href'),
            pagination = $this.closest('ul.pagination'),
            replace = $(pagination).data('replace');

        $.get(url, function(data) {
            let html = $.parseHTML(data);

            $(replace).replaceWith($(html).find(replace));

            smoothScroll(replace);

            history.pushState(null, '', url);
        });

        e.preventDefault();
    });
}
