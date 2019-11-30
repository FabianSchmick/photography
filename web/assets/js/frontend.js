const $ = require('jquery');
global.$ = global.jQuery = $;
import 'bootstrap';

import {disableGoogleAnalytics} from "./util/ga";
import {notice} from "./util/notice";
import {navigation} from "./frontend/app";
import {parallax} from "./frontend/parallax";

import {justify} from "./frontend/justify";
import {lightbox} from "./frontend/lightbox";
import {pagination} from "./util/pagination";
import {map} from "./frontend/map";

import Entry from "./frontend/Entry";

// Global vars
// global.currentPage = 1;
// global.entriesGallery = $("[data-fancybox='entries']");
// global.entriesGalleryLength = $(entriesGallery).length;
// global.checkAjax = true;

$(document).ready(function() {
    disableGoogleAnalytics();
    notice();
    navigation();
    parallax();

    Entry.loadNextPrevEntry();
    Entry.lazyLoad();
    justify();
    lightbox();

    pagination();
    map();
});
