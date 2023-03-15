import '@popperjs/core';

/**
 * Tooltip for hints
 */
export function tooltip() {
    $('[data-bs-toggle="tooltip"]').tooltip({
        trigger: 'focus hover'
    });
}
