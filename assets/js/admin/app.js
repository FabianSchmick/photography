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
}

/**
 * Logic for searching the navigation
 */
export function search() {
    $('form[data-search] .btn').on('click', function () {
        filter($(this).closest('form').find('input[name="query"]'));
    });
    $('input[name="query"]').on('keyup', function () {
        filter(this);
    });

    function filter(input) {
        let $sidebar = $('ul#accordionSidebar'),
            filter = $(input).val();

        if (filter === '') {
            $sidebar.find('.collapse').removeClass('show');
            $sidebar.find('.searchable').show();
            return;
        }

        $sidebar.find('li.searchable').each(function () {
            let $this = $(this);

            if ($this.text().search(new RegExp(filter, 'i')) < 0) {
                $this.hide();
            } else {
                $this.show();
                if ($this.parent().hasClass('collapse')) {
                    $this.parent().addClass('show');
                }
            }
        });
    }
}

/**
 * Ajax function for subnavigation
 */
export function ajaxPageWrapper() {
    let $sidebar = $('ul#accordionSidebar');

    $sidebar.on('click', 'a:not([href="#"]):not(.sidebar-brand)', function() {
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
