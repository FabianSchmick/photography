import Entry from './Entry';

/**
 * Class Lightbox primary usage for displaying entries
 */
class Lightbox {
    constructor() {
        this.$lightbox = $('[data-lightbox]');
        this.modal = '#lightbox';
        this.$modal = null;
    }

    /**
     * Lightbox click listener
     */
    initLightbox() {
        this.$lightbox.on('click', '> a', e => {
            let $this = $(e.currentTarget);

            $.get($this.attr('href'), data => {
                let html = $.parseHTML(data);

                $(html).appendTo('body');

                this.initModal();
            });

            e.preventDefault();
        });
    }

    /**
     * Initialize the lightbox modal
     */
    initModal() {
        this.$modal = $(this.modal);

        this.updateIndex();

        this.$modal.modal();

        this.$modal.on('click', '[data-dismiss="modal"]', () => {
            this.dismissModal();
        });

        this.$modal.on('swipeleft', () => {
            this.loadPrev();
        }).on('swiperight', () => {
            this.loadNext();
        });

        this.$modal.on('click', 'a.prev, a.next', e => {
            if ($(e.currentTarget).hasClass('prev')) {
                this.loadPrev();
            } else {
                this.loadNext();
            }

            e.preventDefault();
        });

        $(document).on('keyup', e => {
            if (e.keyCode === 37) {
                this.loadNext();
            } else if (e.keyCode === 39) {
                this.loadPrev();
            } else if (e.keyCode === 27) {
                this.dismissModal();
            }
        });
    }

    /**
     * Dismisses/closes the current active modal
     */
    dismissModal() {
        $(this.modal+', .modal-backdrop').fadeOut(300, () => {
            $('body').removeClass('modal-open').css('padding-right', '');
            $('.modal-backdrop').remove();
            this.$modal.remove();
            history.pushState(null, '', pageUrl);
        });
    }

    /**
     * Load the previous entry
     */
    loadPrev() {
        this.$modal.addClass('loading');

        let $target = $(this.$modal.find('[data-list-target]').data('list-target'));

        // Do not trust the links on the show page,
        // because the lightbox can contain already filtered entries, too.
        if (!$target.next()) {
            Entry.loadEntries();
        }
        this.loadEntry($target.next().attr('href'));
    }

    /**
     * Load the next entry
     */
    loadNext() {
        this.$modal.addClass('loading');

        let $target = $(this.$modal.find('[data-list-target]').data('list-target'));

        // Do not trust the links on the show page,
        // because the lightbox can contain already filtered entries, too.
        this.loadEntry($target.prev().attr('href'));
    }

    /**
     * Load the next or prev entry into the modal
     *
     * @param {string} url The entry url to load
     */
    loadEntry(url) {
        if (!url) {
            this.$modal.removeClass('loading');

            return;
        }

        $.get(url, data => {
            let html = $.parseHTML(data),
                $lightboxBody = this.$modal.find('.lightbox-body');

            $lightboxBody.fadeOut(300, () => {
                $lightboxBody.replaceWith($(html).find('.lightbox-body'));

                $lightboxBody = this.$modal.find('.lightbox-body'); // Get the updated content
                $lightboxBody.hide().fadeIn(300);

                let img = $lightboxBody.find('img');
                if (img) {
                    img.on('load', () => {
                        this.$modal.removeClass('loading');
                    });
                } else {
                    this.$modal.removeClass('loading');
                }
                this.updateIndex();
            });
        });
    }

    /**
     * Update the index of the lightbox -> Page 3 / 10
     */
    updateIndex() {
        let $listEl = this.$lightbox.find('> a'),
            $target = $(this.$modal.find('[data-list-target]').data('list-target')),
            $currentIndex = $listEl.index($target) +1,
            $lastIndex = $listEl.length;

        this.$modal.find('#current-index').html($currentIndex);
        this.$modal.find('#last-index').html($lastIndex);

        history.pushState(null, '', $target.attr('href'));

        // Load more entries if modal comes to end
        if ($currentIndex >= $lastIndex -3) {
            Entry.loadEntries();
        }
    }
}

// Singleton pattern, so all vars have their correct state
export default (new Lightbox());
