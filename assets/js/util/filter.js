/**
 * Ajax filter
 */
export function filter() {
    const selector = 'ul[data-filter]';

    $('main').on('click', selector+' a', function (e) {
        let $this = $(this),
            url = $this.attr('href'),
            $filter = $this.closest(selector),
            replace = $filter.data('filter');

        $.get(url, data => {
            let $html = $($.parseHTML(data));

            $(replace).replaceWith($html.find(replace));
            $filter.replaceWith($html.find(selector));

            history.pushState(null, '', url);
        });

        e.preventDefault();
    });
}
