import $ from 'jquery';
global.$ = global.jQuery = $;
import 'bootstrap';
import 'detect_swipe';

import { notice } from './util/notice';
import { disableGoogleAnalytics } from './util/ga';
import { registerSW } from './util/sw';

import { navigation } from './frontend/navigation';
import { parallax } from './frontend/parallax';
import { pagination } from './util/pagination';
import { showMoreText } from './util/show-more';
import { lazyload } from './frontend/lazyload';

import Entry from './frontend/Entry';
import Lightbox from './frontend/Lightbox';
import Map from './frontend/Map';

$.detectSwipe.threshold = 80; // The number of pixels your finger must move to trigger a swipe event

$(document).ready(function() {
    notice();
    disableGoogleAnalytics();
    registerSW();

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
});
