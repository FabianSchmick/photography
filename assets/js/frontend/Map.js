import * as L from 'leaflet';
import * as omnivore from '@mapbox/leaflet-omnivore/index';
import { GestureHandling } from 'leaflet-gesture-handling';
import 'leaflet-easybutton';
import 'leaflet.markercluster';

import markerPoi from '../../images/layout/icons/map-poi-icon.svg';
import markerStart from '../../images/layout/icons/map-start-icon.svg';
import markerShadow from 'leaflet/dist/images/marker-shadow.png';
import spinnerIcon from '../../images/layout/icons/spinner-icon.svg';

import { smoothScroll } from '../util/smooth-scroll';

/**
 * Class Map with methods for tour page
 */
export class Map {
    constructor() {
        this.selector = '[data-tour-map]';
        this.map = null;
        this.$map = null;
        this.bounds = [];
        this.tracks = [];
        this.cluster = null;
    }

    /**
     * Create a new map with gpx track
     */
    initMap() {
        this.$map = $(this.selector);

        if (!this.$map.length) {
            return;
        }

        L.Map.addInitHook('addHandler', 'gestureHandling', GestureHandling);

        this.map = L.map(this.$map.attr('id'), {
            scrollWheelZoom: false,
            gestureHandling: true
        });

        // stupid hack so that leaflet's images work after going through webpack
        // https://github.com/PaulLeCam/react-leaflet/issues/255#issuecomment-388492108
        delete L.Icon.Default.prototype._getIconUrl;

        L.Icon.Default.mergeOptions({
            iconRetinaUrl: markerPoi,
            iconUrl: markerPoi,
            shadowUrl: markerShadow
        });

        this.addTileLayer();
        if (this.$map.data('gpx')) {
            let gpx = this.$map.data('gpx'),
                length = Object.keys(gpx).length;

            Object.keys(gpx).forEach(id => {
                this.addGpxTrack(gpx[id], id, length > 1);
            });

            let fitBoundsInterval = setInterval(() => {
                // Ensure every track has been loaded
                if (this.tracks.length === length) {
                    this.addCluster();

                    this.tracks.forEach(track => {
                        this.cluster.addLayer(track);
                    });
                    this.bounds = this.cluster.getBounds();

                    this.fitBounds();

                    clearInterval(fitBoundsInterval);
                }
            }, 250);
        }

        if (this.$map.data('fullscreen')) {
            this.toggleMapFullscreen();
        }
    }

    /**
     * Adds the openstreetmap tiles
     */
    addTileLayer() {
        L.tileLayer('https://{s}.tile.openstreetmap.de/tiles/osmde/{z}/{x}/{y}.png', {
            maxZoom: 18,
            attribution: '&copy; <a href="http://www.openstreetmap.org/copyright">OpenStreetMap</a>'
        }).addTo(this.map);
    }

    /**
     * Adds a track layer from a gpx file
     *
     * @param {string}  url
     * @param {string}  id
     * @param {boolean} hideTrack
     */
    addGpxTrack(url, id, hideTrack = false) {
        let track = omnivore.gpx(url, {}, L.geoJson());
        track.on('ready', e => {
            // https://leafletjs.com/reference-1.6.0.html#path-option
            e.target.setStyle({
                color: '#00cdcd',
                opacity: hideTrack ? 0 : 0.6666,
                interactive: false,
                weight: 5
            });

            // Remove all markers and leave only the track LineString
            let layers = Object.values(e.target._layers);
            layers = layers.filter(layer => layer.feature.geometry.type === 'LineString');

            // Get first coordinates for the start icon
            const firstLayer = layers[Object.keys(layers)[0]],
                startCoordinates = firstLayer._latlngs[0],
                startIcon = this.getStartIcon([startCoordinates.lat, startCoordinates.lng], e.target, id, hideTrack);

            layers.push(startIcon);

            e.target._layers = Object.assign({}, layers);

            this.bounds = track.getBounds();

            this.tracks.push(track);
        });
    }

    /**
     * Centers the track to fit at the map
     */
    fitBounds() {
        this.map.fitBounds(this.bounds, { padding: [25, 25] });
    }

    addCluster() {
        // Compute a polygon "center", use your favorite algorithm (centroid, etc.)
        L.Polygon.addInitHook(function () {
            this._latlng = this._bounds.getCenter();
        });

        // Provide getLatLng and setLatLng methods for Leaflet.markercluster to be able to cluster polygons.
        L.Polygon.include({
            getLatLng: function () {
                return this._latlng;
            },
            setLatLng: function () {} // Dummy method.
        });

        this.cluster = L.markerClusterGroup().addTo(this.map);
    }

    /**
     * Returns the start icon for a track
     *
     * @param {array}   coordinates
     * @param {object}  track
     * @param {string}  id
     * @param {boolean} hideTrack
     *
     * @return Marker start icon
     */
    getStartIcon(coordinates, track, id, hideTrack = false) {
        // Start icon should be bigger than rest
        let startIcon = L.icon({
            iconUrl: markerStart,
            shadowUrl: markerShadow,
            iconSize: [38, 120],
            shadowSize: [50, 64],
            iconAnchor: [22, 94],
            shadowAnchor: [15, 65],
            popupAnchor: [-4, -60]
        });

        let marker = L.marker(coordinates, { icon: startIcon });

        marker.bindPopup(`<img src="${spinnerIcon}" alt="loading" width="50px"/>`, {
            className: 'tour-marker-popup',
            minWidth: 275
        });
        marker.on('click', e => {
            let popup = e.target.getPopup(),
                url = this.$map.data('popup-url');

            url = url.replace('__TOUR_ID__', id);
            $.get(url).done(data => {
                popup.setContent(data);
                popup.update();
            });
        });

        if (hideTrack) {
            marker
                .on('popupopen', _ => {
                    track.setStyle({ opacity: 1 });
                })
                .on('popupclose', _ => {
                    track.setStyle({ opacity: 0 });
                });
        }

        return marker;
    }

    /**
     * Resize the map to full view height
     */
    toggleMapFullscreen() {
        L.easyButton({
            states: [{
                stateName: 'expand',
                icon: '<span class="icon icon-sm icon-expand"><svg><use xlink:href="#expand-icon"/></svg></span>',
                title: 'Expand',
                onClick: control => {
                    this.$map
                        .animate({ height: $(window).height() - $('#menu').height() }, () => {
                            this.map.invalidateSize();
                            this.fitBounds();
                        });

                    smoothScroll(this.$map);
                    control.state('compress');
                }
            }, {
                stateName: 'compress',
                icon: '<span class="icon icon-sm icon-compress"><svg><use xlink:href="#compress-icon"/></svg></span>',
                title: 'Compress',
                onClick: control => {
                    this.$map
                        .animate({ height: $(window).width() > 767 ? '400px' : '300px' }, () => {
                            this.map.invalidateSize();
                            this.fitBounds();
                        });

                    smoothScroll(this.$map);
                    control.state('expand');
                }
            }]
        }).addTo(this.map);
    }
}
