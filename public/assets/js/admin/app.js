import Cookies from 'js-cookie';
import { initSelect2, initWysiwyg } from './form';

/**
 * Functions for the navigation
 */
export function navigation() {
    let $body = $('body');

    $('#sidebarToggle, #sidebarToggleTop').on('click', function () {
        if ($body.hasClass('sidebar-toggled') && $('#accordionSidebar').hasClass('toggled')) {
            Cookies.set('sidebar-toggled', true);
        } else {
            Cookies.set('sidebar-toggled', false);
        }
    });

    let $aside = $('#sidebar-nav');

    $aside
        .on('mouseenter', function () {
            let el = $aside.get(0);
            if (el.scrollHeight > el.clientHeight || el.scrollWidth > el.clientWidth) {
                $aside.addClass('overflow');
                $body.addClass('nav-overflow');
            }
        })
        .on('mouseleave', function () {
            $aside.removeClass('overflow');
            $body.removeClass('nav-overflow');
        })
        .on('shown.bs.collapse hidden.bs.collapse', '.collapse', function () {
            let el = $aside.get(0);
            if (el.scrollHeight > el.clientHeight || el.scrollWidth > el.clientWidth) {
                $aside.addClass('overflow');
                $body.addClass('nav-overflow');
            } else {
                $aside.removeClass('overflow');
                $body.removeClass('nav-overflow');
            }
        })
    ;
}

/**
 * Logic for searching the navigation
 */
export function search() {
    let $sidebar = $('ul#accordionSidebar');

    $('input[data-sidebar-search]').on('keyup', function () {
        let filter = $(this).val();

        if (filter === '') {
            $sidebar.find('.collapse').collapse('hide');
            return;
        }

        $sidebar.find('li.searchable').each(function () {
            if ($(this).text().search(new RegExp(filter, 'i')) < 0) {
                $(this).hide();
            } else {
                $(this).show();
                if ($(this).parent().hasClass('collapse')) {
                    $(this).parent().collapse('show');
                }
            }
        });
    });
}

/**
 * Ajax function for subnavigation
 */
export function ajaxPageWrapper() {
    let $sidebar = $('ul#accordionSidebar');

    $sidebar.on('click', 'a:not([href="#"])', function() {
        let $pageWrapper = $('#page-wrapper'),
            uri = $(this).attr('href');

        // Ajax loader image
        $pageWrapper.html('<div id="spinner" class="spinner"><svg><use xlink:href="#spinner-icon"/></svg></div>');

        history.pushState(null, '', uri);

        // Add class active
        $sidebar.find('li').removeClass('active');
        $sidebar.find('li a[href="' + uri + '"]').closest('li').addClass('active');

        // Get the requested data
        $.get(uri, (data) => {
            let $temp = $(data);

            $temp.find('script').each(function() {
                $(this).remove();
            });

            $pageWrapper.replaceWith($temp.find('#page-wrapper'));
            $('#site-modals').html($temp.find('.modal'));

            initSelect2();
            initWysiwyg();
        });

        return false;
    });
}
