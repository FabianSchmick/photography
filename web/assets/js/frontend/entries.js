import 'detect_swipe';

import {addContentToLightbox} from "./lightbox";

// Lazy loads the entries
export function lazyLoad() {
    $(window).on("scroll", function() {
        if (checkAjax && $(window).scrollTop() + $(window).height() > $(document).height() - 600) {
            checkAjax = false;
            $.when(loadEntries()).done(function() {
                checkAjax = true;
            });
        }
    });
}

// Ajax function to get the next entries
export function loadEntries() {
    currentPage++;
    var paginateUrlPage = paginateUrl + "/" + currentPage,
        spinner = $("#spinner");

    $(spinner).show();

    return $.get(paginateUrlPage, function(data) {
        if (!data.length) {
            $(spinner).remove();
            checkAjax = false;
            return false;
        }

        $(data).insertBefore($(spinner));
        $(spinner).hide();
        $("[data-justified=\"true\"]").justifiedGallery("norewind");

        entriesGalleryLength = $("[data-fancybox='entries']").length;

        if ($.fancybox.getInstance()) { // If clicked throw lightbox
            addContentToLightbox(data);
        }
    });
}

// Loads next or prev entry for entry detail page links
export function loadNextPrevEntry() {
    var spinner = "#spinner";
    $(spinner).hide();

    $("section#entry").on("click", "a.prev, a.next", function (e) {
        $(spinner).show();

        loadEntry($(this).attr("href"));

        e.preventDefault();
    }).on("swipeleft", function() {
        $(spinner).show();

        loadEntry($(this).find("a.prev").attr("href"));
    }).on("swiperight", function() {
        $(spinner).show();

        loadEntry($(this).find("a.next").attr("href"));
    });

    function loadEntry(url) {
        $.get(url, function(data) {
            var html = $.parseHTML(data);

            $("section#entry article").replaceWith($(html).find("section#entry article"));

            $("section#entry img").on("load", function() {
                $(spinner).hide();
            });

            history.pushState(null, "", url);
        });
    }
}
