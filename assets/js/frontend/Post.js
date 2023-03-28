import { navigationAddClassOn } from './navigation';

/**
 * Class Post with methods for index and show page
 */
class Post {
    constructor() {
        let $paginateContainer = $('[data-infinite-scroll]');

        this.paginateConfig = {
            container: $paginateContainer,
            url: $paginateContainer.data('infinite-scroll') || false,
            page: 1
        };
        this.isLoading = false;
        this.lazyLoadInstance = null;
    }

    /**
     * Lazy loads the posts
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
     * Ajax function to get the next posts
     *
     * @returns {*}
     */
    loadEntries() {
        if (!this.paginateConfig.container.length || this.isLoading) {
            return;
        }

        this.isLoading = true;
        this.paginateConfig.container.addClass('loading');

        this.paginateConfig.page++;
        let paginateUrlPage = this.paginateConfig.url + '/' + this.paginateConfig.page;

        return $.get(paginateUrlPage, data => {
            this.paginateConfig.container.removeClass('loading');

            if (!$.trim(data)) {
                this.isLoading = true;
                return false;
            }

            this.paginateConfig.container.append(data);
            this.lazyLoadInstance.update();
            this.isLoading = false;
        });
    }

    /**
     * Loads next or prev post for post detail page links
     */
    loadNextPrevEntry() {
        let $entry = $('section#post');

        $entry.on('click', 'a.prev, a.next', e => {
            this.loadEntry($(e.currentTarget).attr('href'), $entry);

            e.preventDefault();
        }).on('swipeleft', e => {
            this.loadEntry($(e.currentTarget).find('a.prev').attr('href'), $entry);
        }).on('swiperight', e => {
            this.loadEntry($(e.currentTarget).find('a.next').attr('href'), $entry);
        });

        $(document).on('keyup', e => {
            if (e.keyCode === 37) {
                this.loadEntry($(e.currentTarget).find('a.next').attr('href'), $entry);
            } else if (e.keyCode === 39) {
                this.loadEntry($(e.currentTarget).find('a.prev').attr('href'), $entry);
            }
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
        if (this.isLoading) {
            return;
        }

        $entry.addClass('loading');
        this.isLoading = true;

        return $.get(url, data => {
            let html = $.parseHTML(data),
                $article = $entry.find('article');

            $article.fadeOut(300, () => {
                $article.replaceWith($(html).find('section#post article'));
                $entry.find('article').hide().fadeIn(300);

                $entry.find('img').on('load', () => {
                    $entry.removeClass('loading');
                });
            });

            navigationAddClassOn();

            history.pushState(null, '', url);
            this.isLoading = false;
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
export default (new Post);
