// Offline Service Worker
export function registerSW() {
    if ("serviceWorker" in navigator) {
        $(window).on("load", function() {
            navigator.serviceWorker.register("/sw.js").then(function(registration) {
                // console.log('Service Worker registration was successful with scope: ', registration.scope);
            }, function(err) {
                console.log("ServiceWorker registration failed: ", err);
            });
        });
    }
}
