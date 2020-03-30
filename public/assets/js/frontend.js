import $ from 'jquery';
global.$ = global.jQuery = $;
import 'bootstrap';

import { disableGoogleAnalytics } from './util/ga';
import { notice } from './util/notice';
import { navigation } from './frontend/app';
import { parallax } from './frontend/parallax';

import { justify } from './frontend/justify';
import { lightbox } from './frontend/lightbox';
import { pagination } from './util/pagination';
import { map } from './frontend/map';

import Entry from './frontend/Entry';
import {registerSW} from "./util/sw";

$(document).ready(function() {
    disableGoogleAnalytics();
    notice();
    navigation();
    parallax();

    Entry.loadNextPrevEntry();
    Entry.lazyLoad();
    lightbox();

    pagination();
    map();

    registerSW();
});
