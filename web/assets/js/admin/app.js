import Cookies from "js-cookie";
import {initSelect2, initWysiwyg} from "./form";

// Functions for the navigation
export function navigation() {
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
export function search() {
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
export function ajaxPageWrapper() {
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
