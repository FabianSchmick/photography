import '@fancyapps/fancybox';

import Entry from './Entry';

/**
 * Lightbox (fancybox) for the image entries
 * load dynamic content https://github.com/fancyapps/fancyBox/issues/257
 */
export function lightbox() {
    let entriesGallery = '[data-fancybox=\'entries\']';

    $.fancybox.defaults.lang = $('html').attr('lang');
    $.fancybox.defaults.hash = false;
    $.fancybox.defaults.autoFocus = false;
    $.fancybox.defaults.smallBtn = false;
    $.fancybox.defaults.buttons = ['close'];

    $.fancybox.defaults.beforeShow = function() {
        let $entriesGallery = $(entriesGallery);

        history.pushState(null, '', $($entriesGallery[this.index]).attr('href'));

        if (Entry.paginateUrl && !Entry.isLoadingEntries && this.index >= $entriesGallery.length - 3) {
            Entry.loadEntries();
        }
    };
    $.fancybox.defaults.afterClose = function() {
        history.pushState(null, '', pageUrl);
    };

    $(entriesGallery).fancybox();
}

/**
 * Callback because of asynchronous call
 *
 * @param data
 */
export function addContentToLightbox(data) {
    $(data).each((index, el) => {
        let src = $(el).data('src');

        if (typeof src !== 'undefined') {
            $.fancybox.getInstance().addContent({
                type: 'ajax',
                src: src
            });
        }
    });

    $.fancybox.getInstance('updateControls', 'force');
}
