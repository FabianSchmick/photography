import $ from 'jquery';
global.$ = global.jQuery = $;
import * as bootstrap from 'bootstrap';
window.bootstrap = bootstrap;
import 'detect_swipe';

import { disableGoogleAnalytics } from './util/ga';
import { registerSW } from './util/sw';

import { navigation } from './frontend/navigation';
import { parallax } from './frontend/parallax';
import { filter } from './util/filter';
import { pagination } from './util/pagination';
import { showMoreText } from './util/show-more';
import { tooltip } from './util/tooltip';
import { lazyload } from './frontend/lazyload';

import Post from './frontend/Post';
import { Gdpr } from './frontend/Gdpr';
import Lightbox from './frontend/Lightbox';
import Chart from './frontend/Chart';

$.detectSwipe.threshold = 80; // The number of pixels your finger must move to trigger a swipe event

$(document).ready(function() {
    disableGoogleAnalytics();
    registerSW();

    navigation();
    parallax();
    filter();
    pagination();
    showMoreText();
    tooltip();
    let lazyLoadInstance = lazyload();

    Post.setLazyLoadInstance(lazyLoadInstance);
    Post.initLazyLoad();
    Post.loadNextPrevEntry();
    Lightbox.initLightbox();

    Chart.initChart();

    let gdpr = new Gdpr();
    gdpr.initGdprForMap();
});

$(window).bind('popstate', function() {
    window.location = location.href;
});
