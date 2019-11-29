import '@fancyapps/fancybox';

import {loadEntries} from "./entries";

// Lightbox (fancybox) for the image entries -> load dynamic content https://github.com/fancyapps/fancyBox/issues/257
export function lightbox() {
    $.fancybox.defaults.lang = $("html").attr("lang");
    $.fancybox.defaults.hash = false;
    $.fancybox.defaults.autoFocus = false;
    $.fancybox.defaults.smallBtn = false;
    $.fancybox.defaults.buttons = ["close"];

    $.fancybox.defaults.beforeShow = function() {
        var entries = $("[data-fancybox='entries']");

        history.pushState(null, "", $(entries[this.index]).attr("href"));

        if (paginateUrl && checkAjax && this.index >= entriesGalleryLength - 3) {
            loadEntries();
        }
    };
    $.fancybox.defaults.afterClose = function() {
        history.pushState(null, "", pageUrl);
    };

    $(entriesGallery).fancybox();
}

// Callback because of asynchronous call
export function addContentToLightbox(data) {
    $(data).each(function(index, element) {
        if (typeof $(element).data("src") !== "undefined") {
            $.fancybox.getInstance().addContent({
                type: "ajax",
                src: $(element).data("src")
            });
        }
    });

    $.fancybox.getInstance("updateControls", "force");
}
