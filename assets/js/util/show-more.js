/**
 * Show fade out text completely
 */
export function showMoreText() {
    let animateCompleted = true;

    $('main').on('click', 'button[data-show-more]', function () {
        let $this = $(this),
            $target = $($this.data('show-more'));

        if (!animateCompleted) {
            return false;
        }

        animateCompleted = false;

        $target.toggleClass('text-fadein');

        $target.animate({
            height: $target.hasClass('text-fadein') ? $target.get(0).scrollHeight : 100
        }, 250, function() {
            if ($target.hasClass('text-fadein')) {
                $this.html($this.data('show-more-open'));
                $target.height('auto');
            } else {
                $this.html($this.data('show-more-collapsed'));
            }

            animateCompleted = true;
        });

        return false;
    });
}
