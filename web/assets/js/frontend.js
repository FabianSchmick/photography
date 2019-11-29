const $ = require('jquery');
global.$ = global.jQuery = $;
import 'bootstrap';

import {disableGoogleAnalytics} from "./util/ga";
import {notice} from "./util/notice";
import {navigation} from "./frontend/app";
import {parallax} from "./frontend/parallax";

import {lazyLoad, loadNextPrevEntry} from "./frontend/entries";
import {justify} from "./frontend/justify";
import {lightbox} from "./frontend/lightbox";
import {pagination} from "./util/pagination";
import {map} from "./frontend/map";

// Global vars
global.currentPage = 1;
global.entriesGallery = $("[data-fancybox='entries']");
global.entriesGalleryLength = $(entriesGallery).length;
global.checkAjax = true;

$(document).ready(function() {
    disableGoogleAnalytics();
    notice();
    navigation();
    parallax();

    loadNextPrevEntry();
    lazyLoad();
    justify();
    lightbox();

    pagination();
    map();
});
