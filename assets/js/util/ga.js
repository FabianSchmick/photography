/**
 * Disable GA script
 */
export function disableGoogleAnalytics() {
    $('.gaOptOut').on('click', e => {
        gaOptout();
        alert('Google Analytics wurde deaktiviert!');

        e.preventDefault();
    });
}
