$(document).ready(function() {
    navigation();
    search();
    ajaxPageWrapper();
    initSelect2();
    initWysiwyg();
});

// Functions for the navigation
function navigation() {
    $('#sidebarToggle, #sidebarToggleTop').on('click', function () {
        if ($('body').hasClass('sidebar-toggled') && $('#accordionSidebar').hasClass('toggled')) {
            Cookies.set('sidebar-toggled', true);
        } else {
            Cookies.set('sidebar-toggled', false);
        }
    });

    $('aside')
        .on('mouseenter', function () {
            var el = $('aside').get(0);
            if (el.scrollHeight > el.clientHeight || el.scrollWidth > el.clientWidth) {
                $(el).addClass('overflow');
                $('body').addClass('nav-overflow');
            }
        })
        .on('mouseleave', function () {
            $('aside').removeClass('overflow');
            $('body').removeClass('nav-overflow');
        })
        .on('shown.bs.collapse hidden.bs.collapse', '.collapse', function () {
            var el = $('aside').get(0);
            if (el.scrollHeight > el.clientHeight || el.scrollWidth > el.clientWidth) {
                $(el).addClass('overflow');
                $('body').addClass('nav-overflow');
            } else {
                $(el).removeClass('overflow');
                $('body').removeClass('nav-overflow');
            }
        })
    ;
}

// Logic for searching the navigation
function search() {
    $("input[data-sidebar-search]").on("keyup", function () {
        var filter = $(this).val();
        $("ul#accordionSidebar li.searchable").each(function () {
            if ($(this).text().search(new RegExp(filter, "i")) < 0) {
                $(this).hide();
            } else {
                $(this).show();
                if ($(this).parent().hasClass('collapse')) {
                    $(this).parent().collapse("show");
                }
            }
        });

        if (filter === "") {
            $("ul#accordionSidebar .collapse").collapse("hide");
        }
    });
}

// Ajax function for subnavigation
function ajaxPageWrapper() {
    $("ul#accordionSidebar").on("click", "a:not([href=\"#\"])", function() {
        // Ajax loader image
        $("#page-wrapper").html("<div id=\"spinner\" class=\"spinner\"><svg><use xlink:href=\"#spinner-icon\"/></svg></div>");

        var uri = $(this).attr("href"); // Requested uri

        history.pushState(null, "", uri);

        // Add class active
        $("ul#accordionSidebar li").removeClass("active");
        $("ul#accordionSidebar li a[href=\"" + uri + "\"]").closest('li').addClass("active");

        // Get the requested data
        $.get(uri, function(data) {
            var temp = $(data);
            $(temp).find("script").each(function() {
                $(this).remove();
            });
            $("#page-wrapper").replaceWith($(temp).find("#page-wrapper"));
            $("#site-modals").html($(temp).find(".modal"));

            initSelect2();
            initWysiwyg();
        });

        return false;
    });
}

// Initializes select2 fields
function initSelect2() {
    $.fn.select2.defaults.set("theme", "bootstrap4");
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
        dialogsInBody: true,
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
