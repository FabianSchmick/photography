import { navigationAddClassOn } from './navigation';

/**
 * Class Entry with methods for index and show page
 */
class Entry {
    constructor() {
        let $paginateContainer = $('[data-infinite-scroll]');

        this.paginateConfig = {
            container: $paginateContainer,
            url: $paginateContainer.data('infinite-scroll') || false,
            page: 1
        };
        this.isLoadingEntries = false;
        this.lazyLoadInstance = null;
    }

    /**
     * Lazy loads the entries
     */
    initLazyLoad() {
        let $window = $(window);

        $window.on('scroll', () => {
            if ($window.scrollTop() + $window.height() > $(document).height() - 600) {
                this.loadEntries();
            }
        });
    }

    /**
     * Ajax function to get the next entries
     *
     * @returns {*}
     */
    loadEntries() {
        if (!this.paginateConfig.container.length || this.isLoadingEntries) {
            return;
        }

        this.isLoadingEntries = true;
        this.paginateConfig.container.addClass('loading');

        this.paginateConfig.page++;
        let paginateUrlPage = this.paginateConfig.url + '/' + this.paginateConfig.page;

        return $.get(paginateUrlPage, data => {
            this.paginateConfig.container.removeClass('loading');

            if (!$.trim(data)) {
                this.isLoadingEntries = true;
                return false;
            }

            this.paginateConfig.container.append(data);
            this.lazyLoadInstance.update();
            this.isLoadingEntries = false;
        });
    }

    /**
     * Loads next or prev entry for entry detail page links
     */
    loadNextPrevEntry() {
        let $entry = $('section#entry');

        $entry.on('click', 'a.prev, a.next', e => {
            $entry.addClass('loading');

            this.loadEntry($(e.currentTarget).attr('href'), $entry);

            e.preventDefault();
        }).on('swipeleft', e => {
            $entry.addClass('loading');

            this.loadEntry($(e.currentTarget).find('a.prev').attr('href'), $entry);
        }).on('swiperight', e => {
            $entry.addClass('loading');

            this.loadEntry($(e.currentTarget).find('a.next').attr('href'), $entry);
        });
    }

    /**
     * Internal for this.loadNextPrevEntry
     *
     * @param {string} url
     * @param {Object} $entry
     *
     * @returns {*}
     */
    loadEntry(url, $entry) {
        return $.get(url, data => {
            let html = $.parseHTML(data),
                $article = $entry.find('article');

            $article.fadeOut(300, () => {
                $article.replaceWith($(html).find('section#entry article'));
                $entry.find('article').hide().fadeIn(300);

                $entry.find('img').on('load', () => {
                    $entry.removeClass('loading');
                });
            });

            navigationAddClassOn();

            history.pushState(null, '', url);
        });
    }

    /**
     * Sets the lazyLoadInstance
     *
     * @param lazyLoadInstance
     */
    setLazyLoadInstance(lazyLoadInstance) {
        this.lazyLoadInstance = lazyLoadInstance;
    }
}

// Singleton pattern, so all vars have their correct state
export default (new Entry);
