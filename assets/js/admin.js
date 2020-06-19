import $ from 'jquery';
global.$ = global.jQuery = $;
import 'bootstrap';

import 'startbootstrap-sb-admin-2/vendor/jquery-easing/jquery.easing';
import 'startbootstrap-sb-admin-2/js/sb-admin-2';

import { navigation, search, ajaxPageWrapper } from './admin/app';
import { initMask, initSelect2, initWysiwyg } from './admin/form';

$(document).ready(function() {
    navigation();
    search();
    ajaxPageWrapper();

    initSelect2();
    initWysiwyg();
    initMask();
});
