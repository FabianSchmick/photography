import { addContentToLightbox } from './lightbox';

/**
 * Class Entry
 */
class Entry {
    constructor() {
        this.paginateUrl = $('[data-paginate]').data('paginate') || false;
        this.paginatePage = 1;
        this.isLoadingEntries = false;
    }

    /**
     * Lazy loads the entries
     */
    lazyLoad() {
        let $window = $(window);

        $window.on('scroll', () => {
            if (!this.isLoadingEntries && $window.scrollTop() + $window.height() > $(document).height() - 600) {
                this.isLoadingEntries = true;
                $.when(this.loadEntries()).done(() => {
                    this.isLoadingEntries = false;
                });
            }
        });
    }

    /**
     * Ajax function to get the next entries
     * @returns {*}
     */
    loadEntries() {
        if (!this.paginateUrl) {
            return;
        }

        this.paginatePage++;
        let paginateUrlPage = this.paginateUrl + '/' + this.paginatePage,
            $spinner = $('#spinner');

        $spinner.show();

        return $.get(paginateUrlPage, data => {
            if (!data.length) {
                $spinner.remove();
                this.isLoadingEntries = true;
                return false;
            }

            $(data).insertBefore($spinner);
            $spinner.hide();
            $('[data-justified="true"]').justifiedGallery('norewind');

            if ($.fancybox.getInstance()) { // If clicked throw lightbox
                addContentToLightbox(data);
            }
        });
    }

    /**
     * Loads next or prev entry for entry detail page links
     */
    loadNextPrevEntry() {
        let $spinner = $('#spinner'),
            $entry = $('section#entry');

        $spinner.hide();

        $entry.on('click', 'a.prev, a.next', e => {
            $spinner.show();

            this.loadEntry($(e.currentTarget).attr('href'), $entry, $spinner);

            e.preventDefault();
        }).on('swipeleft', e => {
            $spinner.show();

            this.loadEntry($(e.currentTarget).find('a.prev').attr('href'), $entry, $spinner);
        }).on('swiperight', e => {
            $spinner.show();

            this.loadEntry($(e.currentTarget).find('a.next').attr('href'), $entry, $spinner);
        });
    }

    /**
     * Internal for this.loadNextPrevEntry
     *
     * @param {string} url
     * @param {Object} $entry
     * @param {Object} $spinner
     */
    loadEntry(url, $entry, $spinner) {
        $.get(url, data => {
            let html = $.parseHTML(data);

            $entry.find('article').replaceWith($(html).find('section#entry article'));

            $entry.find('img').on('load', () => {
                $spinner.hide();
            });

            history.pushState(null, '', url);
        });
    }
}

export default (new Entry);
