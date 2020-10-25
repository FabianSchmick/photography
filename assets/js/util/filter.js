import { Map } from '../frontend/Map';

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

            // Re-init Map, because there can be new data available
            let map = new Map();
            map.initMap();

            history.pushState(null, '', url);
        });

        e.preventDefault();
    });
}
