import $ from 'jquery';
global.$ = global.jQuery = $;
import 'bootstrap';
import 'detect_swipe';

import { notice } from './util/notice';
import { disableGoogleAnalytics } from './util/ga';
import { registerSW } from './util/sw';

import { navigation } from './frontend/navigation';
import { parallax } from './frontend/parallax';
import { filter } from './util/filter';
import { pagination } from './util/pagination';
import { showMoreText } from './util/show-more';
import { tooltip } from './util/tooltip';
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
    filter();
    pagination();
    showMoreText();
    tooltip();
    let lazyLoadInstance = lazyload();

    Entry.setLazyLoadInstance(lazyLoadInstance);
    Entry.initLazyLoad();
    Entry.loadNextPrevEntry();
    Lightbox.initLightbox();
    Map.initMap();
});
