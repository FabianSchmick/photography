/**
 * Disable GA script
 */
export function disableGoogleAnalytics() {
    $(".gaOptOut").on("click", function () {
        gaOptout();
        alert("Google Analytics wurde deaktiviert!");

        return false;
    });
}
