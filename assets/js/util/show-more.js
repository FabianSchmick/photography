/**
 * Show faded out text completely
 */
export function showMoreText() {
    let isAnimationCompleted = true;

    $('main').on('click', 'button[data-show-more]', function () {
        let $this = $(this),
            $target = $($this.data('show-more'));

        if (!isAnimationCompleted) {
            return false;
        }

        isAnimationCompleted = false;

        $target.toggleClass('text-fadein');

        $target.animate({
            height: $target.hasClass('text-fadein') ? $target.get(0).scrollHeight : 100
        }, 250, () => {
            if ($target.hasClass('text-fadein')) {
                $this.html($this.data('show-more-open'));
                $target.height('auto');
            } else {
                $this.html($this.data('show-more-collapsed'));
            }

            isAnimationCompleted = true;
        });

        return false;
    });
}
