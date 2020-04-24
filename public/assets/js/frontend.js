import $ from 'jquery';
global.$ = global.jQuery = $;
import 'bootstrap';
import 'detect_swipe';

import { disableGoogleAnalytics } from './util/ga';
import { notice } from './util/notice';
import { navigation } from './frontend/app';
import { parallax } from './frontend/parallax';

import { pagination } from './util/pagination';
import { map } from './frontend/map';

import Entry from './frontend/Entry';
import Lightbox from './frontend/Lightbox';
import { registerSW } from './util/sw';
import { lazyload } from './frontend/lazyload';

$(document).ready(function() {
    disableGoogleAnalytics();
    notice();
    navigation();
    parallax();

    let lazyLoadInstance = lazyload();
    Entry.setLazyLoadInstance(lazyLoadInstance);
    Entry.loadNextPrevEntry();
    Entry.lazyLoad();
    Lightbox.lightbox();

    pagination();
    map();

    registerSW();
});
