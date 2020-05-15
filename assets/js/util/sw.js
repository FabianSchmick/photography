/**
 * Offline Service Worker registration
 */
export function registerSW() {
    /** global: navigator */
    if ('serviceWorker' in navigator) {
        $(window).on('load', () => {
            navigator.serviceWorker.register('/sw.js').then(function(registration) {
                // console.log('Service Worker registration was successful with scope: ', registration.scope);
            }, function(err) {
                console.log('ServiceWorker registration failed: ', err);
            });
        });
    }
}
