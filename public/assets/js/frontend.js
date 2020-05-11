import $ from 'jquery';
global.$ = global.jQuery = $;
import 'bootstrap';
import 'detect_swipe';

import { disableGoogleAnalytics } from './util/ga';
import { notice } from './util/notice';
import { navigation } from './frontend/app';
import { parallax } from './frontend/parallax';

import { pagination } from './util/pagination';
import { showMoreText } from './util/show-more';

import Entry from './frontend/Entry';
import Lightbox from './frontend/Lightbox';
import Map from './frontend/Map';
import { registerSW } from './util/sw';
import { lazyload } from './frontend/lazyload';

$.detectSwipe.threshold = 80; // The number of pixels your finger must move to trigger a swipe event

$(document).ready(function() {
    disableGoogleAnalytics();
    notice();
    navigation();
    parallax();
    pagination();
    showMoreText();

    let lazyLoadInstance = lazyload();
    Entry.setLazyLoadInstance(lazyLoadInstance);
    Entry.initLazyLoad();
    Entry.loadNextPrevEntry();
    Lightbox.initLightbox();
    Map.initMap();

    registerSW();
});
