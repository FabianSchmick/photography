$(document).ready(function() {
    search();
    ajaxPageWrapper();
    initSelect2();
    initWysiwyg();
});

// Logic for searching the navigation
function search() {
    jQuery("#search").keyup(function () {
        var filter = jQuery(this).val();
        jQuery("ul#side-menu li").each(function () {
            if (jQuery(this).text().search(new RegExp(filter, "i")) < 0) {
                jQuery(this).hide();
            } else {
                jQuery(this).show();
            }
        });
        jQuery(".subNav").addClass("active").find(".nav").collapse("show");

        if (filter === "") {
            jQuery(".subNav").removeClass("active").find(".nav").collapse("hide");
        }
    });
}

// Ajax function for subnavigation
function ajaxPageWrapper() {
    $(".sidebar-nav ul.nav-second-level").on("click", "a", function() {
        // Ajax loader image
        $("#page-wrapper").html("<div id=\"spinner\" class=\"spinner\"><svg><use xlink:href=\"#spinner-icon\"/></svg></div>");

        var uri = $(this).attr("href"); // Requested uri

        history.pushState(null, "", uri);

        // Add class active
        $("ul.nav a").removeClass("active");
        $("ul.nav a[href=\"" + uri + "\"]").addClass("active");

        // Get the requested data
        $.get(uri, function(data) {
            var temp = $(data);
            $(temp).find("script").each(function() {
                $(this).remove();
            });
            $("#page-wrapper").replaceWith($(temp).find("#page-wrapper"));
            if ($(".nav.navbar-top-links.navbar-right").length) {
                $(".nav.navbar-top-links.navbar-right").replaceWith($(temp).find(".nav.navbar-top-links.navbar-right"));
            } else {
                $(temp).find(".nav.navbar-top-links.navbar-right").insertAfter($("div.navbar-header"));
            }

            initSelect2();
            initWysiwyg();
        });

        return false;
    });
}

// Initializes select2 fields
function initSelect2() {
    $.fn.select2.defaults.set("theme", "bootstrap");
    $.fn.select2.defaults.set("language", $("html").attr("lang"));
    $.fn.select2.defaults.set("placeholder", "");

    $(".select2").select2();

    $(".select2.add").select2({
        tags: true
    });

    $(".clear-select2").on("click", function () {
        $(this).siblings("select.select2").val(null).trigger("change");
    });
}

// Initializes summernote wysiwyg editor
function initWysiwyg() {
    var lang = "";

    if ($("html").attr("lang") === "de") {
        lang = "de-DE";
    }

    $(".wysiwyg").summernote({
        height: 100,
        lang: lang,
        disableDragAndDrop: true,
        linkTargetBlank: false,
        toolbar: [
            // [groupName, [list of button]]
            ["misc", ["undo", "redo"]],
            ["style", ["clear", "bold"]],
            ["insert", ["link"]],
            ["para", ["ul"]],
            ["misc", ["codeview", "help"]]
        ],
        onCreateLink: function(linkUrl) {
            return /^([A-Za-z][A-Za-z0-9+-.]*\:[\/\/]?|\/)/.test(linkUrl) ?
                linkUrl : "http://" + linkUrl;
        }
    });
}
